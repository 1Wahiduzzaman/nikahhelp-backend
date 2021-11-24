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
     *
     * @param TeamFromRequest $request
     * @return JsonResponse
     */
    public function store(TeamFromRequest $request): JsonResponse
    {
        return $this->teamService->save($request);
    }

    /**
     * List user teams.
     *
     * @param Request $request
     *
     */
    public function teamList(Request $request)
    {
        return $this->teamService->getTeamList($request->all());
    }

    /**
     * Team Login.
     *
     * @param Request $request
     *
     */
    public function login(Request $request)
    {
        return $this->teamService->login($request);
    }

    /**
     * Team Edit Mode Check.
     *
     * @param Request $request
     *
     */
    public function teamEditCheck(Request $request)
    {
        return $this->teamService->teamEditCheck($request);
    }

    /**
     * Team Turn On.
     * @param Request $request
     */
    public function teamTurnOn(Request $request)
    {
        return $this->teamService->teamTurnOn($request);
    }

    /**
     * Display the specified resource.
     * @return Response
     */
    public function teamActiveStatusCheck($teamId)
    {
        return $this->teamService->checkTeamActiveStatus($teamId);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Team $team
     * @return Response
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Team $team
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return $this->teamService->teamUpdate($request, $id);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request)
    {
        //        
        return $this->teamService->deleteTeam($request);
    }

    public function teamInformation(Request $request, $id)
    {
        return $this->teamService->getTeamInformation($id);
    }
}
