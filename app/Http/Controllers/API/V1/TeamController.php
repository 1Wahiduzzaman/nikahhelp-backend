<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamFromRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamController extends Controller
{
    /**
     * @var TeamService
     */
    public $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamFromRequest $request): JsonResponse
    {
        return $this->teamService->save($request);
    }

    /**
     * List user teams.
     */
    public function teamList(Request $request)
    {
        return $this->teamService->getTeamList($request->all());
    }

    /**
     * Team Login.
     */
    public function login(Request $request)
    {
        return $this->teamService->login($request);
    }

    /**
     * Team Edit Mode Check.
     */
    public function teamEditCheck(Request $request)
    {
        return $this->teamService->teamEditCheck($request);
    }

    /**
     * Team Turn On.
     */
    public function teamTurnOn(Request $request)
    {
        return $this->teamService->teamTurnOn($request);
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function teamActiveStatusCheck($teamId)
    {
        return $this->teamService->checkTeamActiveStatus($teamId);
    }

    /**
     * Update a family info.
     *
     * @param  CandidateFamilyInfoRequest  $request
     */
    public function candidateOfTeam(): JsonResponse
    {
        return $this->teamService->candidateOfTeam();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Team  $team
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return $this->teamService->teamUpdate($request, $id);

    }

    public function resetPin(Request $request)
    {
        return $this->teamService->teamResetPin($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Request $request)
    {
        //
        return $this->teamService->deleteTeam($request);
    }

    public function teamInformation($id)
    {
        return $this->teamService->getTeamInformation($id);
    }

    //Admin started / Raz
    public function adminTeamList(Request $request)
    {
        return $this->teamService->getTeamListForBackend($request->all());
    }

    public function adminDeletedTeamList(Request $request)
    {
        return $this->teamService->getDeletedTeamListForBackend($request->all());
    }

    public function adminConnectedTeamList($team_id = null)
    {
        return $this->teamService->getConnectedTeamListForBackend($team_id);
    }

    public function adminTeamDelete(Request $request)
    {
        //
        return $this->teamService->adminTeamDelete($request);
    }
}
