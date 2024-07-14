<?php

namespace App\Services;

use App\Http\Requests\DeleteReasonSubmitRequest;
use App\Repositories\DeleteReasonRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DeleteReasonService extends ApiBaseService
{
    use CrudTrait;

    protected \App\Repositories\UserRepository $userRepository;

    protected \App\Repositories\TeamRepository $teamRepository;

    protected \App\Repositories\DeleteReasonRepository $deleteReasonRepository;

    /**
     * TeamService constructor.
     */
    public function __construct(TeamRepository $teamRepository,
        UserRepository $userRepository,
        DeleteReasonRepository $deleteReasonRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->deleteReasonRepository = $deleteReasonRepository;
    }

    /**
     * Update resource
     *
     * @param  DeleteReasonSubmitRequest  $request
     * @return JsonResponse
     */
    public function save($request)
    {
        $team_id = $request->team_id;
        $team = $this->teamRepository->findOneByProperties([
            'team_id' => $team_id,
        ]);

        $user_id = Auth::id();
        $reason_type = $request->reason_type;
        $reason_text = $request->reason_text;

        $reason = [];
        $reason['team_id'] = $team->id;
        $reason['user_id'] = $user_id;
        $reason['reason_type'] = $reason_type;
        $reason['reason_text'] = $reason_text;

        $reason = $this->deleteReasonRepository->save($reason);

        return $this->sendSuccessResponse($reason, 'Information inserted Successfully!');
    }
}
