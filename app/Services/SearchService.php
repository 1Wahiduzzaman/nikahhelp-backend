<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\Team;
use App\Models\TeamConnection;
use App\Repositories\CandidateRepository;
use App\Repositories\RepresentativeInformationRepository;
use App\Repositories\TeamMemberRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use App\Traits\CrudTrait;
use App\Transformers\CandidateSearchTransformer;
use App\Transformers\CandidateTransformer;
use App\Transformers\TeamTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
// use phpDocumentor\Reflection\Types\Null_;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SearchService extends ApiBaseService
{
    use CrudTrait;

    protected \App\Transformers\CandidateSearchTransformer $searchTransformer;

    protected \App\Repositories\UserRepository $userRepository;

    protected \App\Services\BlockListService $blockListService;

    protected \App\Repositories\CandidateRepository $candidateRepository;

    protected \App\Repositories\TeamMemberRepository $teamMemberRepository;

    protected \App\Repositories\RepresentativeInformationRepository $representativeRepository;

    protected \App\Repositories\TeamRepository $teamRepository;

    protected \App\Transformers\TeamTransformer $teamTransformer;

    private \App\Transformers\CandidateTransformer $candidateTransformer;

    /**
     * TeamService constructor.
     */
    public function __construct(
        TeamRepository $teamRepository,
        TeamTransformer $teamTransformer,
        TeamMemberRepository $teamMemberRepository,
        UserRepository $userRepository,
        CandidateRepository $candidateRepository,
        BlockListService $blockListService,
        CandidateTransformer $candidateTransformer,
        RepresentativeInformationRepository $representativeInformationRepository,
        CandidateSearchTransformer $searchTransformer
    ) {
        $this->teamRepository = $teamRepository;
        $this->teamTransformer = $teamTransformer;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
        $this->candidateRepository = $candidateRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($candidateRepository);
        $this->candidateTransformer = $candidateTransformer;
        $this->representativeRepository = $representativeInformationRepository;
        $this->searchTransformer = $searchTransformer;
    }

    /**
     * Update resource
     *
     * @param  Request  $request
     * @return Response
     */
    public function filter($request)
    {
        try {

            $userInfo = [];

            /*Attempt log in */
            try {
                auth()->authenticate();
            } catch (\Exception $e) {
            }

            $userInfo['shortList'] = [];
            $userInfo['blockList'] = [];
            $userInfo['teamList'] = [];
            $connectFrom = [];
            $connectTo = [];
            $userInfo['connectList'] = [];
            $activeTeam = '';

            $candidates = $this->candidateRepository->getModel();

            $representative = $this->representativeRepository->getModel();
            /* FILTER - Candidate Must be verified  */
            $candidates = $candidates->whereHas('user', function ($q) {
                $q->where('status', 3);
            });

            // $candidates->whereHas('candidateTeam')

            if (Auth::check()) {

                $userId = self::getUserId();
                $loggedInRepresentative = null;
                $loggedInCandidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $userId,
                ]);

                if ($loggedInCandidate) {
                    $activeTeam = $loggedInCandidate->active_team;
                }

                if (! $loggedInCandidate) {
                    $loggedInRepresentative = $this->representativeRepository->findOneByProperties([
                        'user_id' => $userId,
                    ]);

                    $activeTeam = $loggedInRepresentative->active_team;

                    $loggedInCandidate = $loggedInRepresentative;
                }

                if (! $activeTeam) {
                    throw new Exception('Team not found, Please create team first');
                }
                $shortListedUsers = $activeTeam->teamShortListedUser;
                $shortListedUsers = $shortListedUsers->filter(function ($shortListedUser) use ($userId) {
                    return $shortListedUser->pivot->shortlisted_by == $userId;
                });
                $userInfo['shortList'] = $shortListedUsers->pluck('id')->toArray();
                $userInfo['blockList'] = $loggedInCandidate->blockList->pluck('user_id')->toArray();
                $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();

                //$userInfo['shortList'] = $loggedInCandidate->shortList->pluck('user_id')->toArray();
                // $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
                // $userInfo['blockList'] = $loggedInCandidate->blockList->pluck('user_id')->toArray();
                $connectFrom = $activeTeam->sentRequest->pluck('team_id')->toArray();
                $connectTo = $activeTeam->receivedRequest->pluck('team_id')->toArray();
                $userInfo['connectList'] = array_unique(array_merge($connectFrom, $connectTo));

                /* FILTER - Own along with team member and block list candidate  */
                $activeTeamUserIds = $activeTeam->team_members->pluck('user_id')->toArray();

                /* FILTER - Remove Team users already in connected list (pending, connected or rejected)  */
                $connectFromMembersId = $activeTeam->sentRequestMembers->pluck('user_id')->toArray();
                $connectToMembersId = $activeTeam->receivedRequestMembers->pluck('user_id')->toArray();

                $exceptIds = array_unique(array_merge(
                    $userInfo['blockList'],
                    $userInfo['teamList'],
                    $userInfo['shortList'],
                    $activeTeamUserIds,
                    $connectFromMembersId,
                    $connectToMembersId
                ));

                $candidates = $candidates->whereNotIn('user_id', $exceptIds);

                /* FILTER - Country not preferred  */
                // $candidates = $candidates->whereNot('per_current_residence_country'/**$loggedInCandidate->bloked_countries->pluck('id')->toArray()**/);
            }

            /* FILTER - Gender  */
            if (isset($request->gender)) {
                $candidates = $candidates->where('per_gender', $request->gender);
            }

            /* FILTER - Age  */
            if (isset($request->min_age) && isset($request->max_age)) {
                $dateRange['max'] = Carbon::now()->subYears($request->max_age);
                $dateRange['min'] = Carbon::now()->subYears($request->min_age);

                $candidates = $candidates->whereBetween('dob', [$dateRange]);
            }

            /* FILTER - Gender  */
            if (isset($request->country)) {
                $candidates = $candidates->where('per_permanent_country', $request->country);
            }

            /* FILTER - Religion  */
            if (isset($request->religion)) {
                $candidates = $candidates->where('per_religion_id', $request->religion);
            }

            /* FILTER - Height  */
            if (isset($request->min_height) && isset($request->max_height)) {
                $heightRange['min'] = $request->min_height;
                $heightRange['max'] = $request->max_height;
                $candidates = $candidates->whereBetween('per_height', [$heightRange]);
            }

            /* FILTER - Ethnicity  */
            if (isset($request->ethnicity)) {
                $candidates = $candidates->where('per_ethnicity', $request->ethnicity);
            }

            /* FILTER - Marital Status  */
            if (isset($request->marital_status)) {
                $candidates = $candidates->where('per_marital_status', $request->marital_status);
            }

            /* FILTER - Employment Status  */
            if (isset($request->employment_status)) {
                $candidates = $candidates->where('per_employment_status', $request->employment_status);
            }

            /* FILTER - Occupation */
            if (isset($request->per_occupation)) {
                $candidates = $candidates->where('per_occupation', $request->per_occupation);
            }

            /* FILTER - Education Level */
            if (isset($request->education_level_id)) {
                $candidates = $candidates->where('per_education_level_id', $request->education_level_id);
            }

            /* FILTER - Mother Tongue */
            if (isset($request->mother_tongue)) {
                $candidates = $candidates->where('per_mother_tongue', $request->mother_tongue);
            }

            /* FILTER - Nationality */
            if (isset($request->nationality)) {
                $candidates = $candidates->where('per_nationality', $request->nationality);
            }

            /* FILTER - Current Residence */
            if (isset($request->current_residence_country)) {
                $candidates = $candidates->where('per_current_residence_country', $request->country);
            }

            /* FILTER - Currently Living With */
            if (isset($request->currently_living_with)) {
                $candidates = $candidates->where('per_currently_living_with', $request->currently_living_with);
            }

            /* FILTER - Smoker status */
            if (isset($request->smoker)) {
                $candidates = $candidates->where('per_smoker ', $request->smoker);
            }

            /* FILTER - Hobbies Interests */
            if (isset($request->hobbies_interests)) {
                $candidates = $candidates->where('per_hobbies_interests', $request->smoker);
            }

            // return response()->json(['msg' => $candidates]);

            $parPage = $request->input('parpage', 10);

            if (Auth::check()) {
                $loggedInCandidateIsBlockedBy = $loggedInCandidate->blockListedBy->pluck('user_id')->toArray();
                $candidate = $candidates->whereNotIn('user_id', $loggedInCandidateIsBlockedBy);
                $candidates = $candidates->whereHas('candidateTeam')->with('getNationality', 'getReligion', 'candidateTeam', 'activeTeams', 'activeTeams.team_members')->paginate($parPage);
            } else {
                $candidates = $candidates->with('getNationality', 'getReligion', 'candidateTeam', 'activeTeams', 'activeTeams.team_members')->paginate($parPage);
            }

            // $caniddateInTeam = $candidates->whereHas('candidateTeam')->get();

            if ($candidates->total() < 1) {
                return $this->sendErrorResponse('No Candidates Match Found', [], HttpStatusCode::NOT_FOUND->value);
            }

            $candidatesResponse = [];
            $candidatesResponseUnAuth = [];
            foreach ($candidates as $key => $candidate) {
                /* Include additional info */
                $candidate->is_short_listed = in_array($candidate->user_id, $userInfo['shortList']);
                $candidate->is_block_listed = in_array($candidate->user_id, $userInfo['blockList']);
                $candidate->is_teamListed = in_array($candidate->user_id, $userInfo['teamList']);

                /* Set Candidate Team related info */
                $teamId = null;
                $teamTableId = '';
                if ($candidate->candidate_team) {
                    $teamId = $candidate->candidate_team->team_id;
                    $teamTableId = $candidate->candidate_team->id;
                    $candidate->team_info = [
                        'team_id' => $candidate->candidate_team->id,
                        'name' => $candidate->candidate_team->name,
                        'members_id' => $candidate->candidate_team->team_members->pluck('user_id')->toArray(),
                    ];
                }
                $candidate->team_id = $teamId;

                /* Set Auth Team related info */
                if (Auth::check()) {
                    $connectionRequestSendType = null;
                    $teamConnectStatus = null;
                    $teamAlreadysentRequest = TeamConnection::where('to_team_id', $candidate->candidate_team->id)->where('from_team_id', $activeTeam->id)->first();
                    $teamConnectRecieved = TeamConnection::where('to_team_id', $activeTeam->id)->where('from_team_id', $candidate->candidate_team->id)->first();

                    if ($teamAlreadysentRequest || $teamConnectRecieved) {
                        Log::info([$teamAlreadysentRequest, $teamConnectRecieved]);

                        continue;
                    }

                    if ($activeTeam) {
                        $candidate->is_connect = $activeTeam->connectedTeam($teamTableId) ? $activeTeam->connectedTeam($teamTableId)->id : null;

                        /* Find Team Connection Status (We Decline or They Decline )*/
                        if (in_array($teamId, $connectFrom)) {
                            $connectionRequestSendType = 1;
                            $teamConnectStatus = TeamConnection::where('from_team_id', $activeTeam->id)->where('to_team_id', $candidate->candidate_team->id)->first();
                            $teamConnectStatus = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
                        } elseif (in_array($teamId, $connectTo)) {
                            $connectionRequestSendType = 2;
                            $teamConnectStatus = TeamConnection::where('from_team_id', $candidate->candidate_team->id)->where('to_team_id', $activeTeam->id)->first();
                            $teamConnectStatus = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
                        }
                    }
                    $candidate->connectionRequestSendType = $connectionRequestSendType;
                    $candidate->teamConnectStatus = $teamConnectStatus;
                }

                $candidatesResponse[] = array_merge(
                    $this->candidateTransformer->transformSearchResult($candidate),
                    [
                        'contact' => $this->searchTransformer->contact($candidate),
                    ],
                    [
                        'personal' => $this->searchTransformer->personal($candidate),
                    ],
                    [
                        'preference' => $this->candidateTransformer->transform($candidate)['preference'],
                    ],
                );

                if (! Auth::check()) {
                    $candidatesResponseUnAuth[] = array_merge([
                        'image' => $candidate->per_avatar_url ? $candidate->per_avatar_url : null,
                        'screen_name' => $candidate->screen_name,
                        'per_gender' => $candidate->per_gender,
                        'per_age' => Carbon::now()->diffInYears($candidate->dob),
                        'per_nationality_id' => $candidate->per_nationality,
                        'per_nationality' => $candidate->getNationality()->exists() ? $candidate->getNationality->name : null,
                        'per_religion_id' => $candidate->per_religion_id,
                        'per_religion' => $candidate->getReligion()->exists() ? $candidate->getReligion->name : null,
                        'per_ethnicity' => $candidate->per_ethnicity,
                        // 'per_hobbies_interests' => $candidate->per_hobbies_interests,
                        'per_occupation' => $candidate->per_occupation,
                        'per_permanent_country_name' => $candidate->getPermanentCountry()->exists() ? $candidate->getPermanentCountry->name : null,

                    ]);
                }
            }

            if (! Auth::check()) {
                $searchResult['data'] = $candidatesResponseUnAuth;
                $searchResult['pagination'] = $this->paginationResponse($candidates);

                return $this->sendSuccessResponse($searchResult, 'Candidates fetched successfully');

            }

            $searchResult['data'] = $candidatesResponse;
            $searchResult['pagination'] = $this->paginationResponse($candidates);

            return $this->sendSuccessResponse($searchResult, 'Candidates fetched successfully');

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR->value);
        }
    }

    /**
     * @return array
     */
    protected function pagination($queryData)
    {
        $data = [
            'total_items' => $queryData->total(),
            'current_items' => $queryData->count(),
            'first_item' => $queryData->firstItem(),
            'last_item' => $queryData->lastItem(),
            'current_page' => $queryData->currentPage(),
            'last_page' => $queryData->lastPage(),
            'has_more_pages' => $queryData->hasMorePages(),
        ];

        return $data;
    }

    /**
     * Team Login
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login($request)
    {
        $team_id = $request->team_id;
        $password = $request->password;

        try {
            $team = $this->teamRepository->findOneByProperties(
                [
                    'team_id' => $team_id,
                ]
            );

            if (! $team) {
                return $this->sendErrorResponse('Team is Not found.', [], HttpStatusCode::NOT_FOUND->value);
            }

            if ($team->password == $password) {
                return $this->sendSuccessResponse($team, 'Login successful.');
            } else {
                return $this->sendErrorResponse('Password incorrect.', [], HttpStatusCode::NOT_FOUND->value);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * Determine role for new team member
     *
     * @return Str
     */
    public function getRoleForNewTeamMember(int $user_id): string
    {
        // Check if the user is a candidate in any team
        $checkCandidate = $this->teamMemberRepository->findOneByProperties([
            'user_id' => $user_id,
            'role' => 'Candidate',
        ]);

        if (! $checkCandidate) {
            // if No join as Candidate
            return 'Candidate';

        }

        // Join as Representative
        return 'Representative';
    }

    /**
     * Get Team list
     */
    public function getTeamList(array $data): JsonResponse
    {
        $user_id = Auth::id();
        try {
            $team_list = $this->teamMemberRepository->findByProperties([
                'user_id' => $user_id,
            ]);

            if (count($team_list) > 0) {
                $team_ids = [];
                foreach ($team_list as $row) {
                    array_push($team_ids, $row->team_id);
                }

                $team_infos = Team::select('*')
                    ->with('team_members')
                    ->whereIn('id', $team_ids)
                    ->where('status', 1)
                    ->get();

                for ($i = 0; $i < count($team_infos); $i++) {
                    // logo storage code has a bug. need to solve it first. then will change the location
                    $team_infos[$i]->logo = url('storage/'.$team_infos[$i]->logo);
                }

                return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
            } else {
                return $this->sendSuccessResponse([], 'Data fetched Successfully!');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
