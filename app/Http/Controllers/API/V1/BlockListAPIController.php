<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\BlockList\CreateBlockListAPIRequest;
use App\Http\Resources\BlockListResource;
use App\Models\CandidateImage;
use App\Models\CandidateInformation;
use App\Models\Generic;
use App\Repositories\BlockListRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\TeamRepository;
use App\Services\BlockListService;
use App\Transformers\CandidateTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Response;
use Symfony\Component\HttpFoundation\Response as FResponse;

/**
 * Class block_listController
 * @package App\Http\Controllers\API
 */
class BlockListAPIController extends AppBaseController
{
    private \App\Repositories\BlockListRepository $blockListRepository;

    private \App\Services\BlockListService $blockListService;
    private \App\Repositories\CandidateRepository $candidateRepository;
    private \App\Transformers\CandidateTransformer $candidateTransformer;
    private \App\Repositories\TeamRepository $teamRepository;

    public function __construct(
        BlockListRepository $blockListRepo,
        BlockListService $blockListService,
        CandidateRepository $candidateRepository,
        CandidateTransformer $candidateTransformer,
        TeamRepository $teamRepository
    ) {
        $this->blockListRepository = $blockListRepo;
        $this->blockListService = $blockListService;
        $this->candidateRepository = $candidateRepository;
        $this->candidateTransformer = $candidateTransformer;
        $this->teamRepository = $teamRepository;
    }

    /**
     * Display a listing of the block_list.
     * GET|HEAD /blockLists
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $userId = self::getUserId();

        $perPage = $request->input('parpage', 10);

        try {

            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            $activeTeamId = Generic::getActiveTeamId();

            if (!$activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }

            $activeTeam = $this->teamRepository->findOneByProperties([
                'id' => $activeTeamId
            ]);

            $userInfo['shortList'] = $activeTeam->teamShortListedUser->pluck('id')->toArray();
            $userInfo['blockList'] = $candidate->blockList->pluck('user_id')->toArray();
            $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
            $connectFrom = $candidate->teamConnection->pluck('from_team_id')->toArray();
            $connectTo = $candidate->teamConnection->pluck('to_team_id')->toArray();
            $userInfo['connectList'] = array_unique (array_merge($connectFrom,$connectTo)) ;

            $blockListCandidates = $candidate->blockList()->paginate($perPage);

            $candidatesResponse = [];

            foreach ($blockListCandidates as $candidate) {
                $candidate->is_short_listed = in_array($candidate->user_id,$userInfo['shortList']);
                $candidate->is_block_listed = in_array($candidate->user_id,$userInfo['blockList']);
                $candidate->is_teamListed = in_array($candidate->user_id,$userInfo['teamList']);

                /* Set Candidate Team related info */
                $teamId = null;
                if($candidate->active_team){
                    $teamId = $candidate->active_team->team_id;
                    $candidate->team_info = [
                        'team_id' => $candidate->active_team->id,
                        'name' => $candidate->active_team->name,
                        'members_id' => $candidate->active_team->team_members->pluck('user_id')->toArray(),
                    ];
                }
                $candidate->team_id = $teamId;

                $teamTableId = $candidate->active_team ? $candidate->active_team->id : '';
                $candidate->is_connect = $activeTeam->connectedTeam($teamTableId) ? $activeTeam->connectedTeam($teamTableId)->id : null;;
                $candidatesResponse[] = $this->candidateTransformer->transformSearchResult($candidate);
            }

            $pagination = $this->paginationResponse($blockListCandidates);

            return $this->sendSuccessResponse($candidatesResponse, 'Short Listed Candidate Fetch successfully!', $pagination, HttpStatusCode::CREATED);


        }catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }




