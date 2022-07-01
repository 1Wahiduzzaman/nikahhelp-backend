<?php

namespace App\Http\Controllers\API;

use App\Models\ShortListedCandidate;
use App\Repositories\ShortListedCandidateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Gate;
use Response;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\UserReportResource;
use App\Mail\UserDeletedMail;
use App\Mail\UserRejectedMail;
use App\Mail\UserSuspendedMail;
use App\Models\CandidateImage;
use App\Models\CandidateInformation;
use App\Models\RejectedNote;
use App\Models\TeamConnection;
use App\Services\AdminService;
use App\Services\SubscriptionService;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\CandidateRepository;
use App\Transformers\CandidateTransformer;

use Illuminate\Support\Facades\Auth;

use App\Repositories\CandidateImageRepository;
use App\Repositories\CountryRepository;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use App\Transformers\RepresentativeTransformer;
use Exception;
use Illuminate\Support\Facades\Mail;

/**
 * Class ShortListedCandidateController
 * @package App\Http\Controllers\API\V1
 */
class AdminDashboardController extends AppBaseController
{
    /**
     * @var  ShortListedCandidateRepository
     */
    private $shortListedCandidateRepository;

    /**
     * @var  AdminService
     */
    private $adminService;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    public function __construct(
        ShortListedCandidateRepository $shortListedCandidateRepo,
        AdminService $adminService,
        UserRepository $UserRepository,
        SubscriptionService $subscriptionService,

        //Raz
        CandidateRepository $candidateRepository,
        CandidateImageRepository $imageRepository,
        CandidateTransformer $candidateTransformer,
        //BlockListService $blockListService,
        RepresentativeRepository $representativeRepository,
        CountryRepository $countryRepository,
        RepresentativeTransformer $representativeTransformer
    )
    {
        $this->shortListedCandidateRepository = $shortListedCandidateRepo;
        $this->adminService = $adminService;
        $this->userRepository = $UserRepository;
        $this->subscriptionService = $subscriptionService;

        //Raz
        $this->candidateRepository = $candidateRepository;
        $this->imageRepository = $imageRepository;
        $this->candidateTransformer = $candidateTransformer;
        //$this->blockListService = $blockListService;
        $this->representativeRepository = $representativeRepository;
        $this->representativeTransformer = $representativeTransformer;
    }

