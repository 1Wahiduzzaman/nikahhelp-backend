<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\ShortListedCandidate;
use App\Repositories\ShortListedCandidateRepository;
use App\Services\UserService;
use DB;
use Illuminate\Http\Request;
use Response;

/**
 * Class ShortListedCandidateController
 */
class UserDashboardController extends AppBaseController
{
    private \App\Repositories\ShortListedCandidateRepository $shortListedCandidateRepository;

    /**
     * PurchaseController constructor.
     */
    public function __construct(
        ShortListedCandidateRepository $shortListedCandidateRepo,
        UserService $userService
    ) {
        $this->shortListedCandidateRepository = $shortListedCandidateRepo;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the ShortListedCandidate.
     * GET|HEAD /shortListedCandidates
     *
     * @return Response
     */
    public function dashboard(Request $request)
    {
        $userId = $this->getUserId();
        $personalList = ShortListedCandidate::where('shortlisted_by', '=', $userId)->whereNull('shortlisted_for')->count();
        $personalListTeam = ShortListedCandidate::where('shortlisted_by', '=', $userId)->whereNotNull('shortlisted_for')->count();
        $result['short_list']['total'] = $personalList + $personalListTeam;
        $result['short_list']['personal'] = $personalList;
        $result['short_list']['team'] = $personalListTeam;
        $profileView = DB::table('profile_logs')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %h:%i') as categories"), DB::raw('COUNT(*) as data'))
            ->groupBy('categories')
            ->get();
        $result['profile_view'] = $profileView;

        return $this->sendResponse($result, 'Dashboard information patch successfully');
    }

    public function profileLog(Request $request)
    {

        return $this->userService->storeProfileLogs($request->all());
    }

    public function getprofileLog(Request $request)
    {

        return $this->userService->getProfileLogs();
    }
}
