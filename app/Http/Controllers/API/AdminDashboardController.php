<?php

namespace App\Http\Controllers\API;

use App\Models\ShortListedCandidate;
use App\Repositories\ShortListedCandidateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\UserReportResource;
use App\Models\CandidateInformation;
use App\Models\RejectedNote;
use App\Services\AdminService;
use App\Services\SubscriptionService;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Transformers\CandidateTransformer;

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
        SubscriptionService $subscriptionService

    )
    {
        $this->shortListedCandidateRepository = $shortListedCandidateRepo;
        $this->adminService = $adminService;
        $this->userRepository = $UserRepository;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of the ShortListedCandidate.
     * GET|HEAD /shortListedCandidates
     *
     * @param Request $request
     * @return Response
     */
    public function dashboard(Request $request)
    {
        $userId = $this->getUserId();
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
                $q->select(['data_input_status', 'user_id']);
            }])
            ->with(['representative_info' => function($q){
                $q->select('data_input_status');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        } 
        elseif (!empty($request->account_type) && empty($request->keyword)) {
            $data = User::where('account_type', $account_type)
            ->with(['candidate_info' => function($q){
                $q->select('data_input_status');
            }])
            ->with(['representative_info' => function($q){
                $q->select('data_input_status');
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
                $q->select(['data_input_status', 'user_id']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        else {
            $data = User::with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id']);
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
                $q->select(['data_input_status', 'user_id']);
            }])
            ->with(['representative_info' => function($q){
                $q->select('data_input_status');
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        } 
        elseif (!empty($request->account_type) && empty($request->keyword)) {
            $data = User::where('status', $status)
            ->where('account_type', $account_type)
            ->with(['candidate_info' => function($q){
                $q->select('data_input_status');
            }])
            ->with(['representative_info' => function($q){
                $q->select('data_input_status');
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
                $q->select(['data_input_status', 'user_id']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        }
        else {
            $data = User::where('status', $status)            
            ->with(['candidate_info' => function($q){
                $q->select(['data_input_status', 'user_id']);
            }])
            ->with(['representative_info' => function($q){
                $q->select(['data_input_status', 'user_id']);
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

    public function verifiedUserList(Request $request)
    {
       $data =  $this->getUserData($request, 3);
       return $this->sendResponse($data, 'Data retrieved successfully');
    }

    public function rejectedUserList(Request $request)
    {
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
        $status = [
            'verified' => 3,
            'rejected' => 4,
            'completed' => 2,
            'incompleted' => 1,
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
        $userInfo->status = $ver_rej;
        if ($userInfo->save()) {
            if($ver_rej == '4') {
                $rj = new RejectedNote();
                $rj->user_id = $userId;
                $rj->note = $request->note;
                $rj->save();                
            }
            return $this->sendSuccess($userInfo, 'User '. $request->status.' successfully', [], FResponse::HTTP_OK);
        } else {
            return $this->sendError('Something went wrong please try again later', FResponse::HTTP_NOT_MODIFIED);
        }

    }

    public function UserInfo($id = null) {
        if (!empty($id)) {
            $userId = $id;
        } else {
            return $this->sendError('User Id is required ', FResponse::HTTP_BAD_REQUEST);
        }
        $userInfo = User::with(['candidate_info', 'representative_info', 'candidate_image', 'rejected_notes'])->where('id', $userId)->first();
        if (!$userInfo) {
            throw (new ModelNotFoundException)->setModel(get_class($this->userRepository->getModel()), $userId);
        }
        //$userInfo->status = 1;
        $userInfo->image_server_base_url = env('IMAGE_SERVER');
        if ($userInfo) {
            //dd($userInfo->candidate_info);
            $ci = new CandidateTransformer();
            $userInfo->candidate_info_modified = $ci->candidateSearchData($userInfo->candidate_info);
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


}
