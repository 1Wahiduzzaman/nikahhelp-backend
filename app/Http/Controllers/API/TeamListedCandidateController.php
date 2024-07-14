<?php

namespace App\Http\Controllers\API;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateTeamListedCandidateAPIRequest;
use App\Models\Generic;
use App\Models\Team;
use App\Models\TeamListedCandidate;
use App\Repositories\CandidateRepository;
use App\Repositories\RepresentativeInformationRepository;
use App\Repositories\ShortListedCandidateRepository;
use App\Repositories\TeamListedCandidateRepository;
use App\Repositories\TeamRepository;
use App\Services\BlockListService;
use App\Traits\CrudTrait;
use App\Transformers\CandidateTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as FResponse;

class TeamListedCandidateController extends AppBaseController
{
    use CrudTrait;

    protected \App\Services\BlockListService $blockListService;

    private \App\Repositories\ShortListedCandidateRepository $shortListedCandidateRepository;

    protected \App\Repositories\CandidateRepository $candidateRepository;

    private \App\Transformers\CandidateTransformer $candidateTransformer;

    private \App\Repositories\TeamRepository $teamRepository;

    private \App\Repositories\TeamListedCandidateRepository $teamListedCandidateRepository;

    public function __construct(
        ShortListedCandidateRepository $shortListedCandidateRepository,
        TeamListedCandidateRepository $teamListedCandidateRepository,
        CandidateRepository $candidateRepository,
        RepresentativeInformationRepository $representativeInformationRepository,
        BlockListService $blockListService,
        CandidateTransformer $candidateTransformer,
        TeamRepository $teamRepository
    ) {
        $this->shortListedCandidateRepository = $shortListedCandidateRepository;
        $this->teamListedCandidateRepository = $teamListedCandidateRepository;
        $this->candidateRepository = $candidateRepository;
        $this->representativeInformationRepository = $representativeInformationRepository;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamListedCandidateAPIRequest $request)
    {
        $input = $request->all();
        $input['team_listed_date'] = Carbon::now();
        $input['team_listed_for'] = (new Generic())->getActiveTeamId();
        if (! $input['team_listed_for']) {
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
     * @return \Illuminate\Http\Response
     */
    public function show(TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamListedCandidate $teamListedCandidate)
    {
        //
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyByCandidate(Request $request)
    {
        $userId = self::getUserId();

        try {
            $candidate = $this->candidateRepository->findOneByProperties(
                [
                    'user_id' => $userId,
                ]
            );

            if (! $candidate) {
                // to remove candidate from team-listed-candidates listed by representative
                $candidate = $this->representativeInformationRepository->findOneByProperties([
                    'user_id' => $userId,
                ]);
            }

            if (! $candidate) {
                throw (new ModelNotFoundException)->setModel(
                    get_class($this->candidateRepository->getModel()),
                    $userId
                );
            }

            /* Get Active Team instance */
            $activeTeamId = (new Generic())->getActiveTeamId();
            if (! $activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }

            $candidate->teamList()->wherePivot('team_listed_for', $activeTeamId)->detach($request->user_id);

            return $this->sendSuccessResponse([], 'Candidate remove from shortlist successfully!', [], HttpStatusCode::CREATED->value);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
