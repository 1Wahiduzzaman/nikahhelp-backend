<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
//use App\Http\Requests\TeamFromRequest;
//use App\Models\Team;
use App\Http\Requests\Team\TeamDisconnectRequest;
use App\Services\TeamConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TeamConnectionController extends Controller
{
    /**
     * @var TeamConnectionService
     */
    public $teamConnectionService;

    public function __construct(TeamConnectionService $teamConnectionService)
    {
        $this->teamConnectionService = $teamConnectionService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        Log::error('not going');
        return $this->teamConnectionService->sendRequest($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function respond(Request $request)
    {
        return $this->teamConnectionService->respondRequest($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function report(Request $request)
    {
        return $this->teamConnectionService->report($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reports(Request $request)
    {
        return $this->teamConnectionService->reports($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function overview(Request $request)
    {
        return $this->teamConnectionService->overview($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function disconnect(Request $request)
    {
        return $this->teamConnectionService->disconnect($request);
    }

    /**
     * @param TeamDisconnectRequest $request
     * @return \App\Services\JsonResponse|Response
     */
    public function teamDisconnect(TeamDisconnectRequest $request)
    {
        return $this->teamConnectionService->teamDisconnect($request);
    }



}
