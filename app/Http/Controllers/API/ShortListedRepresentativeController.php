<?php

namespace App\Http\Controllers\API;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateShortListedCandidateAPIRequest;
use App\Http\Requests\API\UpdateShortListedCandidateAPIRequest;
use App\Http\Resources\ShortlistedCandidateResource;
use App\Models\CandidateInformation;
use App\Models\Generic;
use App\Models\ShortListedCandidate;
use App\Models\Team;
use App\Repositories\RepresentativeInformationRepository;
use App\Repositories\ShortListedRepresentativeRepository;
use App\Repositories\TeamRepository;
use App\Services\BlockListService;
use App\Traits\CrudTrait;
use App\Transformers\RepresentativeTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Response;
use Symfony\Component\HttpFoundation\Response as FResponse;

/**
 * Class ShortListedCandidateController
 */
class ShortListedRepresentativeController extends AppBaseController
{
    use CrudTrait;

    protected \App\Services\BlockListService $blockListService;

    private \App\Repositories\ShortListedRepresentativeRepository $shortListedRepresentativeRepository;

    protected \App\Repositories\RepresentativeInformationRepository $representativeRepository;

    private \App\Transformers\RepresentativeTransformer $representativeTransformer;

    private \App\Repositories\TeamRepository $teamRepository;

    public function __construct(
        ShortListedRepresentativeRepository $shortListedRepresentativeRepository,
        RepresentativeInformationRepository $representativeRepository,
        BlockListService $blockListService,
        RepresentativeTransformer $representativeTransformer,
        TeamRepository $teamRepository
    ) {
        $this->shortListedRepresentativeRepository = $shortListedRepresentativeRepository;
        $this->representativeRepository = $representativeRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($shortListedRepresentativeRepository);
        $this->representativeTransformer = $representativeTransformer;
        $this->teamRepository = $teamRepository;

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userId = self::getUserId();

        $perPage = $request->input('parpage', 10);

        try {
            $representative = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId,
            ]);

            if (! $representative) {
                throw (new ModelNotFoundException)->setModel(get_class($this->representativeRepository->getModel()), $userId);
            }

            $activeTeamId = (new Generic())->getActiveTeamId();

            if (! $activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }

            $activeTeam = $this->teamRepository->findOneByProperties([
                'id' => $activeTeamId,
            ]);

            $userInfo['shortList'] = $activeTeam->teamShortListedUser->pluck('id')->toArray();
            $userInfo['blockList'] = $representative->blockList->pluck('user_id')->toArray();
            $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
            $connectFrom = $representative->teamConnection->pluck('from_team_id')->toArray();
            $connectTo = $representative->teamConnection->pluck('to_team_id')->toArray();
            $userInfo['connectList'] = array_unique(array_merge($connectFrom, $connectTo));

            $singleBLockList = $this->blockListService->blockListByUser($userId)->toArray();

            $shortListRepresentative = $representative->shortList()->wherePivot('shortlisted_for', $activeTeam->id)->whereNotIn('candidate_information.user_id', $singleBLockList)->paginate($perPage);

            $representativeResponse = [];

            foreach ($shortListRepresentative as $representative) {
                $representative->is_short_listed = in_array($representative->user_id, $userInfo['shortList']);
                $representative->is_block_listed = in_array($representative->user_id, $userInfo['blockList']);
                $representative->is_teamListed = in_array($representative->user_id, $userInfo['teamList']);

                /* Set Candidate Team related info */
                $teamId = null;
                if ($representative->active_team) {
                    $teamId = $representative->active_team->team_id;
                    $representative->team_info = [
                        'team_id' => $representative->active_team->id,
                        'name' => $representative->active_team->name,
                        'members_id' => $representative->active_team->team_members->pluck('user_id')->toArray(),
                    ];
                }
                $representative->team_id = $teamId;

                $representative->is_connect = in_array($teamId, $userInfo['connectList']);
                $representativeResponse[] = $this->representativeTransformer->transformSearchResult($representative);
            }

            $pagination = $this->paginationResponse($shortListRepresentative);

            return $this->sendSuccessResponse($representativeResponse, 'Short Listed Candidate Fetch successfully!', $pagination, HttpStatusCode::CREATED->value);

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamShortListedCandidate(Request $request)
    {

        $userId = self::getUserId();

        try {
            $perPage = $request->input('perpage', 10);

            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId,
            ]);

