<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\Team;
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
        BlockListService $blockListService
    )
    {
        $this->teamRepository = $teamRepository;
        $this->teamTransformer = $teamTransformer;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
        $this->candidateRepository = $candidateRepository;
        $this->blockListService = $blockListService;
        $this->setActionRepository($candidateRepository);
    }


    /**
     * Update resource
     * @param Request $request
     * @return Response
     */
    public function filter($request)
    {
        //first priority gender
        //second priority age
        //third priority country
        //fourth priority religious

        try {
            $candidates = $this->candidateRepository->getModel()->get();


/*            $candidates = $this->candidateRepository->where(function($query) use ($request) {
                $query->where('per_gender', $request->get('gender'))
                    ->where('dob', Carbon::parse($request->get('min_age'))->diff(Carbon::now()))
                    ->orWhere('dob', Carbon::parse($request->get('max_age'))->diff(Carbon::now()))
                    ->where('country', $request->get('country'))
                    ->where('religion', $request->get('religion'));

                })->orWhere('dob',Carbon::parse($request->get('min_age'))->diff(Carbon::now()))
                ->orWhere('dob', Carbon::parse($request->get('max_age'))->diff(Carbon::now()))
                ->orWhere('per_gender', $request->get('gender'))
                ->orWhere('country', $request->get('country'))
                ->orWhere('religion', $request->get('religion'))
                ->get();*/

            if(count($candidates) === 0) {
                return $this->sendErrorResponse('No Candidates Registered', [], HttpStatusCode::NOT_FOUND);
            }

            return $this->sendSuccessResponse($candidates, "Candidates fetched successfully");
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
