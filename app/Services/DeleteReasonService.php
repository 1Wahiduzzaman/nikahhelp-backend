<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\Team;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\TeamRepository;
use App\Repositories\DeleteReasonRepository;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use App\Transformers\TeamTransformer;
use Illuminate\Support\Str;
use App\Http\Requests\DeleteReasonSubmitRequest;

class DeleteReasonService extends ApiBaseService
{

    use CrudTrait;

    protected \App\Repositories\UserRepository $userRepository;


    protected \App\Repositories\TeamRepository $teamRepository;

    protected \App\Repositories\DeleteReasonRepository $deleteReasonRepository;


    /**
     * TeamService constructor.
     *
     * @param TeamRepository $teamRepository
     * @param UserRepository $userRepository
     * @param DeleteReasonRepository $deleteReasonRepository
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
     * @param DeleteReasonSubmitRequest $request
     * @return JsonResponse
     */
    public function save($request)
    {
        $team_id = $request->team_id;
        $team = $this->teamRepository->findOneByProperties([
            'team_id' => $team_id
        ]);

        $user_id = Auth::id();
        $reason_type = $request->reason_type;
        $reason_text = $request->reason_text;

        $reason = array();
        $reason["team_id"] = $team->id;
        $reason["user_id"] = $user_id;
        $reason["reason_type"] = $reason_type;
        $reason["reason_text"] = $reason_text;

        $reason = $this->deleteReasonRepository->save($reason);
        return $this->sendSuccessResponse($reason, 'Information inserted Successfully!');
    }


}