//        $userId = $this->getUserId();
//        $blockLists = $this->blockListRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        )->where('block_by', '=', $userId);
//        $formatted_data = BlockListResource::collection($blockLists);
//        return $this->sendResponse($formatted_data, 'Block Lists retrieved successfully');
    }

    public function blockByTeamList(Request $request)
    {
        $userId = self::getUserId();

        try {
            $perPage = $request->input('perpage',10);

            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }

            /* Get Active Team instance */
            $activeTeamId = Generic::getActiveTeamId();
            if (!$activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }
            $activeTeam = $this->teamRepository->findOneByProperties([
                'id' => $activeTeamId
            ]);

            $userInfo['shortList'] = $activeTeam->teamShortListedUser->pluck('id')->toArray();
            $userInfo['blockList'] = $candidate->blockList->pluck('user_id')->toArray();
            $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
            $connectFrom = $candidate->teamConnection->pluck('from_team_id')->toArray();
            $connectTo = $candidate->teamConnection->pluck('to_team_id')->toArray();
            $userInfo['connectList'] = array_unique (array_merge($connectFrom,$connectTo)) ;


            $teamShortListUsers = $activeTeam->blockListedUser()->paginate($perPage) ;
            $teamShortListUsers->load('getCandidate') ;

            $candidatesResponse = [];

            foreach ($teamShortListUsers as $teamShortListUser) {
                $teamShortListUser->getCandidate->is_short_listed = in_array($teamShortListUser->id,$userInfo['shortList']);
                $teamShortListUser->getCandidate->is_block_listed = in_array($teamShortListUser->id,$userInfo['blockList']);
                $teamShortListUser->getCandidate->is_teamListed = in_array($teamShortListUser->id,$userInfo['teamList']);
                $teamId = $teamShortListUser->getCandidate->candidateTeam()->exists() ? $teamShortListUser->getCandidate->candidateTeam->first()->getTeam->team_id : null;
                $teamShortListUser->getCandidate->is_connect = in_array($teamId,$userInfo['connectList']);
                $teamShortListUser->getCandidate->team_id = $teamId;
                $shortListedBy = CandidateInformation::where('user_id', $teamShortListUser->pivot->block_by)->first();
                $teamShortListUser->pivot->shortlisted_by =$shortListedBy->first_name.' '. $shortListedBy->last_name;

                $candidatesResponse[] = $this->candidateTransformer->transformShortListUser($teamShortListUser);
            }

            $pagination = $this->paginationResponse($teamShortListUsers);

            return $this->sendSuccessResponse($candidatesResponse, 'Short Listed Candidate Fetch successfully!',$pagination, HttpStatusCode::CREATED);

        }catch (\Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Store a newly created block_list in storage.
     * POST /blockLists
     *
     * @param  CreateBlockListAPIRequest  $request
     *
     * @return Response
     */
    public function store(CreateBlockListAPIRequest $request)
    {
        return $blockList = $this->blockListService->store($request);
    }

    /**
     * Display the specified block_list.
     * GET|HEAD /blockLists/{id}
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var block_list $blockList */
        $blockList = $this->blockListRepository->find($id);

        if (empty($blockList)) {
            return $this->sendError('Block List not found');
        }

        return $this->sendResponse($blockList->toArray(), 'Block List retrieved successfully');
    }


    /**
     * Remove the specified block_list from storage.
     * DELETE /blockLists/{id}
     *
     * @param  int  $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function destroy($id)
    {
        /** @var block_list $blockList */
        $blockList = $this->blockListRepository->findOrFail($id);

        if (empty($blockList)) {
            return $this->sendError('Block List not found');
        }
        $blockList->delete();

        return $this->sendResponse([], 'Candidate Un-Block successful', FResponse::HTTP_OK);
    }

    public function destroyByCandidate(Request $request)
    {
        $userId = self::getUserId();

        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()),
                    $userId);
            }

            $candidate->blockList()->detach($request->user_id);

            return $this->sendSuccessResponse([], 'Candidate Un-Block successfully!', [], HttpStatusCode::CREATED);
        } catch (\Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
