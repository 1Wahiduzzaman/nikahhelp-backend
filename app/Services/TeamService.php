<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Requests\TeamFromRequest;
use App\Models\CandidateInformation;
use App\Models\Generic;
use App\Models\Team;
use App\Models\TeamChat;
use App\Models\TeamConnection;
use App\Models\TeamListedCandidate;
use App\Models\TeamMember;
use App\Models\TeamMemberInvitation;
use App\Models\TeamPrivateChat;
use App\Models\TeamToTeamMessage;
use App\Models\TeamToTeamPrivateMessage;
use App\Transformers\CandidateTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TeamMemberRepository;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use App\Transformers\TeamTransformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\AccessRulesDefinitionService;
use Illuminate\Support\Carbon;

class TeamService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;

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
        CandidateTransformer $candidateTransformer
    )
    {
        $this->teamRepository = $teamRepository;
        $this->teamTransformer = $teamTransformer;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->userRepository = $userRepository;
        $this->candidateTransformer = $candidateTransformer;
    }


    /**
     * Update resource
     * @param Request $request
     * @return JsonResponse
     */
    public function save($request): JsonResponse
    {
        $userInfo = self::getUserInfo();
        $countTeamList = $this->teamRepository->findByProperties(["created_by" => $userInfo->id, 'status' =>1]);
        if (count($countTeamList) >= env('CANDIDATE_TEAM_CREATE_LIMIT') && $userInfo->account_type == 1) {
            $createLimit = env('CANDIDATE_TEAM_CREATE_LIMIT');
            return $this->sendErrorResponse("Your maximum team create permission is $createLimit", [], HttpStatusCode::BAD_REQUEST);
        }

        if (count($countTeamList) >= env('REPRESENTATIVE_TEAM_CREATE_LIMIT') && $userInfo->account_type == 2) {
            $createLimit = env('REPRESENTATIVE_TEAM_CREATE_LIMIT');
            return $this->sendErrorResponse("Your maximum team create permission is $createLimit", [], HttpStatusCode::BAD_REQUEST);
        }
        if (count($countTeamList) >= env('MATCHMAKER_TEAM_CREATE_LIMIT') && $userInfo->account_type == 4) {
            $createLimit = env('MATCHMAKER_TEAM_CREATE_LIMIT');
            return $this->sendErrorResponse("Your maximum team create permission is $createLimit", [], HttpStatusCode::BAD_REQUEST);
        }
        try {
            $data = $request->all();
            $data[Team::TEAM_ID] = Str::uuid();
            $data[Team::CREATED_BY] = Auth::id();
            $data['member_count'] = 1;

            $team = $this->teamRepository->save($data);
            $data = $this->teamTransformer->transform($team);
            $team_id = $data['id'];

            // Process team logo image
            // if ($request->hasFile('logo')) {
            //     $logo_url = $this->singleImageUploadFile($team_id, $request->file('logo'));
            //     $team->logo = $logo_url['image_path'];
            // }

            if ($request->hasFile('logo')) {
                $image = $this->uploadImageThrowGuzzle([
                    'logo'=>$request->file('logo')
                ]);
                $team->logo = $image->logo;
            }

            // Update logo url
            $input = (array)$team;
            // As BaseRepository update method has bug that's why we have to fallback to model default methods.
            $input = $team->fill($input)->toArray();
            $team->save($input);


            // Automatically add the user in team as member
            // $role = get role

            $user_id = $data['created_by']['id'];

            $user_type = $this->getRoleForNewTeamMember($user_id);
            $team_member = array();
            $team_member['team_id'] = $team_id;
            $team_member['user_id'] = $user_id;
            $team_member['user_type'] = $request->user_type;
            $team_member['role'] = "Owner+Admin";
            $team_member['status'] =1;
            // $team_member['relationship'] ='Own';
            $team_member['relationship'] = $request->relationship;

            $newmember = $this->teamMemberRepository->save($team_member);

            return $this->sendSuccessResponse($data, 'Team created Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
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

        $getUserType=$this->userRepository->findOneByProperties(
            ['id'=>$user_id]
        );

        if($getUserType->account_type==1) {
            // Check if the user is a candidate in any team
            $checkCandidate = $this->teamMemberRepository->findOneByProperties([
                'user_id' => $user_id,
                'user_type' => 'Candidate'
            ]);

            if (!$checkCandidate) {
                // if No join as Candidate
                return "Candidate";

            }

            // Join as Representative
            return "Representative";
        } else if($getUserType->account_type==3) {
            // Join as Matchmaker
            return  "Matchmaker";
        }else{
            // Join as Representative
            return "Representative";
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
                //get team list and created by information add
                $team_infos = Team::select("*")
                    ->with(["team_members" => function($q){
                        $q->with(['user' => function($u){
                            $u->with(['candidate_info' => function($c){
                                $c->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            }]);
                        }]);
                    }])                   
                    ->with('team_invited_members', 'TeamlistedShortListed','teamRequestedConnectedList','teamRequestedAcceptedConnectedList','created_by')
                    ->with(["last_subscription"=> function($q){
                        $q->with(['user' => function($u){
                            $u->with(['candidate_info' => function($c){
                                $c->select(['id', 'user_id', 'per_avatar_url', 'per_main_image_url']);
                            }]);
                        }]);
                        $q->with(['plans']);
                    }])
                    ->whereIn('id', $team_ids)
                    ->where('status', 1)
                    ->get();

                // for ($i = 0; $i < count($team_infos); $i++) {
                //     // logo storage code has a bug. need to solve it first. then will change the location
                //     //$team_infos[$i]->logo = url('storage/' . $team_infos[$i]->logo);
                //     $team_infos[$i]->logo = isset($team_infos[$i]->logo) ? env('IMAGE_SERVER') .'/'. $team_infos[$i]->logo : '';
                // }
                return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
            } else {
                return $this->sendSuccessResponse(array(), 'Data fetched Successfully!');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    public function getTeamInformation($teamId)
    {
        if (empty($teamId)) {
            return $this->sendErrorResponse('Team ID is required.', [], HttpStatusCode::VALIDATION_ERROR);
        }
        try {
            // Get Team Data
            $team = $this->teamRepository->findOneByProperties([
                "team_id" => $teamId
            ]);

            /// Team not found exception throw
            if (!$team) {
                return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
            }

            $team_infos = Team::select("*")
                ->with(["team_members" => function($q){
                    $q->with('candidate_info');
                }])
                ->with('team_invited_members','created_by')
                ->where('team_id', '=', $teamId)
                ->get();
            //$team_infos[0]->logo = isset($team_infos[0]->logo) ? env('IMAGE_SERVER') .'/'. $team_infos[0]->logo : '';
            //$team_infos[0]['logo'] = url('storage/' . $team_infos[0]['logo']);
            return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Get Team list
     * @param Request $request
     * @return JsonResponse
     */
    public function teamEditCheck(Request $request)
    {
        $user_id = Auth::id();
        $team_id = $request->team_id;

        // Get Team Data
        $team = $this->teamRepository->findOneByProperties([
            "team_id" => $team_id,
            "status" => 1
        ]);

        /// Team not found exception throw
        if (!$team) {
            return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
        }

        $team_row_id = $team->id;
        $team_members = $this->teamMemberRepository->findByProperties(
            ["team_id" => $team_row_id]
        );

        // Check team member count != 0
        if (count($team_members) == 0) {
            return $this->sendErrorResponse('There are 0 members in team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // check one candidate and one representative available
        $candidate_id = 0;
        $representative_id = 0;

        foreach ($team_members as $row) {
            if ($row->user_type == "Candidate") {
                $candidate_id = $row->user_id;
            }

            if ($row->user_type == "Representative") {
                $representative_id = $row->user_id;
            }
        }

        if ($candidate_id == 0) {
            return $this->sendErrorResponse('There is no candidate in team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        if ($representative_id == 0) {
            return $this->sendErrorResponse('There is no representative in team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $candidate_user_info = $this->userRepository->findOneByProperties([
            "id" => $candidate_id
        ]);

        if ($candidate_user_info->status == 0) {
            return $this->sendErrorResponse('Candidate is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $representative_user_info = $this->userRepository->findOneByProperties([
            "id" => $representative_id
        ]);

        if ($representative_user_info->status == 0) {
            return $this->sendErrorResponse('Representative is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $data = array();
        $data["team_info"] = $team;
        $data["team_members"] = $team_members;
        return $this->sendSuccessResponse($data, 'Team is ready for edit!');
    }

    /**
     * Team turn on
     * @param Request $request
     * @return JsonResponse
     */
    public function teamTurnOn(Request $request)
    {
        $team_id = $request->team_id;

        // Get Team Data
        $team = $this->teamRepository->findOneByProperties([
            "id" => $team_id,
           // "status" => 1
        ]);
        /// Team not found exception throw
        if (!$team) {
            return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
        }

        $subscription_expire_at = $team->subscription_expire_at;
        if ($subscription_expire_at == "") {
            return $this->sendErrorResponse('You have not choosen any subscription plan for this team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $current_date = Carbon::now()->toDateString();
        $checksubscription = DB::table('teams')
            ->where('id', '=', $team_id)
            ->where('subscription_expire_at', '>', $current_date)
            ->get();

        if (count($checksubscription) == 0) {
            return $this->sendErrorResponse('Your subscription plan has expired.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        //Update Active Team Info

        TeamMember::where('team_id','<>', $team->id)
        ->where('user_id', Auth::id())
        ->update(['status' => 0]);

        //update by 1
        TeamMember::where('team_id', $team->id)
        ->where('user_id', Auth::id())
        ->update(['status' => 1]);

        // Get Team info for response
        // In future we may need to send notification and messages regarding the team as well
        $team_infos = Team::select("*")
            ->with("team_members", 'team_invited_members')
            ->where('id', $team_id)
            ->where('status', 1)
            ->get();
        return $this->sendSuccessResponse($team_infos, 'Team is ready to turn on!');
    }

    /**
     * @param $teamId
     * @return JsonResponse
     */
    public function checkTeamActiveStatus($teamId)
    {
        if (empty($teamId)) {
            return $this->sendErrorResponse('Team ID is required.', [], HttpStatusCode::VALIDATION_ERROR);
        }
        try {
            // Get Team Data
            $team = $this->teamRepository->findOneByProperties([
                "id" => $teamId
            ]);

            /// Team not found exception throw
            if (!$team) {
                return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
            }


            $team_row_id = $team->id;
            $team_members = $this->teamMemberRepository->findByProperties(
                ["team_id" => $team_row_id]
            );

            // Check team member count != 0
            if (count($team_members) == 0) {
                return $this->sendErrorResponse('This team have no members.', [], HttpStatusCode::VALIDATION_ERROR);
            }

            // check one candidate and one representative available
            $candidate_id = 0;
            $representative_id = 0;

            foreach ($team_members as $row) {
                if ($row->user_type == "Candidate") {
                    $candidate_id = $row->user_id;
                }

                if ($row->user_type == "Representative") {
                    $representative_id = $row->user_id;
                }
            }

            if ($candidate_id == 0) {
                return $this->sendErrorResponse('There is no candidate in team.', [], HttpStatusCode::VALIDATION_ERROR);
            }

            if ($representative_id == 0) {
                return $this->sendErrorResponse('There is no representative in team.', [], HttpStatusCode::VALIDATION_ERROR);
            }

            $candidate_user_info = $this->userRepository->findOneByProperties([
                "id" => $candidate_id
            ]);

            if ($candidate_user_info->status == 0) {
                return $this->sendErrorResponse('Candidate is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
            }

            $representative_user_info = $this->userRepository->findOneByProperties([
                "id" => $representative_id
            ]);

        if ($representative_user_info->status == 0) {
            return $this->sendErrorResponse('Representative is not verified.', [], HttpStatusCode::VALIDATION_ERROR);
        }

            $data = array();
            $data["team_info"] = $team;
            $data["team_members"] = $team_members;
            return $this->sendSuccessResponse($data, 'Team is ready to active ');

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * Delete Team
     * @param Request $request
     * @return JsonResponse
     * receivng string team_id not PK
     */
    public function deleteTeam(Request $request)
    {
        $user_id = Auth::id();
        $team_id = $request->team_id;
        $team_password = $request->team_password;
        $notify_members = $request->false;

        // Get Team
        $team = $this->teamRepository->findOneByProperties([
            "team_id" => "$team_id"
        ]);
        if (!$team) {
            return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
        }

        if ($team->password != $team_password) {
            return $this->sendErrorResponse('Incorrect team password.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Get User status
        $team_member = $this->teamMemberRepository->findOneByProperties([
            "team_id" => $team->id,
            "user_id" => $user_id
        ]);

        if (!$team_member) {
            return $this->sendErrorResponse('You are not a member of this team.', [], HttpStatusCode::NOT_FOUND);
        }

        // Access rule check
        $access_rules = new AccessRulesDefinitionService();
        $delete_team_rights = $access_rules->hasDeleteTeamRights();
        if (!in_array($team_member->role, $delete_team_rights)) {
            return $this->sendErrorResponse("You dont have rights to delete this team.", [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Delete Team
        $team->status = 0;
        $input = (array)$team;
        $input = $team->fill($input)->toArray();
        $team->save($input);

        // For now members and invitations are not deleted. Will delete after notification.

        if ($notify_members) {
            // Notify members will be done after notification module is done.

        }

        //Delete Associated data | By Raz  // using pk as $team->id
        /**
         * Invitation Data delete
         * Member Delete
         * Connection Delete
         * Team Chat Delete
         * Team Private Chat Delete
         */
        TeamConnection::where('from_team_id', $team->id)->orWhere('to_team_id', $team->id)->delete();
        TeamMemberInvitation::where('team_id', $team->id)->delete();
        TeamMember::where('team_id', $team->id)->delete();
        TeamChat::where('from_team_id', $team->id)->orWhere('to_team_id', $team->id)->delete();
        TeamToTeamMessage::where('from_team_id', $team->id)->orWhere('to_team_id', $team->id)->delete();
        TeamPrivateChat::where('from_team_id', $team->id)->orWhere('to_team_id', $team->id)->delete();
        TeamToTeamPrivateMessage::where('from_team_id', $team->id)->orWhere('to_team_id', $team->id)->delete();
        TeamListedCandidate::where('team_listed_for', $team->id)->delete();


        // Send response
        return $this->sendSuccessResponse([], "Team successfully deleted.");
    }

    /**
     * @param Request $request
     * @return array
     */
    private function singleImageUploadFile($team_id, $requestFile, $imageType = null)
    {
        $image_type = 'gallery';
        $file = 'team-logo-' . $team_id;
        $disk = config('filesystems.default', 'local');
        $status = $requestFile->storeAs($file, $image_type . '-' . $requestFile->getClientOriginalName(), $disk);
        return [
            "image_path" => $status,
            "image_disk" => $disk
        ];
    }

    public function teamUpdate($request, $id)
    {
        if (empty($id)) {
            return $this->sendErrorResponse('Please select what you want to edit', [], FResponse::HTTP_BAD_REQUEST);
        }
        try {
            $team = $this->teamRepository->findOrFail($id);
            if (!empty($team->id)) {
                $hashedPassword = $team->password;

                if (!empty($request['name'])) {
                    $team->name = $request['name'];
                }

                if (!empty($request['name'])) {
                    $team->description = $request['description'];
                }
                if (!empty($request['old_password'])) {
                    if ($request['old_password'] == $hashedPassword) {
                        $team->password = $request['new_password'];
                    }
                }
                // Process team logo image
                if ($request->hasFile('logo')) {
                    // $logo_url = $this->singleImageUploadFile($team->id, $request->file('logo'));
                    // $team->logo = $logo_url['image_path'];

                    $image = $this->uploadImageThrowGuzzle([
                        'logo'=>$request->file('logo')
                    ]);
                    $team->logo = $image->logo;
                }
                $team->update();

            }
            // if (!empty($team->logo)) {
            //     $team->logo = isset($team->logo) ? env('IMAGE_SERVER') .'/'. $team->logo : '';
            //     //$team->logo = url('storage/' . $team->logo);
            // }
            return $this->sendSuccessResponse($team, 'Successfully updated', [], HttpStatusCode::SUCCESS);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->sendErrorResponse($ex->getMessage(), [], HttpStatusCode::BAD_REQUEST);
        }

    }


    //Admin
    public function getTeamListForBackend(array $data): JsonResponse
    {
        try {
            $team_infos = Team::with('created_by')->where('status',1)->paginate();

            // for ($i = 0; $i < count($team_infos); $i++) {
            //     $team_infos[$i]->logo = isset($team_infos[$i]->logo) ? env('IMAGE_SERVER') .'/'. $team_infos[$i]->logo : '';
            // }
            return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getDeletedTeamListForBackend(array $data): JsonResponse
    {
        try {
            $team_infos = Team::with('created_by')->where('status',0)->paginate();

            // for ($i = 0; $i < count($team_infos); $i++) {
            //     $team_infos[$i]->logo = isset($team_infos[$i]->logo) ? env('IMAGE_SERVER') .'/'. $team_infos[$i]->logo : '';
            // }
            return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function getConnectedTeamListForBackend($team_id = null): JsonResponse
    {
        try {
            $team_infos = TeamConnection::where('from_team_id', $team_id)->orWhere('to_team_id', $team_id)
            ->with(['requested_by_user', 'responded_by_user'])->paginate();

            // for ($i = 0; $i < count($team_infos); $i++) {
            //     $team_infos[$i]->logo = isset($team_infos[$i]->logo) ? env('IMAGE_SERVER') .'/'. $team_infos[$i]->logo : '';
            // }
            return $this->sendSuccessResponse($team_infos, 'Data fetched Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function adminTeamDelete($data) {
        try{
            Team::where('id', $data->id)->update(['status'=> 0]);
            return $this->sendSuccessResponse([], 'Team deleted Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function candidateOfTeam()
    {
        try {
            $activeTeamId = Generic::getActiveTeamId();

            if (!$activeTeamId) {
                throw new Exception('Team not found, Please create team first');
            }

            $activeTeam = $this->teamRepository->findOneByProperties([
                'id' => $activeTeamId
            ]);

            $candidateOfTeam = $activeTeam->candidateOfTeam() ;

            if(!$candidateOfTeam){
                throw new Exception('Team has no candidate, please join candidate first');
            }

            $personal_info = $this->candidateTransformer->transformPreference($candidateOfTeam);
            $personal_info['personal']['per_gender_id'] = $candidateOfTeam->per_gender;
            return $this->sendSuccessResponse($personal_info, 'Get Candidate of team successfully');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

}
