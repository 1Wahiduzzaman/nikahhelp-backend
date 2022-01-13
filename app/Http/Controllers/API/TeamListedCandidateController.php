<?php

namespace App\Http\Controllers\API;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use App\Http\Requests\API\CreateShortListedCandidateAPIRequest;
use App\Http\Requests\API\CreateTeamListedCandidateAPIRequest;
use App\Http\Requests\API\UpdateShortListedCandidateAPIRequest;
use App\Models\BlockList;
use App\Models\CandidateInformation;
use App\Models\Generic;
use App\Models\ShortListedCandidate;
use App\Models\Team;
use App\Models\TeamMember;
use App\Repositories\CandidateRepository;
use App\Repositories\ShortListedCandidateRepository;
use App\Repositories\TeamListedCandidateRepository;
use App\Repositories\TeamRepository;
use App\Services\BlockListService;
use App\Traits\CrudTrait;
use App\Transformers\CandidateTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\ShortlistedCandidateResource;

class TeamListedCandidateController extends AppBaseController
{

    use CrudTrait;

    /**
     * @var BlockListService
     */
    protected $blockListService;

    /** @var  ShortListedCandidateRepository */
    private $shortListedCandidateRepository;

    /**
     * @var CandidateRepository
     */
    protected $candidateRepository;


    /**
     * @var CandidateTransformer
     */
    private $candidateTransformer;
    /**
     * @var TeamRepository
     */
    private $teamRepository;
    /**
     * @var TeamListedCandidateRepository
     */
    private $teamListedCandidateRepository;

    public function __construct(
        ShortListedCandidateRepository $shortListedCandidateRepository,
        TeamListedCandidateRepository $teamListedCandidateRepository,
        CandidateRepository $candidateRepository,
        BlockListService $blockListService,
        CandidateTransformer $candidateTransformer,
        TeamRepository $teamRepository
    ) {
        $this->shortListedCandidateRepository = $shortListedCandidateRepository;
        $this->teamListedCandidateRepository = $teamListedCandidateRepository;
        $this->candidateRepository = $candidateRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($shortListedCandidateRepository);
        $this->candidateTransformer = $candidateTransformer;
        $this->teamRepository = $teamRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamListedCandidateAPIRequest $request)
    {
        $input = $request->all();
        $input['team_listed_date'] = Carbon::now();
        $input['team_listed_for'] = Generic::getActiveTeamId();
        if (!$input['team_listed_for']) {
            return $this->sendErrorResponse('Team Not found, Please make team first');
        }
        $teamListedCandidate = $this->teamListedCandidateRepository->create($input);
        return $this->sendResponse(
            $teamListedCandidate->toArray(),
            'Team Listed Candidate saved successfully',
            FResponse::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\TeamListedCandidate $teamListedCandidate
     * @return \Illuminate\Http\Response
     */
    public function show(TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\TeamListedCandidate $teamListedCandidate
     * @return \Illuminate\Http\Response
     */
    public function edit(TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\TeamListedCandidate $teamListedCandidate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\TeamListedCandidate $teamListedCandidate
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyByCandidate(Request $request)
    {
        $userId = self::getUserId();

        try {
            $candidate = $this->candidateRepository->findOneByProperties(
                [
                    'user_id' => $userId
                ]
            );

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(
                    get_class($this->candidateRepository->getModel()),
                    $userId
                );
            }

            /* Get Active Team instance */
            $activeTeamId = Generic::getActiveTeamId();
            if (!$activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }

            $candidate->teamList()->wherePivot('team_listed_for', $activeTeamId)->detach($request->user_id);

            return $this->sendSuccessResponse( [], 'Candidate remove from shortlist successfully!',[],HttpStatusCode::CREATED);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

}
