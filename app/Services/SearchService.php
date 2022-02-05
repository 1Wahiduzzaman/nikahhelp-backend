<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\CandidateImage;
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
     * @param Request $request
     * @return Response
     */
    public function filter($request)
    {
        try {

            $userInfo = [];

            /*Attempt log in */
            try {
                JWTAuth::parseToken()->authenticate();
            }catch (\Exception $e){
            }

            $userInfo['shortList'] = [];
            $userInfo['blockList'] = [];
            $userInfo['teamList'] = [];
            $connectFrom = [];
            $connectTo = [];
            $userInfo['connectList'] = [];

            $candidates = $this->candidateRepository->getModel();

            /* FILTER - Candidate fill at least personal info  */
            $candidates = $candidates->where('data_input_status','>',2); // collect candidate with at list personal info given

            if(Auth::check()){
                $userId = self::getUserId();
                $loggedInCandidate = $this->candidateRepository->findOneByProperties([
                    'user_id' => $userId
                ]);

                $activeTeamId = Generic::getActiveTeamId();

                if (!$activeTeamId) {
                    throw new Exception('Team not found, Please create team first');
                }

                $activeTeam = $this->teamRepository->findOneByProperties([
                    'id' => $activeTeamId
                ]);

                $userInfo['shortList'] = $loggedInCandidate->shortList->pluck('user_id')->toArray();
                $userInfo['teamList'] = $activeTeam->teamListedUser->pluck('id')->toArray();
                $userInfo['blockList'] = $loggedInCandidate->blockList->pluck('user_id')->toArray();
                $connectFrom = $activeTeam->sentRequest->pluck('team_id')->toArray();
                $connectTo = $activeTeam->receivedRequest->pluck('team_id')->toArray();
                $userInfo['connectList'] = array_unique (array_merge($connectFrom,$connectTo)) ;

                /* FILTER - Own along with team member and blocklist candidate  */
                $activeTeamUserIds = $activeTeam->team_members->pluck('user_id')->toArray();
                $exceptIds = array_merge($userInfo['blockList'],$activeTeamUserIds);
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

            $candidates = $candidates->with('getNationality','getReligion','candidateTeam')->paginate($parPage);

            if(!count($candidates->items())){
                return $this->sendErrorResponse('No Candidates Match Found', [], HttpStatusCode::SUCCESS);
            }

            $candidatesResponse = [];

            foreach ($candidates as $candidate) {
                /* Include additional info */
                $candidate->is_short_listed = in_array($candidate->user_id,$userInfo['shortList']);
                $candidate->is_block_listed = in_array($candidate->user_id,$userInfo['blockList']);
                $candidate->is_teamListed = in_array($candidate->user_id,$userInfo['teamList']);
                $teamId = $candidate->active_team ? $candidate->active_team->team_id : null;
                $candidate->is_connect = in_array($teamId,$userInfo['connectList']);
                $candidate->team_id = $teamId;

                /* Find Team Connection Status (We Decline or They Decline )*/
                if(in_array($teamId,$connectFrom)){
                    $connectionRequestSendType = 1;
                    $teamConnectStatus = TeamConnection::where('from_team_id',$activeTeam->id)->where('to_team_id',$teamId)->first();
                    $teamConnectStatus = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
                }elseif (in_array($teamId,$connectTo)){
                    $connectionRequestSendType = 2;
                    $teamConnectStatus = TeamConnection::where('from_team_id',$teamId)->where('to_team_id',$activeTeam->id)->first();
                    $teamConnectStatus = $teamConnectStatus ? $teamConnectStatus->connection_status : null;
                }else{
                    $connectionRequestSendType = null;
                    $teamConnectStatus = null;
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

    /**
     * @param $queryData
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
