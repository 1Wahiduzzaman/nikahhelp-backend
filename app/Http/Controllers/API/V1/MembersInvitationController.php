<?php
namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberInvitationRequest;
use App\Http\Requests\JoinByInvitationRequest;
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
     *
     * @param MemberInvitationRequest $request
     * @return JsonResponse
     */
    public function store(MemberInvitationRequest $request): JsonResponse
    {
        return $this->memberInvitationService->save($request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param JoinByInvitationRequest $request
     * @return JsonResponse
     */
    public function joinTeamByInvitation(JoinByInvitationRequest $request)
    {
        return $this->memberInvitationService->join($request->all());
    }

}
