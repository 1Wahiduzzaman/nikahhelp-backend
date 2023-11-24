<?php


namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Http\Requests\TicketSubmissionRequest;
use App\Models\PictureServerToken;
use App\Models\ProcessTicket;
use App\Models\TicketSubmission;
use App\Models\User;
use App\Models\ProfileLog;
use App\Models\VerifyUser;
use App\Mail\VerifyMail as VerifyEmail;
use App\Mail\VerifyTwoFactorCode;
use App\Repositories\RepresentativeInformationRepository;
use App\Repositories\TicketRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository as EmailVerifyRepository;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use App\Transformers\CandidateTransformer;
use App\Repositories\CandidateRepository;
use App\Repositories\ProfileLogRepository;
use DB;
use Symfony\Component\HttpFoundation\Response as FResponse;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \App\Domain;
use App\Models\CandidateInformation;
use App\Models\TeamMember;
use App\Models\TeamMemberInvitation;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordReset;
use App\Models\RepresentativeInformation;
use App\Models\Team;
use App\Models\TeamConnection;
use App\Transformers\RepresentativeTransformer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UserService extends ApiBaseService
{

    use CrudTrait;

    protected \App\Repositories\UserRepository $userRepository;

    /**
     * @var EmailVerifyRepository
     */
    protected EmailVerifyRepository $emailVerifyRepository;

    /**
     * @var RepresentativeRepository
     */
    protected RepresentativeRepository $representativeRepository;

    protected \App\Transformers\CandidateTransformer $candidateTransformer;

    protected \App\Repositories\CandidateRepository $candidateRepository;
    protected \App\Repositories\ProfileLogRepository $profileLogRepository;

    protected \App\Transformers\RepresentativeTransformer $repTransformer;

    protected \App\Repositories\TicketRepository $ticketRepository;

    protected \App\Domain $domain;

    /**
     * UserService constructor.
     *
     * @param UserRepository $UserRepository
     * @param EmailVerifyRepository $emailVerifyRepository
     * @param RepresentativeRepository $representativeRepository
     * @param CandidateTransformer $candidateTransformer
     * @param CandidateRepository $candidateRepository
     * @param ProfileLogRepository $profileLogRepository
     * @param Domain $domain
     */
    public function __construct(
        UserRepository $UserRepository,
        EmailVerifyRepository $emailVerifyRepository,
        RepresentativeRepository $representativeRepository,
        CandidateTransformer $candidateTransformer,
        CandidateRepository $candidateRepository,
        RepresentativeTransformer $repTransformer,
        ProfileLogRepository $profileLogRepository,
        TicketRepository $ticketRepository,
        Domain $domain
    )
    {
        $this->userRepository = $UserRepository;
        $this->emailVerifyRepository = $emailVerifyRepository;
        $this->representativeRepository = $representativeRepository;
        $this->candidateTransformer = $candidateTransformer;
        $this->candidateRepository = $candidateRepository;
        $this->profileLogRepository = $profileLogRepository;
        $this->ticketRepository = $ticketRepository;
        $this->domain = $domain;
        $this->repTransformer = $repTransformer;
    }

    /**
     * this function use for user registration
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register($request)
    {
        try {
            $data = array();
            /* Data set for user table */
            $inputData['email'] = $request->get('email');
            $inputData['password'] = Hash::make($request->get('password'));
            $inputData['full_name'] = $request->get('first_name') . ' '. $request->get('last_name');
            $inputData['account_type'] = $request->get('account_type');
            $inputData['form_type'] = $request->get('form_type') ??  1;
            $user = $this->userRepository->save($inputData);

            /* Data set for user information table */
            $registerUser['user_id'] = $user->id;
            $registerUser['email'] = $request->get('email');
            $registerUser['first_name'] = $request->get('first_name');
            $registerUser['last_name'] = $request->get('last_name');
            $registerUser['screen_name'] = $request->get('screen_name');
            $registerUser['data_input_status'] = 0;

            if($request->get('account_type') == 1){ // 1 for candidate
                $userInfoResponse = $this->candidateRepository->save($registerUser);
            }elseif ($request->get('account_type') == 2){ // 2 for representative
                $userInfoResponse = $this->representativeRepository->save($registerUser);
            }

            
            if ($user) {
                $token = JWTAuth::fromUser($user);
                $encryptedToken = Crypt::encryptString($token);
                VerifyUser::create([
                    'user_id' => $user->id,
                    'token' => $encryptedToken,
                ]);

                try{
                    Mail::to($user->email)->send(new VerifyEmail($user, $this->domain->domain));
                } catch(Exception $e) {
                    $deleteCandidate = $this->candidateRepository->findOneByProperties(['user_id' => $user->id]);
                    $deleteCandidate->delete();
                    $deleteUser = $this->userRepository->findOneByProperties(['id' => $user->id]);
                    $deleteUser->delete();
                    return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
                }
                
                $user->resetLoginCount();

                self::authenticate($request);

                $user->resetLoginCount();


                $user['data_input_status'] = $userInfoResponse->data_input_status;
                $data['token'] = self::TokenFormater($token);
                $data['user'] = $user;
                

                return $this->sendSuccessResponse($data, 'User registration successfully completed', [], FResponse::HTTP_CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * This function use for user login by email and password
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $data = array();
        try {

            $userInfo = User::where('email', $request->input('email'))->first();

            /* Check the user is exist */
            if (empty($userInfo)) {
                return $this->sendErrorResponse(
                    'You are not a registered you should registration first ',
                    [],
                    403
                );
            }
            /* Check the user is not delete */
            if ($userInfo->status == 0) {
                return $this->sendErrorResponse(
                    'Your account has been deleted ( ' . $userInfo->email . ' ), please contact us so we can assist you.',
                    [],
                    403
                );
            } elseif($userInfo->status == 9){
                return $this->sendErrorResponse(
                    'Your account has been Suspended ( ' . $userInfo->email . ' ), please contact us so we can assist you.',
                    [],
                    403
                );
            }

            /* attempt login */
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->sendErrorResponse(
                    'Invalid credentials',
                    ['detail' => 'Ensure that the email and password included in the request are correct'],
                    403
                );
            } else {
                /*
                     handle tow factor authentication
                */

                // increase logn count
                $userInfo->incrementLoginCount();

                // check if user has 10 login count
                // if yes, generate new 2fa code
                if($userInfo->login_count > 1 && $userInfo->two_factor_code == null) {
                    
                    $userInfo->generateTwoFactorCode();
                    // $user->notify(new TwoFactorCode());
                    try{
                        Mail::to($userInfo->email)->send(new VerifyTwoFactorCode($userInfo, $this->domain->domain));
                        return $this->sendSuccessResponse('A verification code was sent to your email.', [], FResponse::HTTP_BAD_REQUEST);
                    } catch(Exception $e) {
                        return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
                    }
                } else if($userInfo->login_count > 1 && $userInfo->two_factor_code != null) {
                    if($userInfo->two_factor_expires_at > now()) {
                        return $this->sendSuccessResponse('A verification code was sent to your email.', [], FResponse::HTTP_BAD_REQUEST);
                    } else {
                        $userInfo->generateTwoFactorCode();
                        try{
                            Mail::to($userInfo->email)->send(new VerifyTwoFactorCode($userInfo, $this->domain->domain));
                            return $this->sendSuccessResponse('A verification code was sent to your email.', [], FResponse::HTTP_BAD_REQUEST);
                        } catch(Exception $e) {
                            return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
                        }
                    }
                }

                /* Load data input status */
                if($userInfo->account_type == 1){
                    $userInfo['per_permanent_country_name'] = $userInfo->getCandidate->getPermanentCountry ? $userInfo->getCandidate->getPermanentCountry->name : "";
                    $userInfo['data_input_status'] = $userInfo->getCandidate->data_input_status;
                    $userInfo['per_main_image_url'] = $userInfo->getCandidate->per_main_image_url;
                }elseif ($userInfo->account_type == 2){
                    $userInfo['per_permanent_country_name'] = $userInfo->getCandidate->getPermanentCountry ? $userInfo->getCandidate->getPermanentCountry->name : "";
                    $userInfo['data_input_status'] = $userInfo->getRepresentative->data_input_status;
                    $userInfo['per_main_image_url'] = $userInfo->getRepresentative->per_main_image_url;
                }
                $userInfo['last_login'] = now();
                $data['token'] = self::TokenFormater($token);
                $data['user'] = $userInfo;

                return $this->sendSuccessResponse($data, 'Login successfully');
            }
        } catch (JWTException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function authenticatedWithImageService(User $user): bool
    {
        try {
            new ImageServerService($user, 'login');
           $token = ImageServerService::getTokenFromDatabase($user);
           if (!isset($token)) {
               new ImageServerService($user, 'register');
               $token = ImageServerService::getTokenFromDatabase($user);
           }

            return isset($token);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }

        return false;
    }

    public function logout($token)
    {
        if (empty($token)) {
            return $this->sendErrorResponse('Authorization token is empty', [], HttpStatusCode::VALIDATION_ERROR);
        }

        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->sendSuccessResponse([], 'User has been logged out');
        } catch (JWTException $exception) {
            return $this->sendErrorResponse('Sorry, user cannot be logged out', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**matrimonial-assist
     * THis function use for getting user information use token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            } else {
                $candidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $user["id"]
                ]);
                if (!$candidate) {
                    $candidateInformation = array();
                } else {
                    $candidateInformation = $this->candidateTransformer->transform($candidate);
                }
                $representativeInformation = $this->representativeRepository->findBy(['user_id' => $user["id"]]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => $e->getStatusCode(),
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => $e->getStatusCode(),
                'message' => 'Token is Invalid',
                'error' => ['details' => 'Token is Invalid']
            ], $e->getStatusCode());

            return response()->json(['status' => 'Token is Invalid']);
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => $e->getStatusCode(),
                'message' => 'Token is Expired',
                'error' => ['details' => 'Token is Expired']
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => $e->getStatusCode(),
                'message' => 'Authorization Token not found',
                'error' => ['details' => 'Authorization Token not found']
            ], $e->getStatusCode());
        }

        $data = array();
        $data['user'] = $user;
        $data['candidate_information'] = $candidateInformation;
        $data['representative_information'] = $representativeInformation;

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [], HttpStatusCode::SUCCESS);

    }

    /**
     * This function use for getting user information by user id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile($request)
    {
        $teamid = null;
        $teamTableId = null;
        $userid = self::getUserId();
        try {

            $user = $this->userRepository->findOneByProperties([
                "id" => $request->user_id
            ]);

            if (!$user) {
                return $this->sendErrorResponse('User not found.', [], HttpStatusCode::NOT_FOUND);
            } else {
                $candidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $request->user_id
                ]);
                if (!$candidate) {
                    $candidateInformation = array();

                } else {

                    $status['is_short_listed'] = null;
                    $status['is_block_listed'] = null;
                    $status['is_teamListed'] = null;
                    $status['is_connect'] = null;
                    
                    // handle blocked users
                    $loggedInUser = Auth::user();
                    $blockedByThisCandidate = $candidate->blockList->pluck('user_id')->toArray();
                    if(in_array($loggedInUser->id, $blockedByThisCandidate)) {
                        return $this->sendErrorResponse('You are blocked by this user');
                    }


                    if (empty($candidate)) {
                        $candidate = $this->representativeRepository->findOneByProperties([
                            'user_id' => $request->user_id
                        ]);
                    }

                    if($loggedInUser){
                        if($loggedInUser->getCandidate()->exists()){
                            $loggedInCandidate = $loggedInUser->getCandidate;
                        } else {
                            $loggedInCandidate = $loggedInUser->getRepresentative;
                        }
                        $status['is_block_listed'] = in_array($candidate->user_id,$loggedInCandidate->blockList->pluck('user_id')->toArray());
                        $status['is_short_listed'] = in_array($candidate->user_id,$loggedInCandidate->shortList->pluck('user_id')->toArray());

                        $teamTableId = $candidate->candidate_team ? [
                            'id' => $candidate->candidate_team->id,
                            'team_name' => $candidate->candidate_team->name,
                            'member' => $candidate->candidate_team->member_count,
                            'created_by' => User::find($candidate->candidate_team->created_by),
                            'created_at' => $candidate->candidate_team->created_at,
                            'logo' => $candidate->candidate_team->logo
                        ] : '';
                        $teamid = $candidate->candidate_team->team_id ?? null;
                        $status['is_teamListed'] = null;
                        $status['is_connect'] =  null;;

                        try {
                            $userActive = $this->getRightUser();
                            $fromTeamId =  $userActive->active_team->id ?? null;
                            $connection = TeamConnection::where('from_team_id', $fromTeamId)->where('to_team_id', $candidate->candidate_team->id ?? null)->get();

                            if (count($connection) < 1) {
                                $connection = TeamConnection::where('from_team_id', $candidate->candidate_team->id ?? null)->where('to_team_id', $fromTeamId)->get();
                            }


                        } catch (\Exception $th) {
                           return $this->sendErrorResponse($th->getMessage(), [], HttpStatusCode::FORBIDDEN);
                        }

                        $activeTeam = $loggedInCandidate->active_team;
                        if($activeTeam){
                            $status['is_teamListed'] = in_array($candidate->user_id,$activeTeam->teamListedUser->pluck('id')->toArray());
                            $status['is_connect'] = $connection;
                        }

                    }


                    if (is_a($candidate, 'RepresentativeInformation')) {
                        $candidateInformation = $this->repTransformer->transform($candidate);
                        $candidateInformation['essential'] = $this->repTransformer->transformPersonalEssential($candidate)['essential'];
                        $candidateInformation['status'] = $status;
                        $candidateInformation['more_about'] = $this->repTransformer->transformPersonalMoreAbout($candidate)['more_about'];

                    } else {
                        $candidateInformation = $this->candidateTransformer->transform($candidate);
                        $candidateInformation['essential'] = $this->candidateTransformer->transformPersonalEssential($candidate)['essential'];
                        $candidateInformation['status'] = $status;
                        $candidateInformation['more_about'] = $this->candidateTransformer->transformPersonalMoreAbout($candidate)['more_about'];

                    }
                }

                $representativeInformation = $this->representativeRepository->findBy(['user_id' => $request->user_id]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => $e->getCode(),
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], $e->getCode());

        }

        $data = array();
        $data['user'] = $user;
        $data['candidate_information'] = $candidateInformation;
        $data['representative_information'] = $representativeInformation;
        $data['team_id'] = $teamid;
        $data['team'] = $teamTableId;

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [], HttpStatusCode::SUCCESS);

    }

    protected function getRightUser()
    {
        $fromCandidate = CandidateInformation::where('user_id', self::getUserId())->get();

        return count($fromCandidate) > 0 ? CandidateInformation::where('user_id', self::getUserId())->first() : RepresentativeInformation::where('user_id', self::getUserId())->first();
    }

    public function findUserInfo($request)
    {
        try {

            $user = $this->userRepository->findOneByProperties([
                "email" => $request->email
            ]);
            if (!$user) {
                return $this->sendErrorResponse('User not found.', [], HttpStatusCode::NOT_FOUND);
            } else {
                $candidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $user->id
                ]);
                if (!$candidate) {
                    $candidateInformation = array();
                } else {
                    $candidateInformation = $this->candidateTransformer->transform($candidate);
                }

                //$representativeInformation = $this->representativeRepository->findBy(['user_id' => $user->id]);

                $invitation_data = TeamMemberInvitation::
                where('email', $request->email)
                ->where('team_id', $request->team_id)
                ->first();
                $joined_data = TeamMember::where('user_id', $user->id)->where('team_id', $request->team_id)->first();
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => $e->getStatusCode(),
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], $e->getStatusCode());

        }
        $data = array();
        $data['user'] = $user;
        $data['candidate_information'] = $candidateInformation;
        //$data['representative_information'] = $representativeInformation;
        $status = 0;
        if(!$invitation_data && !$joined_data) {
            $status = 0;
        } elseif($invitation_data && !$joined_data) {
            $status = 1;
        } elseif(!$invitation_data && $joined_data) {
            $status = 2;
        }
        $data['invitation_status'] = $status;

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [], HttpStatusCode::SUCCESS);

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTokenRefresh()
    {
        try {
            $token = auth('api')->refresh();
            $data['token'] = self::TokenFormater($token);
            $data['user'] = auth('api')->user();
            return $this->sendSuccessResponse($data, 'Token regenerate successfully');
        } catch (Exception $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::INTERNAL_ERROR,
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], HttpStatusCode::INTERNAL_ERROR);

        } catch (Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::INTERNAL_ERROR,
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function TokenFormater($token)
    {
        $expireTime = auth('api')->factory()->getTTL() * 60;
        $dateTime = Carbon::now()->addSeconds($expireTime);
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $dateTime,
        ];
        return $data;
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function emailVerify(Request $request, $token)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($token);
            $request->headers->set('Authorization', 'Bearer '.$decrypted);
            
        } catch (DecryptException $e) {
            return $this->sendErrorResponse('Invalid Token', ['detail' => 'Token not found in Database'],
                HttpStatusCode::BAD_REQUEST
            );
        }

        try {
            return DB::transaction(function () use($request, $token, $decrypted){
                if ($user = JWTAuth::parseToken()->authenticate()) {

                    // check if user is already verified
                    if ($user->is_verified == 1) {
                        // do something and return
                        return response()->json(['message' => 'User is already verified']);
                    }

                    // check if token is valid
                    $verifyUser = VerifyUser::where('user_id', $user->id)->first();

                    if ($verifyUser->exists()) {
                        $dbTimeStamp = strtotime($verifyUser->created_at);
                        // check if token is expired
                        if (time() - $dbTimeStamp > 15 * 60) {
                            
                            if($user->account_type == 1) {
                                $candidate = $this->candidateRepository->findOneByProperties([
                                    'user_id' => $user->id
                                ]);
                                if($candidate) {
                                    $candidate->delete();
                                }
                            } else if($user->account_type == 2) {
                                
                                $representative = $this->representativeRepository->findOneByProperties([
                                    'user_id' => $user->id
                                ]);
                                if($representative) {
                                    $representative->forceDelete();
                                }
                            }
                            $verifyUser->delete();
                            $user->delete();
                            return $this->sendErrorResponse('Token expired', ['detail' => 'Token expired'],
                                HttpStatusCode::BAD_REQUEST
                            );
                        } else {
                            $user->is_verified = 1;
                            $user->email_verified_at = Carbon::now()->toDateTimeString();
                            $user->save();
                            $verifyUser->delete();
        //                    $this->sendAuthToImageServer($user);
                            // $user->token = $decrypted;
                            return $this->sendSuccessResponse($user, 'User verification successfully completed',[],200);
                        }
                    }
                    
                }
                return $this->sendErrorResponse('Invalid Token', ['detail' => 'Token not found in Database'],
                    HttpStatusCode::BAD_REQUEST
                );
            });
        } catch (Exception $e) {
            throw $e;
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::NOT_FOUND,
                'message' => $e->getMessage(),
                'error' => ['details' => $e->getMessage()]
            ], HttpStatusCode::NOT_FOUND);
        }
    }

    /** 
     * @param $request
     * @return JsonResponse
     */
    public function tokenVerifyOrResend(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $twoFactorCode = $request->twoFACode;
        $isResend = $request->isResend;

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->sendErrorResponse(
                'Invalid credentials',
                ['detail' => 'Ensure that the email and password included in the request are correct'],
                403
            );
        } else {
            try {
                $userInfo = User::where('email', $request->input('email'))->first();

                /* Check the user is exist */
                if (empty($userInfo)) {
                    return $this->sendErrorResponse(
                        'You are not a registered you should registration first ',
                        [],
                        403
                    );
                }
                /* Check the user is not delete */
                if ($userInfo->status == 0) {
                    return $this->sendErrorResponse(
                        'Your account has been deleted ( ' . $userInfo->email . ' ), please contact us so we can assist you.',
                        [],
                        403
                    );
                } elseif($userInfo->status == 9){
                    return $this->sendErrorResponse(
                        'Your account has been Suspended ( ' . $userInfo->email . ' ), please contact us so we can assist you.',
                        [],
                        403
                    );
                }

                // handle if resend code 
                if($isResend) {
                    if($userInfo->two_factor_code == null || $userInfo->two_factor_expires_at < now()) {
                        $userInfo->generateTwoFactorCode();
                    }
                    try{
                        Mail::to($userInfo->email)->send(new VerifyTwoFactorCode($userInfo, $this->domain->domain));
                        // Mail::to($userInfo->email)->send(new VerifyTwoFactorCode($userInfo, $this->domain->domain));

                        return $this->sendSuccessResponse([], 'A verification code was sent to your email.');
                    } catch(Exception $e) {
                        return $this->sendErrorResponse($e);
                    }
                }

                // handle if code is not match
                if($userInfo->two_factor_code != $twoFactorCode) {
                    return $this->sendErrorResponse('Your verification code is invalid, please check your email inbox, spam or junk folder for latest verification code.', [], FResponse::HTTP_BAD_REQUEST);
                } else if($userInfo->two_factor_expires_at < now()) {
                    return $this->sendErrorResponse('Your verification code is expired, please check your email inbox, spam or junk folder for latest verification code or request ', [], FResponse::HTTP_BAD_REQUEST);
                }

                // handle if code is match
                if($userInfo->two_factor_code == $twoFactorCode) {
                    $userInfo->resetTwoFactorCode();
                    $userInfo->resetLoginCount();
                    $data = array();

                    /* Load data input status */
                    if($userInfo->account_type == 1){
                        $userInfo['per_permanent_country_name'] = $userInfo->getCandidate->getPermanentCountry->name;
                        $userInfo['data_input_status'] = $userInfo->getCandidate->data_input_status;
                        $userInfo['per_main_image_url'] = $userInfo->getCandidate->per_main_image_url;
                        // $userInfo['per_main_image_url'] = $userInfo->getCandidate->per_main_image_url;
                    }elseif ($userInfo->account_type == 2){
                        $userInfo['per_permanent_country_name'] = $userInfo->getRepresentative->per_permanent_country;
                        $userInfo['data_input_status'] = $userInfo->getRepresentative->data_input_status;
                        $userInfo['per_main_image_url'] = $userInfo->getRepresentative->per_main_image_url;
                        // $userInfo['per_main_image_url'] = $userInfo->getRepresentative->per_main_image_url;
                    }

                    $userInfo['last_login'] = now();
                    $data['token'] = self::TokenFormater($token);
                    $data['user'] = $userInfo;

                    return $this->sendSuccessResponse($data, 'Logged in successfully!');
                }
            } catch (JWTException $exception) {
                return $this->sendErrorResponse($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function switchAccount($request)
    {
        //check password
        //screen form should not be directed

        $userId = self::getUserId();
        if (!empty($request['account_type'])) {
            $usr_info = $this->userRepository->findOne($userId);
            $usr_info->account_type = $request['account_type'];
            if ($usr_info->save()) {
                return $this->sendSuccessResponse($usr_info, 'User account switch successfully');
            } else {
                return $this->sendErrorResponse('Invalid Token', ['detail' => 'Token not found in Database'],
                    HttpStatusCode::BAD_REQUEST
                );
            }
        } else {
            return $this->sendErrorResponse('Data validation error', [], HttpStatusCode::VALIDATION_ERROR);
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function changePassword($request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hashedPassword = $user->password;

        if (Hash::check($request['oldpassword'], $hashedPassword)) {
            if (!Hash::check($request['newpassword'], $hashedPassword)) {

                $user->password = Hash::make($request['newpassword']);
                $user->save();
                return $this->sendSuccessResponse($user, 'password updated successfully');

            } else {
                return $this->sendErrorResponse('new password can not be the old password!', [],
                    HttpStatusCode::BAD_REQUEST
                );
            }

        } else {
            return $this->sendErrorResponse('old password doesnt matched', [],
                HttpStatusCode::BAD_REQUEST
            );
        }

    }

    /**
     * @return JsonResponse
     */
    public function deleteUserAccount(Request $request)
    {
       $validPass = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);



        if ($validPass->fails()) {
            return $this->sendErrorResponse('Sorry you can not access', [], HttpStatusCode::FORBIDDEN);
        }

        // $hashPassword = Hash::make($request->password);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return $this->sendErrorResponse('User Not Found', [], HttpStatusCode::NOT_FOUND);
        }

        $check = Hash::check($request->password, $user->password);
        if (!$check) {
            return $this->sendErrorResponse('Sorry you are not allowed to access', ['data' => false], HttpStatusCode::FORBIDDEN);
        }

        try {
            $user->status = 0;


            if ($user->save()) {
                JWTAuth::invalidate(JWTAuth::getToken());
                return $this->sendSuccessResponse([], 'Your account has been delete');
            } else {
                return $this->sendErrorResponse('Sorry, something went wrong please try again', [], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (JWTException $exception) {
            return $this->sendErrorResponse('Sorry, user cannot be logged out', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */

    public function storeProfileLogs($request)
    {
        if (!empty($request['user_id'])) {
            $userId = $request['user_id'];
            $location = self::getUserLocation($userId);
            $sote = new ProfileLog();
            $sote->visitor_id = self::getUserId();
            $sote->user_id = $userId;
            $sote->country = $location['country'];
            $sote->city = $location['city'];
            $sote->date = Carbon::now(); //->format('Y-m-d');
            $sote->save();
            if ($sote->save()) {
                return $this->sendSuccessResponse($sote, 'Profile visiting log store Successfully');
            }
        } else {
            return $this->sendErrorResponse('validation error ', [],
                HttpStatusCode::VALIDATION_ERROR
            );
        }

    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return parent::getUserId(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $visitorId
     * @return string[]
     */
    public function getUserLocation($visitorId)
    {
        $userInfo = User::find($visitorId);
        $info = ['country' => '', 'city' => ''];
        if ($userInfo->account_type == 1) {
            $info['country'] = $userInfo->getCandidate->per_current_residence_country ?? null;
            $info['city'] = $userInfo->getCandidate->per_current_residence_city ?? null;
        } elseif ($userInfo->account_type == 2) {

            $info['country'] = $userInfo->getRepresentative->per_current_residence_country ?? null;
            $info['city'] = $userInfo->getRepresentative->per_current_residence_city ?? null;
        }
        return $info;
    }

    public function getProfileLogs(){
        try {
            $userId = self::getUserId();
            $data = DB::table("profile_logs")
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %h:%i') as categories"), DB::raw("COUNT(*) as data"))
                ->groupBy('categories')
                ->get();

            return $this->sendSuccessResponse($data, 'Profile visiting log store Successfully');
        }catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    //Admin Panel Raz
    public function getUserList() {
        try{
            $data = User::all();
            return $this->sendSuccessResponse($data, 'User List Fetched successfully');
        } catch(Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    public function formTypeStatus(Request $request)
    {
        $userId = self::getUserId();
        $user = $this->userRepository->findOneByProperties([
            'id' => $userId
        ]);
        $formType = (int)$request->get('form_type');
        $user->update(['form_type'=> $formType]);

        return $this->sendSuccessResponse($user,'Form type status update successfully');
    }

    public function passwordExpiryCheck($token)
    {
        $rule = ['token' => 'required|string|max:255'];
        $isValid = Validator::make(['token' => $token], $rule);

       if ($isValid->fails()) {
            return $this->sendErrorResponse('Please reset password', 'Token not valid');
       }

        $tokenExistInDB = PasswordReset::where('token', $token)->first();
        if($tokenExistInDB){
            $time = now()->subMinute(15);

            if($tokenExistInDB->created_at <= $time) {
                $tokenExistInDB->delete();
                return $this->sendSuccessResponse(['accepted' => false], 'Token expired');
            }
            return $this->sendSuccessResponse(['accepted' => true], 'Token accepted');
        } else {
            return $this->sendSuccessResponse(['accepted' => false], 'Token not valid');
        }
    }


    public function ticketSubmission(TicketSubmissionRequest $request)
    {

        $user_id = $this->getUserId();


        try {


            $ticket = new TicketSubmission([
                'issue_type' => $request->issue_type,
                'issue' => $request->issue,
                'user_id' => $user_id,
                'user' => $request->user,
            ]);

            $ticket->save();
            return $this->sendSuccessResponse(['ticket' => $ticket], 'successfully submittedTicket', HttpStatusCode::SUCCESS);

        } catch (Exception $exception) {
           return $this->sendErrorResponse($exception, $exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }
    }

    public function issueScreenShot(\App\Http\Requests\TicketSumbissionScreenshot $request)
    {
        $user_id = $this->getUserId();

        try {
            if ($request->hasFile('screen_shot')) {
                $screenshot_path = $this->uploadImageThrowGuzzle([
                    'screen_shot' => $request->file('screen_shot') ]);
            } else {
                throw new Exception('no file');
            }


           $issueTicket =  TicketSubmission::where('user_id', $user_id)->first();

           $issueTicket->screen_shot_path = $screenshot_path;

           $issueTicket->save();

           return $this->sendSuccessResponse(['not success'], 'screenshot updated');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), 'failed');
        }
    }

    public function allTickets(Request $request)
    {
        try {


            $allTickets = TicketSubmission::with('processTicket')->get();

            return $this->sendSuccessResponse($allTickets, 'All tickets');
        } catch (Exception $exception) {
            return $this->sendErrorResponse('error', $exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }
    }

    public function userTickets(Request $request, int $id)
    {

        try {
            $userTickets = $this->ticketRepository->findByProperties([
                'user_id' => $id
            ]);

            return $this->sendSuccessResponse($userTickets, 'successful');
        } catch (Exception $exception) {
           return $this->sendErrorResponse('problem with server');
        }
    }

    public function saveRequest(Request $request)
    {
        try {
           $validRequest =  Validator::make($request->all(), [
                'message' => 'required|string',
                'ticket_id' => 'required|int',
                'user' => 'json'
            ]);

           if ($validRequest->fails()) {
               throw new  Exception($validRequest->errors());
           }

            $ticketProcess = new ProcessTicket([
                'message' => $request->input('message'),
                'ticket_id' => $request->input('ticket_id'),
                'status' => 1,
                'user' => $request->input('user')
            ]);

           $ticketProcess->save();

           return $this->sendSuccessResponse($ticketProcess, 'ticket processed', HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception, $exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }
    }

    public function ticketMessages(Request $request, $id)
    {
        try {

            $ticketProcessMessages = ProcessTicket::where('ticket_id', $id)->get();

            return $this->sendSuccessResponse($ticketProcessMessages, 'Success', HttpStatusCode::SUCCESS);

        } catch (Exception $exception)
        {
            return $this->sendErrorResponse($exception, $exception->getMessage());
        }
    }

    public function deleteTicketMessage(Request $request, $id)
    {
        try {
            $ticketProcessMessages = ProcessTicket::find($id);

            $ticketProcessMessages->delete();

            return $this->sendSuccessResponse($ticketProcessMessages, 'Success', HttpStatusCode::SUCCESS);

        } catch (Exception $exception)
        {
            return $this->sendErrorResponse($exception, $exception->getMessage());
        }
    }

    public function resolveTicket(Request $request)
    {
        try {
           $valid = Validator::make($request->all(), [
                'ticket_id' => 'required|number'
            ]);

            $resolveIssue = TicketSubmission::find($request->input('ticket_id'));
            $resolveIssue->resolve = 1;
            $resolveIssue->save();
            return $this->sendSuccessResponse($resolveIssue, 'Pending to resolve', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), 'Failed to resolve', HttpStatusCode::INTERNAL_ERROR);
        }

    }

    public function sendMessage(Request $request)
    {
        try {
           $message = new ProcessTicket([
                'message' => $request->input('message'),
                'ticket_id' => $request->input('ticket_id'),
                'user' => $request->input('user'),
                'status' => 1
            ]);

           $message->save();

           return $this->sendSuccessResponse($message, 'Message sent', [],HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse('failed', 'failed', HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * @param User $user
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authenticateImageServer(User $user): array
    {
        $email = $user->email;
        $password = $user->password;

        $client = new \GuzzleHttp\Client();

        $res = $client->request('POST', config('chobi.chobi') . '/api/v1/register', [
            'form_params' => [
                'email' => $email,
                'password' => $password
            ]
        ]);
        return array($email, $password, $client, $res);
    }

    /**
     * @param $res
     * @return bool
     */
    public function isSuccessFullRequest($res): bool
    {
        return json_decode($res->getBody()->getContents())->status == 'FAIL';
    }

    /**
     * @param User $user
     * @param $res
     * @return void
     */
    public function savePictureServerToken(User $user, $res): void
    {
        PictureServerToken::create([
            'user_id' => $user->id,
            'token' => json_decode($res->getBody()->getContents())->data->token->access_token
        ]);
    }
}
