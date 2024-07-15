<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
//use App\Transformers\TeamMemberTransformer;
use App\Models\TeamMemberInvitation;
use App\Repositories\MemberInvitationRepository;
use App\Repositories\TeamMemberRepository;
use App\Repositories\TeamRepository;
use App\Traits\CrudTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class MemberInvitationService extends ApiBaseService
{
    use CrudTrait;

    protected \App\Repositories\MemberInvitationRepository $memberInvitationRepository;

    protected \App\Repositories\TeamRepository $teamRepository;

    protected \App\Repositories\TeamMemberRepository $teamMemberRepository;

    /**
     * MemberInvitationService constructor.
     */
    public function __construct(MemberInvitationRepository $memberInvitationRepository, TeamRepository $teamRepository, TeamMemberRepository $teamMemberRepository)
    {
        $this->memberInvitationRepository = $memberInvitationRepository;
        $this->teamRepository = $teamRepository;
        $this->teamMemberRepository = $teamMemberRepository;
    }

    /**
     * Update resource
     */
    public function save(array $data): JsonResponse
    {
        try {
            $team_id = $data['team_id'];

            $team = $this->teamRepository->findOneByProperties([
                'team_id' => $team_id,
            ]);

            if (! $team) {
                return $this->sendErrorResponse('Team is not found', [], HttpStatusCode::NOT_FOUND);
            }

            $team_row_id = $team->id;
            $members = $data['members'];
            $response = [];
            foreach ($members as $invitation) {
                $tempinvitation = [];
                $tempinvitation['team_id'] = $team_row_id;
                $tempinvitation['email'] = @$invitation['email'];
                $tempinvitation['role'] = $invitation['role'];
                $tempinvitation['link'] = $invitation['invitation_link'];
                $tempinvitation['user_type'] = $invitation['add_as_a'];
                $tempinvitation['relationship'] = $invitation['relationship'];
                $res = $this->memberInvitationRepository->save($tempinvitation);

                // For clarity sending always string team_id column of team table as team_id to frontend
                $tempinvitation['team_id'] = $team_id;
                $tempinvitation['invitation_id'] = $res->id;
                array_push($response, $tempinvitation);
            }

            return $this->sendSuccessResponse($response, 'Information inserted Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    public function edit($data)
    {
        try {
            TeamMemberInvitation::where('id', $data['invitation_id'])->update(['email' => $data['email']]);

            return $this->sendSuccessResponse([], 'Information Updated Successfully!');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Join Team By Invitation
     *
     * @return JsonResponse
     */
    public function join(array $data)
    {
        //user is public
        //
        $userInfo = self::getUserInfo();
        //if ( $userInfo->status == 5) {
        // Find invitation from link
        $invitation = $this->memberInvitationRepository->findOneByProperties(
            [
                'link' => $data['invitation_link'],
            ]
        );

        if (! $invitation) {
            return $this->sendErrorResponse('Invitation not found or expired.', [], HttpStatusCode::NOT_FOUND);
        }

        // Search team
        $team = $this->teamRepository->findOneByProperties(
            [
                'id' => $invitation->team_id,
            ]
        );

        if (! $team) {
            return $this->sendErrorResponse('Team not found or may be deleted.', [], HttpStatusCode::NOT_FOUND);
        }

        // Match with team password
        if ($team->password !== $data['team_password']) {
            return $this->sendErrorResponse('Password incorrect.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Check team size < 5
        if ($team->member_count > 4) {
            return $this->sendErrorResponse('Team can not have more than 5 members.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // Get user id
        $user_id = $this->getUserId();

        // Check if already a member
        $team_member = $this->teamMemberRepository->findOneByProperties(
            [
                'team_id' => $team->id,
                'user_id' => $user_id,
            ]
        );

        if ($team_member) {
            return $this->sendErrorResponse('You are already a member.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        // If user is a member of more than 5 teams
        $all_user_teams = $this->teamMemberRepository->findByProperties([
            'user_id' => $user_id,
        ]);

        if (count($all_user_teams) == 5) {
            return $this->sendErrorResponse('You can not join in more than 5 teams.', [], HttpStatusCode::VALIDATION_ERROR);
        }

        //If already member as a candidate  By Raz
        // $is_candidate = $this->teamMemberRepository->findByProperties([
        //     "user_id" => $user_id,
        //     ''
        // ]);

        // if ($invitation) {
        //     # code...
        // }

        // if($is_candidate->count()){
        //     return $this->sendErrorResponse('You can not join as a Candidate in multiple teams.', [], HttpStatusCode::BAD_REQUEST);
        // }

        // If everything alright add in team members
        $new_team_member = [];
        $new_team_member['team_id'] = $team->id;
        $new_team_member['user_id'] = $user_id;
        $new_team_member['user_type'] = $invitation->user_type;
        $new_team_member['role'] = $invitation->role;
        $new_team_member['relationship'] = $invitation->relationship;

        try {
            $this->teamMemberRepository->save($new_team_member);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
        // Update team member count
        try {
            $team->member_count = $team->member_count + 1;
            $input = (array) $team;
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

        return $this->sendSuccessResponse([], 'Information inserted Successfully!');
        // } else {
        //     return $this->sendErrorResponse("You are not able to create a Team or join in a Team until verified. please contact us so we can assist you.", [], HttpStatusCode::BAD_REQUEST);
        // }
    }

    public function delete($request)
    {
        // code...
        $row = $this->memberInvitationRepository->findOneByProperties(
            [
                'id' => $request->id,
            ]
        );
        $this->memberInvitationRepository->delete($row);

        return $this->sendSuccessResponse([], 'Invitation Request Deleted Successfully!');
    }
}
