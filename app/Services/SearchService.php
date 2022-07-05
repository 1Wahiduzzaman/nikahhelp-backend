<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Requests\Search\CreateSearchAPIRequest;
use App\Models\CandidateImage;
use App\Models\Country;
use App\Models\Generic;
use App\Models\Team;
use App\Models\TeamConnection;
use App\Transformers\CandidateTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TeamMemberRepository;
use Illuminate\Support\Facades\Auth;
use App\Transformers\TeamTransformer;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var BlockListService
     */
    protected $blockListService;
    /**
     * @var CandidateRepository
     */
    protected $candidateRepository;

    /**
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * @var TeamTransformer
     */
    protected $teamTransformer;
    /**
     * @var CandidateTransformer
     */
    private $candidateTransformer;

    /**
     * TeamService constructor.
     *
     * @param TeamRepository $teamRepository
     */


    public function __construct(
        TeamRepository $teamRepository,
        TeamTransformer $teamTransformer,
        TeamMemberRepository $teamMemberRepository,
        UserRepository $userRepository,
        CandidateRepository $candidateRepository,
        BlockListService $blockListService,
        CandidateTransformer $candidateTransformer
    )
    {
        $this->teamRepository = $teamRepository;
        $this->teamTransformer = $teamTransformer;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
        $this->candidateRepository = $candidateRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($candidateRepository);
        $this->candidateTransformer = $candidateTransformer;
    }


    /**
     * Update resource
     * @param CreateSearchAPIRequest $request
     * @return Response
     */
    public function filter(CreateSearchAPIRequest $request)
    {
        try {

            $members = $this->candidateRepository->getModel()->with(['user' => function($query) {
                $query->where('status', '3');
            }])->with('getReligion')
                ->with('getCurrentResidenceCountry')
                ->where('per_gender', (string)$request->input('gender'))
                ->where('per_current_residence_country', (string)$request->input('country'))
                ->where('per_religion_id', (string)$request->input('religion'))
                ->get()
                ->filter(function ($candidate) {
                    return $candidate->dob != null;
                })
                ->filter(static function ($candidate) use ($request) {
                    $min_age = (int)$request->input('min_age');
                    $max_age = (int)$request->input('max_age');

                    $date_of_birth = new Carbon($candidate->dob);

                    return $date_of_birth->diffInYears(now()) >= $min_age &&
                        $date_of_birth->diffInYears(now()) <= $max_age;
                });


            if ($members->count() === 0) {
                return $this->sendErrorResponse("No Candidates found", "no candidates", HttpStatusCode::NOT_FOUND);
            }
            return $this->sendSuccessResponse($members, "Candidates fetched successfully");

            $candidates = $this->candidateRepository->getModel();

            /* FILTER - Candidate Must be verified  */
            $candidates = $candidates->whereHas('user',function ($q){
                $q->where('status',3);
            });

            if(Auth::check()){

                $userId = self::getUserId();
                $loggedInCandidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $userId
                ]);

                $activeTeam = $loggedInCandidate->active_team;

                if (!$activeTeam) {
                    throw new Exception('Team not found, Please create team first');
                }

                $userInfo['shortList'] = $loggedInCandidate->shortList->pluck('user_id')->toArray();
                $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
                $userInfo['blockList'] = $loggedInCandidate->blockList->pluck('user_id')->toArray();
                $connectFrom = $activeTeam->sentRequest->pluck('team_id')->toArray();
                $connectTo = $activeTeam->receivedRequest->pluck('team_id')->toArray();
                $userInfo['connectList'] = array_unique(array_merge($connectFrom,$connectTo)) ;

                /* FILTER - Own along with team member and block list candidate  */
                $activeTeamUserIds = $activeTeam->team_members->pluck('user_id')->toArray();

                /* FILTER - Remove Team users already in connected list (pending, connected or rejected)  */
                $connectFromMembersId = $activeTeam->sentRequestMembers->pluck('user_id')->toArray();
                $connectToMembersId = $activeTeam->receivedRequestMembers->pluck('user_id')->toArray();

                $exceptIds = array_unique(array_merge($userInfo['blockList'],$activeTeamUserIds,$connectFromMembersId,$connectToMembersId));
                $candidates = $candidates->whereNotIn('user_id',$exceptIds);

                /* FILTER - Country not preferred  */
                $candidates = $candidates->whereNotIn('per_current_residence_country',$loggedInCandidate->bloked_countries->pluck('id')->toArray());
            }

            /* FILTER - Gender  */
            if (isset($request->gender)) {
                $candidates = $candidates->where('per_gender', $request->gender);
            }

            /* FILTER - Age  */
            if (isset($request->min_age) && isset($request->max_age)) {
                $dateRange['max'] = Carbon::now()->subYears($request->max_age);
                $dateRange['min'] = Carbon::now()->subYears($request->mim_age);

                $candidates = $candidates->whereBetween('dob', [$dateRange]);
            }

            /* FILTER - Gender  */
            if (isset($request->country)) {
                $candidates = $candidates->where('per_current_residence_country', $request->country);
            }

            /* FILTER - Religion  */
            if (isset($request->religion)) {
                $candidates = $candidates->where('per_religion_id', $request->religion);
            }

            /* FILTER - Height  */
            if(isset($request->min_height) && isset($request->max_height)){
                $heightRange['min'] = $request->min_height;
                $heightRange['max'] = $request->max_height;
                $candidates = $candidates->whereBetween('per_height', [$heightRange]);
            }

            /* FILTER - Ethnicity  */
            if(isset($request->ethnicity)){
                $candidates = $candidates->where('per_ethnicity', $request->ethnicity);
            }

            /* FILTER - Marital Status  */
            if(isset($request->marital_status)){
                $candidates = $candidates->where('per_marital_status', $request->marital_status);
            }

            /* FILTER - Employment Status  */
            if(isset($request->employment_status)){
                $candidates = $candidates->where('pre_employment_status', $request->employment_status);
            }

            /* FILTER - Occupation */
            if(isset($request->per_occupation)){
                $candidates = $candidates->where('per_occupation', $request->per_occupation);
            }

            /* FILTER - Education Level */
            if(isset($request->education_level_id)){
                $candidates = $candidates->where('per_education_level_id', $request->education_level_id);
            }

            /* FILTER - Mother Tongue */
            if(isset($request->mother_tongue)){
                $candidates = $candidates->where('per_mother_tongue', $request->mother_tongue);
            }

            /* FILTER - Nationality */
            if(isset($request->nationality)){
                $candidates = $candidates->where('per_nationality', $request->nationality);
            }

            /* FILTER - Current Residence */
            if(isset($request->current_residence_country)){
                $candidates = $candidates->where('per_current_residence_country', $request->country);
            }

            /* FILTER - Currently Living With */
            if(isset($request->currently_living_with)){
                $candidates = $candidates->where('per_currently_living_with', $request->currently_living_with);
            }

            /* FILTER - Smoker status */
            if(isset($request->smoker)){
                $candidates = $candidates->where('per_smoker ', $request->smoker);
            }

            /* FILTER - Hobbies Interests */
            if(isset($request->hobbies_interests)){
                $candidates = $candidates->where('per_hobbies_interests', $request->smoker);
            }

            $parPage = $request->input('parpage',10);

            $candidates = $candidates->with('getNationality','getReligion','candidateTeam','activeTeams','activeTeams.team_members')->paginate($parPage);

            if(!count($candidates->items())){
                return $this->sendErrorResponse('No Candidates Match Found', [], HttpStatusCode::SUCCESS);
            }

            $candidatesResponse = [];
            foreach ($candidates as $candidate) {
                /* Include additional info */
                $candidate->is_short_listed = in_array($candidate->user_id,$userInfo['shortList']);
                $candidate->is_block_listed = in_array($candidate->user_id,$userInfo['blockList']);
                $candidate->is_teamListed = in_array($candidate->user_id,$userInfo['teamList']);

                /* Set Candidate Team related info */
                $teamId = null;
                $teamTableId = '';
                if($candidate->active_team){
                    $teamId = $candidate->active_team->team_id;
                    $teamTableId = $candidate->active_team->id;
                    $candidate->team_info = [
                        'team_id' => $candidate->active_team->id,
                        'name' => $candidate->active_team->name,
                        'members_id' => $candidate->active_team->team_members->pluck('user_id')->toArray(),
                    ];
                }
                $candidate->team_id = $teamId;

                /* Set Auth Team related info */
                $connectionRequestSendType = null;
                $teamConnectStatus = null;
                if($activeTeam){
                    $candidate->is_connect = $activeTeam->connectedTeam($teamTableId) ? $activeTeam->connectedTeam($teamTableId)->id : null;

                    /* Find Team Connection Status (We Decline or They Decline )*/
                    if(in_array($teamId,$connectFrom)){
                        $connectionRequestSendType = 1;
                        $teamConnectStatus = TeamConnection::where('from_team_id',$activeTeam->id)->where('to_team_id',$candidate->active_team->id)->first();
                        $teamConnectStatus = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
                    }elseif (in_array($teamId,$connectTo)){
                        $connectionRequestSendType = 2;
                        $teamConnectStatus = TeamConnection::where('from_team_id',$candidate->active_team->id)->where('to_team_id',$activeTeam->id)->first();
                        $teamConnectStatus = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
                    }
                }
                $candidate->connectionRequestSendType = $connectionRequestSendType;
                $candidate->teamConnectStatus = $teamConnectStatus;

                $candidatesResponse[] = array_merge(
                    $this->candidateTransformer->transformSearchResult($candidate),
                    [
                        'personal' => $this->candidateTransformer->transform($candidate)['personal']
                    ],
                    [
                        'preference' => $this->candidateTransformer->transform($candidate)['preference']
                    ],
                );
            }

            $searchResult['data'] = $candidatesResponse;
            $searchResult['pagination'] = $this->paginationResponse($candidates);

            return $this->sendSuccessResponse($searchResult, "Candidates fetched successfully");

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    public function searchCandidates(\App\Http\Requests\CandidateSearch $request)
    {

        try {
            $searchedCandidates = $this->candidateRepository->getModel()->with(['user' => function($query) use ($request) {
                $query->where('status', '3');
            }])->where('per_gender', $request->input('per_gender'))
                ->where('per_religion_id', $request->input('per_religion_id'))
                ->where('per_country_id', $request->input('country'))
                ->where('per_ethnicity', $request->input('ethnicity'))
                ->where('per_marital_status', $request->input('marital_status'))
                ->where('per_residence_country', $request->input('nationality'))
                ->where('per_occupation', $request->input('employment_status'))
                ->get()
                ->filter(function ($candidate) use ($request) {
                    $min_age = (int)$request->input('min_age');
                    $max_age = (int)$request->input('max_age');

                    $date_of_birth = new Carbon($candidate->dob);

                    return $date_of_birth->diffInYears(now()) >= $min_age &&
                        $date_of_birth->diffInYears(now()) <= $max_age;
                });

            return $this->sendSuccessResponse($searchedCandidates->toArray(), HttpStatusCode::SUCCESS);
        } catch (Exception $exception)
        {
            $this->sendErrorResponse($exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }




    }

    /**
     * @param $queryData
     * @return array
     */
    protected function pagination($queryData)
    {
        $data = [
            'total_items' => $queryData->count(),
            'current_items' => $queryData->count(),
            'first_item' => $queryData->first(),
            'last_item' => $queryData->last(),
        ];
        return $data;
    }


    /**
     * Team Login
     * @param Request $request
     * @return JsonResponse
     */
    public function login($request)
    {
        $team_id = $request->team_id;
        $password = $request->password;

        try {
            $team = $this->teamRepository->findOneByProperties(
                [
                    "team_id" => $team_id
                ]
            );

            if (!$team) {
                return $this->sendErrorResponse('Team is Not found.', [], HttpStatusCode::NOT_FOUND);
            }

            if ($team->password == $password) {
                return $this->sendSuccessResponse($team, "Login successful.");
            } else {
                return $this->sendErrorResponse('Password incorrect.', [], HttpStatusCode::NOT_FOUND);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * Determine role for new team member
     * @param int $user_id
     * @return Str
     */
    public function getRoleForNewTeamMember(int $user_id): string
    {
        // Check if the user is a candidate in any team
        $checkCandidate = $this->teamMemberRepository->findOneByProperties([
            'user_id' => $user_id,
            'role' => 'Candidate'
        ]);

        if (!$checkCandidate) {
            // if No join as Candidate
            return "Candidate";

        }

        // Join as Representative
        return "Representative";
    }

    /**
     * Get Team list
     * @param array $data
     * @return JsonResponse
     */
    public function getTeamList(array $data): JsonResponse
    {
        $user_id = Auth::id();
        try {
            $team_list = $this->teamMemberRepository->findByProperties([
                "user_id" => $user_id
            ]);

            if (count($team_list) > 0) {
                $team_ids = array();
                foreach ($team_list as $row) {
                    array_push($team_ids, $row->team_id);
                }

                $team_infos = Team::select("*")
                    ->with("team_members")
                    ->whereIn('id', $team_ids)
                    ->where('status', 1)
                    ->get();

                for ($i = 0; $i < count($team_infos); $i++) {
                    // logo storage code has a bug. need to solve it first. then will change the location
                    $team_infos[$i]->logo = url('storage/' . $team_infos[$i]->logo);
                }

                return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
            } else {
                return $this->sendSuccessResponse(array(), 'Data fetched Successfully!');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


}