    /**
     * Display a listing of the ShortListedCandidate.
     * GET|HEAD /shortListedCandidates
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        if(!Gate::allows('DASHBOARD_ASSESS')){
            return $this->sendUnauthorizedResponse();
        }

        $personalList = ShortListedCandidate::whereNull('shortlisted_for')->count();
        $personalListTeam = ShortListedCandidate::whereNotNull('shortlisted_for')->count();
        $result['short_list']['total'] = $personalList + $personalListTeam;
        $result['short_list']['personal'] = $personalList;
        $result['short_list']['team'] = $personalListTeam;
        $result['profile_view'] = 0;
        return $this->sendResponse($result, 'Dashboard information patch successfully');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function userReport(Request $request)
    // {

    //     $search = [];
    //     $page = $request['page'] ?: 1;
    //     $parpage = $request['parpage'] ?: 10;
    //     $userList = $this->userRepository->getModel()->newQuery();
    //     if ($request->has('account_type')) {
    //         $search['account_type'] = $request->input('account_type');
    //     }

    //     if ($request->has('full_name')) {
    //         $search['full_name'] = $request->input('full_name');
    //     }
    //     if ($page) {
    //         $skip = $parpage * ($page - 1);
    //         $queryData = $this->userRepository->all($search, $skip, $parpage);
    //     } else {
    //         $queryData = $this->userRepository->all($search, 0, $parpage);
    //     }
    //     //dd($queryData);
    //     $PaginationCalculation = $userList->paginate($parpage);
    //     $team_info = UserReportResource::collection($queryData);
    //     $result['result'] = $team_info;
    //     $result['pagination'] = self::pagination($PaginationCalculation);

    //     return $this->sendResponse($result, 'Data retrieved successfully');

    // }

    public function count_can_rep() {
        $candidate_count = User::where('status', 1)
        ->where('account_type', 1)
        ->count();
        $rep_count = User::where('status', 1)
        ->where('account_type', 2)
        ->count();

        $data =  [
            'no_of_candidate' => $candidate_count,
            'no_of_rep' => $rep_count,
        ];
        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function userReport(Request $request)
    {
        if(!Gate::allows('GET_ACTIVE_USER')){
            return $this->sendUnauthorizedResponse();
        }

        $data = $this->getActiveUserData($request);
        return $this->sendResponse($data, 'Data retrieved successfully');
    }

    private function getActiveUserData(Request $request)
    {
        $keyword = @$request->input('keyword');
        $account_type = @$request->input('account_type');
        if (!empty($request->keyword) && !empty($request->account_type)) {
            $data = User::where('account_type', $account_type)
            ->where(function($q)use ($keyword){
                $q->orWhere('full_name', 'LIKE','%'.$keyword.'%');
                $q->orWhere('email', 'LIKE','%'.$keyword.'%');
                $q->orWhere('id', $keyword);
            })
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                 $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        elseif (!empty($request->account_type) && empty($request->keyword)) {
            $data = User::where('account_type', $account_type)
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                 $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        } elseif(!empty($request->keyword) && empty($request->account_type)) {
            $data = User::where(function($q)use ($keyword){
                $q->orWhere('full_name', 'LIKE','%'.$keyword.'%');
                $q->orWhere('email', 'LIKE','%'.$keyword.'%');
                $q->orWhere('id', $keyword);
            })
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                 $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        else {
            $data = User::with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                 $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        return $data;

    }

    private function getUserData(Request $request, $status)
    {
        $keyword = @$request->input('keyword');
        $account_type = @$request->input('account_type');
        $status = $status;
        if (!empty($request->keyword) && !empty($request->account_type)) {
            $data = User::where('status', $status)
            ->where('account_type', $account_type)
            ->where(function($q)use ($keyword){
                $q->orWhere('full_name', 'LIKE','%'.$keyword.'%');
                $q->orWhere('email', 'LIKE','%'.$keyword.'%');
                $q->orWhere('id', $keyword);
            })
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        elseif (!empty($request->account_type) && empty($request->keyword)) {
            $data = User::where('status', $status)
            ->where('account_type', $account_type)
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        } elseif(!empty($request->keyword) && empty($request->account_type)) {
            $data = User::where('status', $status)
            ->where(function($q)use ($keyword){
                $q->orWhere('full_name', 'LIKE','%'.$keyword.'%');
                $q->orWhere('email', 'LIKE','%'.$keyword.'%');
                $q->orWhere('id', $keyword);
            })
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        else {
            $data = User::where('status', $status)
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id', 'is_uplaoded_doc']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        return $data;

    }

    /**
     * @param Request $request
     * @return string
     */
    public function pendingUserList(Request $request)
    {
        if(!Gate::allows('GET_PENDING_USER')){
            return $this->sendUnauthorizedResponse();
        }
        $data = $this->getUserData($request, 2);
        return $this->sendResponse($data, 'Data retrieved successfully');
    }

//     public function pendingUserList(Request $request)
//     {
//         $parpage = 10;
//         $page = 1;
//         if ($request->has('parpage')): $parpage = $request->input('parpage'); endif;
//         if ($request->has('page')): $page = $request->input('page'); endif;

//         $search = $this->userRepository->getModel()->newQuery();
//         if ($page) {
//             $skip = $parpage * ($page - 1);
//             $userList = $search->where('status','=','2')->limit($parpage)->offset($skip)->get();
//         } else {
//             $userList = $search->where('status','=','2')->limit($parpage)->offset(0)->get();
//         }
// //        $userList=User::where('status','=',0)->paginate($parpage);
//         $formatted_data = UserReportResource::collection($userList);
//         return $this->sendResponse($formatted_data, 'Data retrieved successfully');

//     }
    public function approvedUserList(Request $request)
    {
        if(!Gate::allows('GET_APPROVED_USER')){
            return $this->sendUnauthorizedResponse();
        }

       $data =  $this->getUserData($request, 5);
       return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function verifiedUserList(Request $request)
    {
        if(!Gate::allows('GET_VERIFIED_USER')){
            return $this->sendUnauthorizedResponse();
        }

       $data =  $this->getUserData($request, 3);
       return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function rejectedUserList(Request $request)
    {
        if(!Gate::allows('GET_REJECTED_USER')){
            return $this->sendUnauthorizedResponse();
        }

       $data =  $this->getUserData($request, 4);
       return $this->sendResponse($data, 'Data retrieved successfully');
    }

//     public function verifiedUserList(Request $request)
//     {
//         $parpage = 10;
//         $page = 1;
//         if ($request->has('parpage')): $parpage = $request->input('parpage'); endif;
//         if ($request->has('page')): $page = $request->input('page'); endif;

//         $search = $this->userRepository->getModel()->newQuery();
//         if ($page) {
//             $skip = $parpage * ($page - 1);
//             $userList = $search->where('status','=','3')->limit($parpage)->offset($skip)->get();
//         } else {
//             $userList = $search->where('status','=','3')->limit($parpage)->offset(0)->get();
//         }
// //        $userList=User::where('status','=',0)->paginate($parpage);
//         $formatted_data = UserReportResource::collection($userList);
//         return $this->sendResponse($formatted_data, 'Data retrieved successfully');

//     }

//     public function rejectedUserList(Request $request)
//     {
//         $parpage = 10;
//         $page = 1;
//         if ($request->has('parpage')): $parpage = $request->input('parpage'); endif;
//         if ($request->has('page')): $page = $request->input('page'); endif;

//         $search = $this->userRepository->getModel()->newQuery();
//         if ($page) {
//             $skip = $parpage * ($page - 1);
//             $userList = $search->where('status','=','4')->limit($parpage)->offset($skip)->get();
//         } else {
//             $userList = $search->where('status','=','4')->limit($parpage)->offset(0)->get();
//         }
// //        $userList=User::where('status','=',0)->paginate($parpage);
//         $formatted_data = UserReportResource::collection($userList);
//         return $this->sendResponse($formatted_data, 'Data retrieved successfully');

//     }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyRejectUser(Request $request)
    {

        if(!Gate::allows('CAN_ACCESS_ADMIN')){
            return $this->sendUnauthorizedResponse();
        }

        $status = [
            'suspend' => 9,
            'approved' => 5,
            'verified' => 3,
            'rejected' => 4,
            'completed' => 2,
            'incompleted' => 1,
            'deleted' => 0,
        ];
        $ver_rej = $status[$request->status];
        if (!empty($request->id)) {
            $userId = $request->id;
        } else {
            return $this->sendError('User Id is required ', FResponse::HTTP_BAD_REQUEST);
        }
        $userInfo = $this->userRepository->findOneByProperties(['id' => $userId]);
        if (!$userInfo) {
            throw (new ModelNotFoundException)->setModel(get_class($this->userRepository->getModel()), $userId);
        }
        $userInfo->status = strval($ver_rej);
        if ($userInfo->save()) {
            if($ver_rej == '4') {
                $rj = new RejectedNote();
                $rj->user_id = $userId;
                $rj->note = $request->note;
                $rj->save();
            }
            if($ver_rej == '9') {
                if($userInfo->email) {
                    try{
                        Mail::to($userInfo->email)->send(new UserSuspendedMail($userInfo));
                    } catch(Exception $e) {
                        return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
                    }
                }
            }
            //
            if($ver_rej == '4') {
                if($userInfo->email) {
                    try{
                        Mail::to($userInfo->email)->send(new UserRejectedMail($userInfo));
                    } catch(Exception $e) {
                        return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
                    }
                }
            }
            if($ver_rej == '0') {
                if($userInfo->email) {
                    try{
                        Mail::to($userInfo->email)->send(new UserDeletedMail($userInfo));
                    } catch(Exception $e) {
                        return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
                    }
                }
            }

            return $this->sendSuccess($userInfo, 'User '. $request->status.' successfully', [], FResponse::HTTP_OK);
        } else {
            return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
        }

    }

    //Raz

    // Representative details
    public function RepresentativeUserInfo($id = null) {

        if(!Gate::allows('GET_PARTICULAR_REPRESENTATIVE')){
            return $this->sendUnauthorizedResponse();
        }

        if (!empty($id)) {
            $userId = $id;
        } else {
            return $this->sendError('User Id is required ', FResponse::HTTP_BAD_REQUEST);
        }

        try {
            $representativeInformation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$representativeInformation) {
                throw (new ModelNotFoundException)->setModel(get_class($this->representativeRepository->getModel()), $userId);
            }
            $data = $this->representativeTransformer->profileInfo($representativeInformation);
            if ($data) {
                 //Raz
                $rejected_notes = RejectedNote::where('user_id', $userId)->get();
                $res = array_merge(
                    $data,
                    [
                        'rejected_notes' => $rejected_notes
                    ],
                    [
                        'user' => User::where('id', $id)->first()
                    ]
                );
                return $this->sendSuccess($res, 'User info loaded successfully', [], FResponse::HTTP_OK);
            } else {
                return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
            }


        }catch (Exception $exception){
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    // candidate details

    public function CandidateUserInfo($id = null) {

        if(!Gate::allows('GET_PARTICULAR_CANDIDATE')){
            return $this->sendUnauthorizedResponse();
        }

        if (!empty($id)) {
            $userId = $id;
        } else {
            return $this->sendError('User Id is required ', FResponse::HTTP_BAD_REQUEST);
        }
        $candidate = $this->candidateRepository->findOneByProperties([
            'user_id' => $userId
        ]);
        if (!$candidate) {
            throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
        }
        $images = $this->imageRepository->findBy(['user_id'=>$userId]);
        $candidate_info = $this->candidateTransformer->transform($candidate);
        $candidate_info['essential'] = $this->candidateTransformer->transformPersonalEssential($candidate)['essential'];
        $candidate_image = $this->candidateTransformer->candidateOtherImage($images,CandidateImage::getPermissionStatus($userId));

        //Raz
        $rejected_notes = RejectedNote::where('user_id', $userId)->get();

        /* Find Team Connection Status (We Decline or They Decline )*/

        $candidate_details = array_merge(
            $candidate_info,
            [
                'essential' => $this->candidateTransformer->transformPersonal($candidate)['essential'],
            ],
            [
                'general' => $this->candidateTransformer->transformPersonal($candidate)['general'],
            ],
            [
                'contact' => $this->candidateTransformer->transformPersonal($candidate)['contact'],
            ],
            [
                'more_about' =>  $this->candidateTransformer->transformPersonal($candidate)['more_about'],
            ],
            [

                'other_images' => $candidate->other_images
            ],
            [
                'rejected_notes' => $rejected_notes
            ],
            [
                'user' => User::where('id', $id)->first()
            ]
        );

        if ($candidate_details) {
            return $this->sendSuccess($candidate_details, 'User info loaded successfully', [], FResponse::HTTP_OK);
        } else {
            return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
        }
    }

    public function UserInfo($id = null) {

        if(!Gate::allows('GET_PARTICULAR_USER')){
            return $this->sendUnauthorizedResponse();
        }

        if (!empty($id)) {
            $userId = $id;
        } else {
            return $this->sendError('User Id is required ', FResponse::HTTP_BAD_REQUEST);
        }
        $userInfo = User::
        with([
            'candidate_info' => function($r1){
                $r1->with([
                    'candidateEducationLevel',
                    'getCountryOFBirth',
                    'getCurrentResidenceCountry',
                    'getPermanentCountry',
                    'getPetnarCountryOFBirth',
                    'activeTeams'
                ]);
            }
        ])
        ->with(
            [
                'representative_info',
                'candidate_image',
                'rejected_notes'
                ]
            )->where('id', $userId)->first();
        if (!$userInfo) {
            throw (new ModelNotFoundException)->setModel(get_class($this->userRepository->getModel()), $userId);
        }
        //$userInfo->status = 1;
        $userInfo->image_server_base_url = env('IMAGE_SERVER');
        //dd($userInfo->representative_info);
        if ($userInfo) {
            $ci = new CandidateTransformer();
            $cr = new RepresentativeTransformer();
            if($userInfo->candidate_info)
            $userInfo->candidate_info_modified = $ci->candidateSearchData($userInfo->candidate_info);
            else $userInfo->candidate_info_modified = null;
            if($userInfo->representative_info)
            $userInfo->representative_info_modified = $cr->RepDetails($userInfo->representative_info);
            else $userInfo->representative_info_modified = null;

            return $this->sendSuccess($userInfo, 'User info loaded successfully', [], FResponse::HTTP_OK);
        } else {
            return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function subscription(Request $request)
    {
        return $this->subscriptionService->subscriptionReport($request->all());
    }


    /**
     * @param $queryData
     * @return array
     */
    protected function pagination($queryData)
    {
        $data = [
            'total_items' => $queryData->total(),
            'current_items' => $queryData->count(),
            'first_item' => $queryData->firstItem(),
            'last_item' => $queryData->lastItem(),
            'current_page' => $queryData->currentPage(),
            'last_page' => $queryData->lastPage(),
            'has_more_pages' => $queryData->hasMorePages(),
        ];
        return $data;
    }

    //helper methods
    public function profileInfo(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item)
            ],
            [
                'personal' => $this->personalInfo($item)
            ],
            [
                'verification' => $this->verificationInfo($item)
            ],
            [
                'image_upload' => $this->imageUploadInfo($item)
            ]
        );
    }

    private function basicInfo(RepresentativeInformation $item): array
    {
        return [
            'id' => $item->id,
            'user_id'=>$item->user_id,
            'first_name'=>$item->first_name,
            'last_name'=>$item->last_name,
            'screen_name'=>$item->screen_name,
            'data_input_status' => $item->data_input_status
        ];
    }

    /**
     * Extract Essential info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function essentialInfo(RepresentativeInformation $item): array
    {
        return [
            'per_gender' => $item->per_gender,
            'per_gender_text' => CandidateInformation::getGender($item->per_gender),
            'dob' => $item->dob,
            'per_occupation' => $item->per_occupation,
        ];
    }

    /**
     * Extract Personal info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function personalInfo(RepresentativeInformation $item): array
    {
        return [
            'per_email' => $item->per_email,
            'per_current_residence_country' => $item->per_current_residence_country,
            'per_current_residence_country_text' => $item->currentResidenceCountry ? $item->currentResidenceCountry->name : null,
            'per_current_residence_city' => $item->per_current_residence_city,
            'per_permanent_country' => $item->per_permanent_country ,
            'per_permanent_country_text' => $item->permanentCountry ? $item->permanentCountry->name : null,
            'per_permanent_city' => $item->per_permanent_city,
            'per_county' => $item->per_county,
            'per_county_text' => $item->country ? $item->country->name : null,
            'per_telephone_no' => $item->per_telephone_no,
            'mobile_number' => $item->mobile_number,
            'mobile_country_code' => $item->mobile_country_code,
            'per_permanent_post_code' => $item->per_permanent_post_code,
            'per_permanent_address' => $item->per_permanent_address,
        ];
    }

    /**
     * Extract Verification info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function verificationInfo(RepresentativeInformation $item): array
    {
        return [
            'is_document_upload' => $item->is_document_upload,
            'ver_country' => $item->ver_country,
            'ver_city' => $item->ver_city,
            'ver_document_type' => $item->ver_document_type,
            'ver_document_frontside' => $item->ver_document_frontside ? env('IMAGE_SERVER') .'/'. $item->ver_document_frontside : '',
            'ver_document_backside' => $item->ver_document_backside ? env('IMAGE_SERVER') .'/'. $item->ver_document_backside : '',
            'ver_recommender_title' => $item->ver_recommender_title,
            'ver_recommender_first_name' => $item->ver_recommender_first_name,
            'ver_recommender_last_name' => $item->ver_recommender_last_name,
            'ver_recommender_occupation' => $item->ver_recommender_occupation,
            'ver_recommender_address' => $item->ver_recommender_address,
            'ver_recommender_mobile_no' => $item->ver_recommender_mobile_no,
        ];
    }

    /**
     * Extract Verification info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function imageUploadInfo(RepresentativeInformation $item): array
    {
        return [
            'per_avatar_url' => $item->per_avatar_url ?  $item->per_avatar_url : '',
            // 'per_avatar_url' => $item->per_avatar_url ? env('IMAGE_SERVER') .'/'. $item->per_avatar_url : '',
            // 'per_main_image_url' => $item->per_main_image_url ? env('IMAGE_SERVER') .'/'. $item->per_main_image_url : '',
            'per_main_image_url' => $item->per_main_image_url ?  $item->per_main_image_url : '',
            'anybody_can_see' => $item->anybody_can_see,
            'only_team_can_see' => $item->only_team_can_see,
            'team_connection_can_see' => $item->team_connection_can_see,
            'is_agree' => $item->is_agree,
        ];
    }

    private function galleryInfo(RepresentativeInformation $item)
    {
        return [
            'ver_document_frontside' => $item->ver_document_frontside ? env('IMAGE_SERVER') .'/'. $item->ver_document_frontside : '',
            'ver_document_backside' => $item->ver_document_backside ? env('IMAGE_SERVER') .'/'. $item->ver_document_backside : '',
            'per_avatar_url' => $item->per_avatar_url ? $item->per_avatar_url : '',
            // 'per_avatar_url' => $item->per_avatar_url ? env('IMAGE_SERVER') .'/'. $item->per_avatar_url : '',
            // 'per_main_image_url' => $item->per_main_image_url ? env('IMAGE_SERVER') .'/'. $item->per_main_image_url : '',
            'per_main_image_url' => $item->per_main_image_url ? $item->per_main_image_url : '',
        ];
    }

}
