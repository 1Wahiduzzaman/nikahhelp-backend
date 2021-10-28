<?php

namespace App\Http\Controllers\API;

use App\Helpers\Notificationhelpers;
use App\Http\Requests\API\CreateShortListedCandidateAPIRequest;
use App\Http\Requests\API\UpdateShortListedCandidateAPIRequest;
use App\Models\BlockList;
use App\Models\ShortListedCandidate;
use App\Models\Team;
use App\Models\TeamMember;
use App\Repositories\ShortListedCandidateRepository;
use App\Services\BlockListService;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\ShortlistedCandidateResource;

/**
 * Class ShortListedCandidateController
 * @package App\Http\Controllers\API\V1
 */
class ShortListedCandidateController extends AppBaseController
{
    use CrudTrait;

    /**
     * @var BlockListService
     */
    protected $blockListService;

    /** @var  ShortListedCandidateRepository */
    private $shortListedCandidateRepository;

    public function __construct(ShortListedCandidateRepository $shortListedCandidateRepository, BlockListService $blockListService)
    {
        $this->shortListedCandidateRepository = $shortListedCandidateRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($shortListedCandidateRepository);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId();
        $parpage = (isset($request['parpage']) && !empty($request['parpage'])) ? $request['parpage'] : 10;
        $shortList = $this->actionRepository->getModel()->newQuery();
        $shortList->where('shortlisted_by', '=', $userId);
        // check block listed Candidate
        if (!empty($userId)) {
            $silgleBLockList = $this->blockListService->blockListByUser($userId)->toArray();
            if (count($silgleBLockList) >= 1) {
                $shortList->whereNotIn('user_id', $silgleBLockList);
            }
        }

        $page = $request['page'] ?: 1;
        if ($page < 1):$page = 1;endif;
        if ($page) {
            $skip = $parpage * ($page - 1);
            $shortListedCandidates = $shortList->limit($parpage)->offset($skip)->orderBy('id', 'DESC')->get();
        } else {
            $shortListedCandidates = $shortList->limit($parpage)->offset(0)->get();
        }

        $formatted_data = ShortlistedCandidateResource::collection($shortListedCandidates);
        return $this->sendResponse($formatted_data, 'Short Listed Candidates retrieved successfully');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamShortListedCandidate(Request $request)
    {
        $parpage = $request['parpage'] ?? 100;
        $activeTeamId = $request['active_team_id'] ?? "";
        $userId = $this->getUserId();
        $search = $this->actionRepository->getModel()->newQuery();
        if (!empty($activeTeamId)) {
            $activeTeamId = Team::where('team_id', '=', $activeTeamId)->first();
            $activeTeamId = $activeTeamId->id;
            $teamBlockList = $this->blockListService->getActiveTeamBlockListByUser($activeTeamId);
            $search->whereNotIn('user_id', $teamBlockList->toArray());
            $search->where('shortlisted_for', $activeTeamId);

        } else {
            $joinTeamList = TeamMember::where('user_id', '=', $userId)->groupBy('team_id')->pluck('team_id');
            if (!empty($joinTeamList)) {
                $search->WhereIn('shortlisted_for', $joinTeamList);
            } else {
                return $this->sendResponse([], 'Team Short Listed Candidates retrieved successfully');
            }
        }
        $page = $request['page'] ?: 1;
        if ($page) {
            $skip = $parpage * ($page - 1);
            $queryData = $search->limit($parpage)->offset($skip)->get();
        } else {
            $queryData = $search->limit($parpage)->offset(0)->get();
        }
        $shortListedCandidates = $queryData;
        $formatted_data = ShortlistedCandidateResource::collection($shortListedCandidates);
        return $this->sendResponse($formatted_data, 'Team Short Listed Candidates retrieved successfully');
    }


    /**
     * Store a newly created ShortListedCandidate in storage.
     * POST /shortListedCandidates
     *
     * @param CreateShortListedCandidateAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateShortListedCandidateAPIRequest $request)
    {
        $input = $request->all();
        $input['shortlisted_date'] = Carbon::now();
        $shortListedCandidate = $this->shortListedCandidateRepository->create($input);
        Notificationhelpers::add('Short Listed Candidate saved successfully', 'single', null, $input['shortlisted_by']);
        return $this->sendResponse($shortListedCandidate->toArray(), 'Short Listed Candidate saved successfully', FResponse::HTTP_CREATED);
    }

    /**
     * Display the specified ShortListedCandidate.
     * GET|HEAD /shortListedCandidates/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ShortListedCandidate $shortListedCandidate */
        $shortListedCandidate = $this->shortListedCandidateRepository->findOne($id);

        if (empty($shortListedCandidate)) {
            return $this->sendError('Short Listed Candidate not found');
        }

        return $this->sendResponse($shortListedCandidate->toArray(), 'Short Listed Candidate retrieved successfully');
    }

    /**
     * Update the specified ShortListedCandidate in storage.
     * PUT/PATCH /shortListedCandidates/{id}
     *
     * @param int $id
     * @param UpdateShortListedCandidateAPIRequest $request
     *
     * @return Response
     */

    public function update($id, UpdateShortListedCandidateAPIRequest $request)
    {
        $input = $request->all();
        try {

            $shortListedCandidate = $this->shortListedCandidateRepository->findOne($id);
            if (empty($shortListedCandidate)) {
                return $this->sendError('Short Listed Candidate not found');
            }

            $shortListedCandidate->update($input);
            return $this->sendResponse($shortListedCandidate, 'ShortListedCandidate updated successfully');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Remove the specified ShortListedCandidate from storage.
     * DELETE /shortListedCandidates/{id}
     *
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function destroy($id)
    {
        /** @var ShortListedCandidate $shortListedCandidate */
        $shortListedCandidate = $this->shortListedCandidateRepository->findOne($id);

        if (empty($shortListedCandidate)) {
            return $this->sendError('Short Listed Candidate not found', FResponse::HTTP_NOT_FOUND);
        }

        $shortListedCandidate->delete();
        return $this->sendSuccess([], 'Short Listed Candidate deleted successfully', FResponse::HTTP_OK);
    }

    public function deletedCandidate()
    {
        $deletedCandidate = $this->shortListedCandidateRepository->deletedCandidate();
        $formatted_data = ShortlistedCandidateResource::collection($deletedCandidate);
        return $this->sendResponse($formatted_data, 'Deleted Candidate List');
    }


}
