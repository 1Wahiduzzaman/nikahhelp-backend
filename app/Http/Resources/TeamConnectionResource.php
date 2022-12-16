<?php

namespace App\Http\Resources;

use App\Models\CandidateImage;
use App\Models\ShortListedCandidate;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use DB;
use JWTAuth;

class TeamConnectionResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (isset($this->active_teams) && !empty($this->active_teams)) {
            $aactiveTeam = $this->active_teams;
        }
        $userId = self::getUserId();
        $result = [
            'connection_id' => $this->id ?? null,
            'from_team_table_id' => $this->from_team_id ?? null,
            'from_team_id' => $this->from_team->team_id ?? null,
            'from_team_name' => $this->from_team->name ?? null,
            'to_team_table_id' => $this->to_team_id ?? null,
            'to_team_id' => $this->to_team->team_id ?? null,
            'to_team_name' => $this->to_team->name ?? null,

            'requested_by' => User::find($this->requested_by) ?? null,
            'responded_by' => User::find($this->responded_by) ?? null,
            'connection_status' => $this->connection_status ?? null,
            'requested_at' => $this->requested_at ?? null,
            'responded_at' => $this->responded_at ?? null,
            'from_team_members' => self::getTeamMembers($this->from_team_id),
            'to_team_members' => self::getTeamMembers($this->to_team_id),
        ];

        $result['candidateInfo'] = null;
//        Request send
        if ($this->connection_status == 0 && !empty($aactiveTeam) && $this->from_team_id == $aactiveTeam) {
            $result['team_table_id'] = $this->to_team->id;
            $result['team_name'] = $this->to_team->name;
            $result['team_created_date'] = $this->to_team->created_at;
            $result['team_created_by'] = Self::getTeamCreatedBy($this->to_team->created_by);;
            $candidateInfo = self::candidateInfomation($this->to_team->id);
            if (!empty($candidateInfo)) {
                $result['candidateInfo'] = $candidateInfo;
            }
            $countTeamMember = self::totalTeamMember($this->to_team->id);
            if (!empty($countTeamMember)) {
                $result['total_teamMember'] = $countTeamMember;
            }
            $result['connection'] = 'pending';
            $result['connection_type'] = 'Request sent';
        }

//        Request received
        if ($this->connection_status == 0 && !empty($aactiveTeam) && $this->to_team_id == $aactiveTeam) {

            $result['team_table_id'] = $this->from_team->id;
            $result['team_name'] = $this->from_team->name;
            $result['team_created_date'] = $this->from_team->created_at;
            $result['team_created_by'] = Self::getTeamCreatedBy($this->from_team->created_by);
            $candidateInfo = self::candidateInfomation($this->from_team->id);

            if (!empty($candidateInfo)) {
                $result['candidateInfo'] = $candidateInfo;
            }
            $countTeamMember = self::totalTeamMember($this->from_team->id);
            if (!empty($countTeamMember)) {
                $result['total_teamMember'] = $countTeamMember;
            }
            $result['connection'] = 'pending';
            $result['connection_type'] = 'Request received';

        }

//        Connected
        if ($this->connection_status == 1 && !empty($aactiveTeam) && ($this->to_team_id == $aactiveTeam or $this->from_team_id == $aactiveTeam)) {


            if ($this->from_team_id == $aactiveTeam) {
                $result['team_table_id'] = $this->to_team->id;
                $result['team_name'] = $this->to_team->name;

                $result['team_created_date'] = $this->to_team->created_at;
                $result['team_created_by'] = Self::getTeamCreatedBy($this->to_team->created_by);

                $candidateInfo = self::candidateInfomation($this->to_team->id);
                $countTeamMember = self::totalTeamMember($this->to_team->id);
            }
            if ($this->to_team_id == $aactiveTeam) {
                $result['team_table_id'] = $this->from_team->id;
                $result['team_name'] = $this->from_team->name;

                $result['team_created_date'] = $this->from_team->created_at;
                $result['team_created_by'] = Self::getTeamCreatedBy($this->to_team->created_by);
                $candidateInfo = self::candidateInfomation($this->from_team->id);
                $countTeamMember = self::totalTeamMember($this->from_team->id);
            }
            if (!empty($candidateInfo)) {
                $result['candidateInfo'] = $candidateInfo;
            }

            if (!empty($countTeamMember)) {
                $result['total_teamMember'] = $countTeamMember;
            }
            $result['connection'] = 'connected';
            $result['connection_type'] = 'connected';

        }

//        we declined
        if ($this->connection_status == 2 && !empty($aactiveTeam) && $this->to_team_id == $aactiveTeam) {
            $result['team_table_id'] = $this->from_team->id;
            $result['team_name'] = $this->from_team->name;
            $result['team_created_date'] = $this->from_team->created_at;
            $result['team_created_by'] = Self::getTeamCreatedBy($this->from_team->created_by);
            $candidateInfo = self::candidateInfomation($this->from_team->id);
            if (!empty($candidateInfo)) {
                $result['candidateInfo'] = $candidateInfo;
            }
            $countTeamMember = self::totalTeamMember($this->from_team->id);
            if (!empty($countTeamMember)) {
                $result['total_teamMember'] = $countTeamMember;
            }
            $result['connection'] = 'we declined';
            $result['connection_type'] = 'we declined';

        }

