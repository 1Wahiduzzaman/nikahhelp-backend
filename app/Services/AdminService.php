<?php


namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\TicketSubmission;
use App\Repositories\CandidateRepository;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use App\Transformers\CandidateTransformer;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Mail;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RepresentativeRepository
     */
    protected $representativeRepository;

    /**
     * @var CandidateTransformer
     */
    protected $candidateTransformer;

    /**
     * @var CandidateRepository
     */
    protected $candidateRepository;


    public function __construct(
        UserRepository $UserRepository,
        RepresentativeRepository $representativeRepository,
        CandidateTransformer $candidateTransformer,
        CandidateRepository $candidateRepository
    ) {
        $this->userRepository = $UserRepository;

        $this->representativeRepository = $representativeRepository;
        $this->candidateTransformer = $candidateTransformer;
        $this->candidateRepository = $candidateRepository;
    }


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $data = array();
        try {
            $adminInfo = Admin::where('email', $request->input('email'))->first();

            /* Check the user is existed */
            if (empty($adminInfo)) {
                return $this->sendErrorResponse(
                    'You are not a registered you should registration first ',
                    [],
                    403
                );
            }
            if (!$token = Auth::guard('admin')->attempt($credentials)) {
                return $this->sendErrorResponse(
                    'Invalid credentials',
                    ['detail' => 'Ensure that the email and password included in the request are correct'],
                    403
                );
            } else {
                $data['token'] = self::TokenFormater($token);
                $data['permissions'] = $this->getPermissions(Auth::guard('admin')->user());
                $data['user'] = Auth::guard('admin')->user();

                return $this->sendSuccessResponse($data, 'Login successfully');
            }
        } catch (JWTException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function TokenFormater($token)
    {
        $expireTime = auth('api')->factory()->getTTL() * 60;
        $dateTime = Carbon::now()->addSeconds($expireTime);
        $data = [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $dateTime,
        ];

        return $data;
    }

    /**
     * @param $request
     */
    public function userList($request)
    {
        $result = $this->userRepository->getModel()->newQuery();

        $data = $result->get();
        $data = Category::paginate(request()->all());

        return Response::json($data, 200);

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [1], HttpStatusCode::SUCCESS);
    }


    /**
     * This function use for getting user information by user id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile($request)
    {
        try {
            $user = $this->userRepository->findOneByProperties([
                "id" => $request->user_id,
            ]);
            if ( ! $user) {
                return $this->sendErrorResponse('User not found.', [], HttpStatusCode::NOT_FOUND);
            } else {
                $candidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $request->user_id,
                ]);
                if ( ! $candidate) {
                    $candidateInformation = array();
                } else {
                    $candidateInformation = $this->candidateTransformer->transform($candidate);
                }

                $representativeInformation = $this->representativeRepository->findBy(['user_id' => $request->user_id]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status'      => 'FAIL',
                'status_code' => $e->getStatusCode(),
                'message'     => $e->getMessage(),
                'error'       => ['details' => $e->getMessage()],
            ], $e->getStatusCode());
        }

        $data = array();
        $data['user'] = $user;
        $data['candidate_information'] = $candidateInformation;
        $data['representative_information'] = $representativeInformation;

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [], HttpStatusCode::SUCCESS);
    }

    private function getPermissions(Admin $admin)
    {
        $permissionList = Permission::with('roles')->get();
        $adminPermissions = [];
        foreach ($permissionList as $permission) {
           $adminPermissions[$permission->slug] = $admin->hasRole($permission->roles);
        }

        return $adminPermissions;
    }

    public function resolveTicket(Request $request)
    {
        try {
          $ticket =  TicketSubmission::find($request->input('ticket_id'));
          $ticket->delete();

          return $this->sendSuccessResponse($ticket, 'Resolved', 0, HttpStatusCode::SUCCESS);
        } catch(Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), 'failed to resolve');
        }
    }


}