            if (! $candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            /* Get Active Team instance */
            $activeTeamId = (new Generic())->getActiveTeamId();
            if (! $activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }
            $activeTeam = $this->teamRepository->findOneByProperties([
                'id' => $activeTeamId,
            ]);

            $userInfo['shortList'] = $activeTeam->teamShortListedUser->pluck('id')->toArray();
            $userInfo['blockList'] = $candidate->blockList->pluck('id')->toArray();
            $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
            $connectFrom = $candidate->teamConnection->pluck('from_team_id')->toArray();
            $connectTo = $candidate->teamConnection->pluck('to_team_id')->toArray();
            $userInfo['connectList'] = array_unique(array_merge($connectFrom, $connectTo));

            $teamShortListUsers = $activeTeam->teamListedUser()->paginate($perPage);
            $teamShortListUsers->load('getCandidate');

            $candidatesResponse = [];

            foreach ($teamShortListUsers as $teamShortListUser) {
                $teamShortListUser->getCandidate->is_short_listed = in_array($teamShortListUser->id, $userInfo['shortList']);
                $teamShortListUser->getCandidate->is_block_listed = in_array($teamShortListUser->id, $userInfo['blockList']);
                $teamShortListUser->getCandidate->is_teamListed = in_array($teamShortListUser->id, $userInfo['teamList']);

                /* Set Candidate Team related info */
                $teamId = null;
                $teamTableId = '';
                if ($teamShortListUser->getCandidate->active_team) {
                    $teamId = $teamShortListUser->getCandidate->active_team->team_id;
                    $teamTableId = $candidate->active_team->id;
                    $teamShortListUser->getCandidate->team_info = [
                        'team_id' => $teamShortListUser->getCandidate->active_team->team_id,
                        'name' => $teamShortListUser->getCandidate->active_team->name,
                        'members_id' => $teamShortListUser->getCandidate->active_team->team_members->pluck('user_id')->toArray(),
                    ];
                }
                $teamShortListUser->getCandidate->team_id = $teamId;
                $teamShortListUser->getCandidate->is_connect = $activeTeam->connectedTeam($teamTableId) ? $activeTeam->connectedTeam($teamTableId)->id : null;
                $shortListedBy = CandidateInformation::where('user_id', $teamShortListUser->pivot->team_listed_by)->first();
                $teamShortListUser->pivot->shortlisted_by = $shortListedBy->first_name.' '.$shortListedBy->last_name;
                $candidatesResponse[] = $this->candidateTransformer->transformShortListUser($teamShortListUser);
            }

            $pagination = $this->paginationResponse($teamShortListUsers);

            return $this->sendSuccessResponse($candidatesResponse, 'Short Listed Candidate Fetch successfully!', $pagination, HttpStatusCode::CREATED->value);

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Store a newly created ShortListedCandidate in storage.
     * POST /shortListedCandidates
     *
     *
     * @return Response
     */
    public function store(CreateShortListedCandidateAPIRequest $request)
    {
        $input = $request->all();
        $input['shortlisted_date'] = Carbon::now();
        $input['shortlisted_for'] = (new Generic())->getActiveTeamId();
        if (! $input['shortlisted_for']) {
            return $this->sendErrorResponse('Team Not found, Please make team first');
        }
        $shortListedRepresentative = $this->shortListedRepresentativeRepository->create($input);

        //        Notificationhelpers::add('Short Listed Candidate saved successfully', 'single', null, $input['shortlisted_by']);
        return $this->sendResponse($shortListedRepresentative->toArray(), 'Short Listed Candidate saved successfully', FResponse::HTTP_CREATED);
    }

    /**
     * Display the specified ShortListedCandidate.
     * GET|HEAD /shortListedCandidates/{id}
     *
     * @param  int  $id
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
     * @param  int  $id
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
     * @param  int  $id
     * @return Response
     *
     * @throws \Exception
     */
    public function destroy($id)
    {

        $shortListedRepresentative = $this->shortListedRepresentativeRepository->findOne($id);

        if (empty($shortListedRepresentative)) {
            return $this->sendError('Short Listed Candidate not found', FResponse::HTTP_NOT_FOUND);
        }

        $shortListedRepresentative->delete();

        return $this->sendSuccess([], 'Short Listed Candidate deleted successfully', FResponse::HTTP_OK);
    }

    public function deletedCandidate()
    {
        $deletedCandidate = $this->shortListedCandidateRepository->deletedCandidate();
        $formatted_data = ShortlistedCandidateResource::collection($deletedCandidate);

        return $this->sendResponse($formatted_data, 'Deleted Candidate List');
    }

    public function destroyByCandidate(Request $request)
    {
        $userId = self::getUserId();

        try {
            $representative = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId,
            ]);

            if (! $representative) {
                throw (new ModelNotFoundException)->setModel(get_class($this->representativeRepository->getModel()), $userId);
            }

            /* Get Active Team instance */
            $activeTeamId = (new Generic())->getActiveTeamId();
            if (! $activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }

            $representative->shortList()->detach($request->user_id);

            return $this->sendSuccessResponse([], 'Candidate remove from shortlist successfully!', [], HttpStatusCode::CREATED->value);

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