//        They declined
        if ($this->connection_status == 2 && !empty($aactiveTeam) && $this->from_team_id == $aactiveTeam) {
            $result['team_table_id'] = $this->to_team->id;
            $result['team_name'] = $this->to_team->name;
            $result['team_created_date'] = $this->to_team->created_at;
            $result['team_created_by'] = Self::getTeamCreatedBy($this->to_team->created_by);
            $candidateInfo = self::candidateInfomation($this->to_team->id);
            if (!empty($candidateInfo)) {
                $result['candidateInfo'] = $candidateInfo;
            }
            $countTeamMember = self::totalTeamMember($this->to_team->id);
            if (!empty($countTeamMember)) {
                $result['total_teamMember'] = $countTeamMember;
            }
            $result['connection'] = 'They declined';
            $result['connection_type'] = 'they declined';

        }

        return $result;

    }

    /**
     * @param $id
     * @return |null
     */
    public function partnerReligions($id)
    {
        if (empty($id)) {
            return null;
        }
        $idsArr = explode(',', $id);
        $re = DB::table('religions')->whereIn('id', $idsArr)->pluck('name');

        if (count($re) > 0) {
            return $re;
        } else {
            return null;
        }
    }

    public function candidateInfomation($teamId)
    {
        $teamInfo = TeamMember::where('team_id', '=', $teamId)->where('user_type', '=', 'Candidate')->first();

        if (!empty($teamInfo->getCandidateInfo) && isset($teamInfo->getCandidateInfo->first_name)) {
            // if ($teamInfo->getCandidateInfo->anybody_can_see == 1 or $teamInfo->getCandidateInfo->team_connection_can_see == 1) {
            //     // $image = url('storage/' . $teamInfo->getCandidateInfo->per_main_image_url);
            //     // $image = isset($teamInfo->getCandidateInfo->per_main_image_url) ? env('IMAGE_SERVER') .'/'. $teamInfo->getCandidateInfo->per_main_image_url : '';
            //     $image = isset($teamInfo->getCandidateInfo->per_main_image_url) ?  $teamInfo->getCandidateInfo->per_main_image_url : '';
            // } else {
            //     $image = null;
            // }

            $result = [
                'candidate_fname' => $teamInfo->getCandidateInfo->first_name,
                'candidate_lname' => $teamInfo->getCandidateInfo->last_name,
                'candidate_age' => $teamInfo->getCandidateInfo->dob,
                'candidate_location' => $teamInfo->getCandidateInfo->per_current_residence_country,
                'candidate_ethnicity' => $teamInfo->getCandidateInfo->per_ethnicity,
                // 'candidate_image' => $image,
                'candidate_location' => isset($teamInfo->getCandidateInfo->getNationality->name) ? $teamInfo->getCandidateInfo->getNationality->name : null,
                'candidate_religion' => isset($teamInfo->getCandidateInfo->getReligion->name) ? $teamInfo->getCandidateInfo->getReligion->name : null,
                'candidate_userid' => $teamInfo->getCandidateInfo->user_id,
                'candidate_image' => CandidateImage::getCandidateMainImage($teamInfo->getCandidateInfo->user_id),
            ];
        } else {
            $result = '';
        }
        return $result;
    }

    /**
     * @param $userId
     * @return |null
     */
    public function getCandidateTeamId($userId)
    {
        if (empty($userId)) {
            return null;
        }
        $candidateID = TeamMember::select('teams.team_id')
            ->join('teams', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', '=', $userId)->where('team_members.user_type', '=', 'Candidate')->first();
        if (!empty($candidateID)) {
            return $candidateID->team_id;
        } else {
            return null;
        }
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function shortListed($userId)
    {
        return $list = ShortListedCandidate::where('shortlisted_by', '=', $userId)
            ->whereNull('shortlisted_for')
            ->pluck('user_id');
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function shortListedTeam($userId)
    {
        return $list = ShortListedCandidate::where('shortlisted_by', '=', $userId)
            ->whereNotNull('shortlisted_for')
            ->pluck('user_id');
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $user['id'];
    }

    public function totalTeamMember($teamId)
    {
        if (!empty($teamId)) {
            return TeamMember::where('team_id', $teamId)->count();
        } else {
            return 0;
        }
    }

    public function getTeamCreatedBy($userId)
    {
        $userrInfo = User::find($userId);
        if ($userrInfo) {
            return $userrInfo->full_name;

        } else {
            return null;
        }
    }

    public function getTeamMembers($teamId)
    {
        if (!empty($teamId)) {
            return TeamMember::select('id', 'user_id')->where('team_id', $teamId)->pluck('user_id')->toArray();
        } else {
            return 0;
        }
    }
}
