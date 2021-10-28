<?php


namespace App\Services;

use App\Helpers\Notificationhelpers;
use App\Models\Team;
use App\Enums\HttpStatusCode;
use App\Models\TeamMember;
use App\Transformers\TeamMemberTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use App\Repositories\TeamRepository;
use App\Repositories\TeamMemberRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\AccessRulesDefinitionService;
use App\Services\TeamService;


class TeamMemberService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var TeamService
     */
    protected $teamService;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * @var TeamMemberTransformer
     */
    protected $teamMemberTransformer;

    /**
     * TeamService constructor.
     *
     * @param TeamMemberRepository $teamMemberRepository
     */
    public function __construct(
        TeamMemberRepository $teamMemberRepository,
        TeamMemberTransformer $teamMemberTransformer,
        TeamRepository $teamRepository,
        TeamService $teamService
    )
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamMemberTransformer = $teamMemberTransformer;
        $this->teamRepository = $teamRepository;
        $this->teamService = $teamService;
    }


    /**
     * Update resource
     * @param array $data
     * @return JsonResponse
     */
    public function save(array $data): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $teamMember = $this->teamMemberRepository->save($data);
            $data = $this->teamMemberTransformer->transform($teamMember);

            Notificationhelpers::add('New team member has been added', 'team', $data['team_id'], $userId);
            return $this->sendSuccessResponse($data, 'Information inserted Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Change team member access type
     * @param Request $data
     * @return JsonResponse
     */
    public function changeTeamMemberAccess(array $request)
    {
        $user_id = Auth::id();
        $user_id_for_change_access = $request["user_id"];
        $team_id = $request["team_id"];
        $new_access_type = $request["access_type"];

        // Currently returns static. Later i will take value from table or some other way
        $access_rules = new AccessRulesDefinitionService();
        $valid_roles = $access_rules->getValidRoles();
        if (!in_array($new_access_type, $valid_roles)) {
            return $this->sendErrorResponse("Invalid access type specified.", [], HttpStatusCode::VALIDATION_ERROR);
        }


        $team = $this->teamRepository->findOneByProperties([
            "team_id" => $team_id
        ]);

        // Check team
        if (!$team) {
            return $this->sendErrorResponse('Team not found.', [], HttpStatusCode::NOT_FOUND);
        }

        // Get executer
        $executer_member_info = $this->teamMemberRepository->findOneByProperties(
            [
                "user_id" => $user_id,
                "team_id" => $team->id
            ]
        );

        // Check if executer is a member and has enough access to do this
        if (!$executer_member_info) {
            return $this->sendErrorResponse('You are not a member of this team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        $role_with_change_rights = $access_rules->hasRoleChangeRights();//["Owner+Admin"];
        if (!in_array($executer_member_info->role, $role_with_change_rights)) {
            return $this->sendErrorResponse("You do not have access to do this.", [], HttpStatusCode::NOT_FOUND);
        }

        // Check if requested user is a member
        $user_for_change_access = $this->teamMemberRepository->findOneByProperties(
            [
                "team_id" => $team->id,
                "user_id" => $user_id_for_change_access
            ]
        );

        if (!$user_for_change_access) {
            return $this->sendErrorResponse('Specified user is not a member of this team.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        if ($executer_member_info->role == "Owner+Admin" && $new_access_type == "Owner+Admin") {
            // He is trying to change owner admin
            // In this case we change both his role and the specified user role
            // Role change for specified user
            $user_for_change_access->role = $new_access_type;
            $input = (array)$user_for_change_access;
            $input = $user_for_change_access->fill($input)->toArray();
            $user_for_change_access->save($input);

            // Role change for executer
            $executer_member_info->role = "Member";
            $input = (array)$executer_member_info;
            $input = $executer_member_info->fill($input)->toArray();
            $executer_member_info->save($input);

            $response = "Owner Admin changed successfully";

        } else {
            $user_for_change_access->role = $new_access_type;
            $input = (array)$user_for_change_access;
            $input = $user_for_change_access->fill($input)->toArray();
            $user_for_change_access->save($input);

            $response = "User access changed successfully";
        }

        // Get current team data
        $team_infos = Team::select("*")
            ->with("team_members")
            ->where('id', $team->id)
            ->where('status', 1)
            ->get();
        for ($i = 0; $i < count($team_infos); $i++) {
            $team_infos[$i]->logo = url('storage/' . $team_infos[$i]->logo);
        }
        Notificationhelpers::add($response, 'team', $team_id, $user_id);
        return $this->sendSuccessResponse($team_infos[0], $response);
    }

    /**
     * Delete resource
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $team_id = $request->team_id;
            $delete_user_id = $request->delete_user_id;
            $user_id = Auth::id();

            // find team
            $team = $this->teamRepository->findOneByProperties(
                [
                    "team_id" => $team_id
                ]
            );

            if (!$team) {
                return $this->sendErrorResponse('Team is Not found.', [], HttpStatusCode::NOT_FOUND);
            }

            // find user to be deleted
            $user_to_be_deleted = $this->teamMemberRepository->findOneByProperties([
                "user_id" => $delete_user_id,
                "team_id" => $team->id
            ]);
            if (!$user_to_be_deleted) {
                return $this->sendErrorResponse('User to be deleted not found.', [], HttpStatusCode::NOT_FOUND);
            }

            // get requested user member status
            $request_member = $this->teamMemberRepository->findOneByProperties([
                "user_id" => $user_id,
                "team_id" => $team->id
            ]);
            if (!$request_member) {
                return $this->sendErrorResponse('You are not a member of this team.', [], HttpStatusCode::NOT_FOUND);
            }

            if ($user_id != $delete_user_id) {
                // I am checking capability only when he is trying to remove someone else.
                // For now i am checking capability like this. Will get this from table or enums later.
                $access_rules = new AccessRulesDefinitionService();
                $role_with_remove_rights = $access_rules->hasRemoveMemberRights();//["Owner+Admin","Member Admin","Candidate","Matchmaker"];
                if (!in_array($request_member->role, $role_with_remove_rights)) {
                    return $this->sendErrorResponse("You do not have access to remove member.($request_member->user_id)", [], HttpStatusCode::NOT_FOUND);
                }
            }

            if ($user_to_be_deleted->role == "Owner+Admin") {
                return $this->sendErrorResponse("You can not remove Team Owner", [], HttpStatusCode::NOT_FOUND);
            }

            if ($user_to_be_deleted->user_type == "Candidate") {
                // If user to be deleted is a Candidate then we need to remove the team as well after 48 Hours.
                // Need to discuss about the execution process of this feature
                // Currently i am removing the team member only
                $this->teamMemberRepository->delete($user_to_be_deleted);
                $response = "Successfully removed listed candidate member from team. The team is also removed.";
                $team->status = '0';
            } else {
                $this->teamMemberRepository->delete($user_to_be_deleted);
                $response = "Successfully removed listed non-candidate member from team!";
            }

            // Update team member count
            // if member count 0 delete team will be done later.
            if ($team->member_count > 0) {
                $team->member_count = $team->member_count - 1;
            }
            $input = (array)$team;
            // As BaseRepository update method has bug that's why we have to fallback to model default methods.
            $input = $team->fill($input)->toArray();
            $team->save($input);
            Notificationhelpers::add($response, 'team', $team_id, $user_id);
            return $this->sendSuccessResponse($user_to_be_deleted, $response);

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function leaveTeam($request)
    {
        $teamId = $request->team_id;
        $userId = $request->user_id;
        $newOwner = $request['new_owner'];
        $team = $this->teamRepository->findOneByProperties(
            [
                "team_id" => $teamId
            ]
        );
        $user = $this->teamMemberRepository->findOneByProperties([
            "team_id" => $teamId,
            "user_id" => $userId
        ]);

        if (!$user) {
            return $this->sendErrorResponse("User not found.", [], HttpStatusCode::NOT_FOUND);
        }

        if (!empty($request['new_owner'])) {
            $Owner = $this->teamMemberRepository->findOneByProperties([
                "team_id" => $teamId,
                "user_id" => $newOwner
            ]);

            if (!$Owner) {
                return $this->sendErrorResponse("Team New Owner not found.", [], HttpStatusCode::NOT_FOUND);
            }
            $Owner->role = 'Owner+Admin';
            $Owner->save();
            $user->delete();
            Notificationhelpers::add('New Owner Admin assign Successfully', 'team', $teamId, $userId);
            return $this->sendSuccessResponse([], 'New Owner Admin assign Successfully!');
        } else {

            $user->delete();
            if ($team->member_count > 0) {
                $team->member_count = $team->member_count - 1;
                $team->save();
            }

            Notificationhelpers::add('User Leave Successfully!', 'team', $teamId, $userId);
            return $this->sendSuccessResponse([], 'User Leave Successfully!');
        }

    }

}
