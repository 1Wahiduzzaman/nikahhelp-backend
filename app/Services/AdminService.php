<?php


namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Models\User;
use App\Models\VerifyUser;
use App\Mail\VerifyMail as VerifyEmail;
use App\Repositories\RepresentativeInformationRepository;
use Carbon\Carbon;
use Exception;
use Mail;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository as EmailVerifyRepository;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use App\Transformers\CandidateTransformer;
use App\Repositories\CandidateRepository;
use DB;
use Symfony\Component\HttpFoundation\Response as FResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    )
    {
        $this->userRepository = $UserRepository;

        $this->representativeRepository = $representativeRepository;
        $this->candidateTransformer = $candidateTransformer;
        $this->candidateRepository = $candidateRepository;
    }

    /**
     * @param $request
     */
    public function userList($request)
    {
        $result=$this->userRepository->getModel()->newQuery();

        $data=$result->get();
        $data = Category::paginate(request()->all());
        return Response::json($data, 200);

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [1], HttpStatusCode::SUCCESS);
    }


    /**
     * This function use for getting user information by user id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile($request)
    {
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
                    $candidateInformation = $this->candidateTransformer->transform($candidate);
                }

                $representativeInformation = $this->representativeRepository->findBy(['user_id' => $request->user_id]);
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
        $data['representative_information'] = $representativeInformation;

        return $this->sendSuccessResponse($data, 'Data retrieved successfully', [], HttpStatusCode::SUCCESS);

    }


}
