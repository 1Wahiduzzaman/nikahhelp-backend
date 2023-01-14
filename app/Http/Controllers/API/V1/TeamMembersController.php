<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamMemberFromRequest;
use App\Http\Requests\Team\TeamLeaveRequest;
use App\Models\TeamMember;
use App\Models\TeamMemberInvitation;
use App\Services\TeamMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMembersController extends Controller
{
    /**
     * @var TeamMemberService
     */
    public $teamMemberService;

    public function __construct(TeamMemberService $teamMemberService)
    {
        $this->teamMemberService = $teamMemberService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->teamMemberService->findAll();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param TeamMemberFromRequest $request
     * @return JsonResponse
     */
    public function store(TeamMemberFromRequest $request): JsonResponse
    {
        return $this->teamMemberService->save($request->all());
    }

    /**
     * Change team member access.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changeTeamMemberAccess(Request $request)
    {
        return $this->teamMemberService->changeTeamMemberAccess($request->all());
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        return $this->teamMemberService->delete($request);
    }

    /**
     * @param TeamLeaveRequest $request
     * @return JsonResponse
     */
    public function teamLeave(TeamLeaveRequest $request)
    {
        return $this->teamMemberService->leaveTeam($request);
    }

    public function teamInvitationInformation($link = null) {        
        $data = TeamMemberInvitation::with(['team'=> function($q){
            $q->with('created_by:id,full_name,status,is_verified,locked_end, locked_at,form_type,updated_at,created_at,account_type')->with('team_members');
        }])
        ->where('link', $link)->first();
        return $this->sendSuccessResponse($data, 'Success');
        //dd($data);
    }
}
