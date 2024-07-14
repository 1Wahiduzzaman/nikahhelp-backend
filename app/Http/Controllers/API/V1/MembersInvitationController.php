<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\JoinByInvitationRequest;
use App\Http\Requests\MemberInvitationRequest;
use App\Services\MemberInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembersInvitationController extends Controller
{
    /**
     * @var MemberInvitationService
     */
    public $memberInvitationService;

    public function __construct(MemberInvitationService $memberInvitationService)
    {
        $this->memberInvitationService = $memberInvitationService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberInvitationRequest $request): JsonResponse
    {
        return $this->memberInvitationService->save($request->all());
    }

    public function update(Request $request)
    {
        return $this->memberInvitationService->edit($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function joinTeamByInvitation(JoinByInvitationRequest $request)
    {
        return $this->memberInvitationService->join($request->all());
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->memberInvitationService->delete($request);
    }
}
