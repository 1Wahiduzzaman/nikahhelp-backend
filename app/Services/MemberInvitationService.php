<?php

namespace App\Services;


use App\Models\TeamMemberInvitation;

//use App\Transformers\TeamMemberTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use App\Repositories\TeamRepository;
use App\Repositories\MemberInvitationRepository;
use App\Repositories\TeamMemberRepository;
use App\Enums\HttpStatusCode;
use Illuminate\Support\Facades\Auth;


class MemberInvitationService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var MemberInvitationRepository
     */
    protected $memberInvitationRepository;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * MemberInvitationService constructor.
     *
     * @param MemberInvitationRepository $memberInvitationRepository
     * @param TeamRepository $teamRepository
     * @param TeamMemberRepository $teamMemberRepository
     */
    public function __construct(MemberInvitationRepository $memberInvitationRepository, TeamRepository $teamRepository, TeamMemberRepository $teamMemberRepository)
    {
        $this->memberInvitationRepository = $memberInvitationRepository;
        $this->teamRepository = $teamRepository;
        $this->teamMemberRepository = $teamMemberRepository;
    }

    /**
     * Update resource
     * @param array $data
     * @return JsonResponse
     */
    public function save(array $data): JsonResponse
    {
        try {
            $team_id = $data["team_id"];

            $team = $this->teamRepository->findOneByProperties([
                'team_id' => $team_id
            ]);

            if (!$team) {
                return $this->sendErrorResponse('Team is not found', [], HttpStatusCode::NOT_FOUND);
            }

            $team_row_id = $team->id;
            $members = $data["members"];
            $response = array();
            foreach ($members as $invitation) {
                $tempinvitation = array();
                $tempinvitation["team_id"] = $team_row_id;
                $tempinvitation["email"] = @$invitation["email"];
                $tempinvitation["role"] = $invitation["role"];
                $tempinvitation["link"] = $invitation["invitation_link"];
                $tempinvitation["user_type"] = $invitation["add_as_a"];
                $tempinvitation["relationship"] = $invitation["relationship"];
                $this->memberInvitationRepository->save($tempinvitation);

                // For clarity sending always string team_id column of team table as team_id to frontend
                $tempinvitation["team_id"] = $team_id;
                array_push($response, $tempinvitation);
            }

            return $this->sendSuccessResponse($response, 'Information inserted Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Join Team By Invitation
     * @param array $data
     * @return JsonResponse
     */
    public function join(array $data)
    {
        // Find invitation from link
        $invitation = $this->memberInvitationRepository->findOneByProperties(
            [
                "link" => $data["invitation_link"]
            ]
        );

        if (!$invitation) {
            return $this->sendErrorResponse('Invitation not found or expired.', [], HttpStatusCode::NOT_FOUND);
        }

        // Search team
        $team = $this->teamRepository->findOneByProperties(
            [
                "id" => $invitation->team_id
            ]
        );

        if (!$team) {
            return $this->sendErrorResponse('Team not found or may be deleted.', [], HttpStatusCode::NOT_FOUND);
        }

        // Match with team password
        if ($team->password !== $data["team_password"]) {
            return $this->sendErrorResponse('Password incorrect.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Check team size < 5
        if ($team->member_count > 4) {
            return $this->sendErrorResponse('Team can not have more than 5 members.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Get user id
        $user_id = Auth::id();

        // Check if already a member
        $team_member = $this->teamMemberRepository->findOneByProperties(
            [
                "team_id" => $team->id,
                "user_id" => $user_id
            ]
        );

        if ($team_member) {
            return $this->sendErrorResponse('You are already a member.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // If user is a member of more than 5 teams
        $all_user_teams = $this->teamMemberRepository->findByProperties([
            "user_id" => $user_id
        ]);

        if (count($all_user_teams) == 5) {
            return $this->sendErrorResponse('You can not join in more than 5 teams.', [], HttpStatusCode::VALIDATION_ERROR);
        }


        // If everything alright add in team members
        $new_team_member = array();
        $new_team_member["team_id"] = $team->id;
        $new_team_member["user_id"] = $user_id;
        $new_team_member["user_type"] = $invitation->user_type;
        $new_team_member["role"] = $invitation->role;
        $new_team_member["relationship"] = $invitation->relationship;

        try {
            $this->teamMemberRepository->save($new_team_member);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
        // Update team member count
        try {
            $team->member_count = $team->member_count + 1;
            $input = (array)$team;
            // As BaseRepository update method has bug that's why we have to fallback to model default methods.
            $input = $team->fill($input)->toArray();
            $team->save($input);
//            $this->teamRepository->save($team);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
        // Change invitation status
        // Remove Invitation from table

        $this->memberInvitationRepository->delete($invitation);
        return $this->sendSuccessResponse(array(), 'Information inserted Successfully!');
    }
}
