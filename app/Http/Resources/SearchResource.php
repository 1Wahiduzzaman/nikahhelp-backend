<?php

namespace App\Http\Resources;

use App\Models\ShortListedCandidate;
use App\Models\TeamMember;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $is_connected = false;

        $firstName = $this->userinfo['first_name'] ?? '';
        $lastName = $this->userinfo['last_name'] ?? '';
        $age = Carbon::parse($this->userinfo['dob'])->diff(Carbon::now())->y.' Year';
        $candidateInfo = [
            'name' => $firstName.' '.$lastName,
            'location' => $this->userinfo['per_nationality'] ?? '',
            'location_name' => $this->getNationality['name'] ?? '',
            'age' => $this->userinfo['dob'] ?? '',
            'age2' => $age ?? '',
            'religion' => $this->getReligion['name'] ?? '',
            'ethnicity' => $this->userinfo['per_ethnicity'] ?? '',
            'educationLevel' => $this->candidateEducationLevel['name'] ?? '',
            'height' => $this->per_height ?? '',
            'country_of_birth' => $this->getCountryOFBirth['name'] ?? '',
            'per_country_of_birth_id' => $this->getCountryOFBirth['id'] ?? '',
            'profession' => $this->per_occupation ?? '',
            'image' => null,
        ];

        if ($this->anybody_can_see == 1) {
            if (! empty($this->per_main_image_url)) {
                $candidateInfo['image'] = url('storage/'.$this->per_main_image_url);
            } else {
                $candidateInfo['image'] = null;
            }
        }
        $pre_partner_religions = self::partnerReligions($this->pre_partner_religions);
        $preFerences = [
            'partner_age_min' => $this->userinfo['pre_partner_age_min'] ?? '',
            'partner_age_max' => $this->userinfo['pre_partner_age_max'] ?? '',
            'height_min' => $this->userinfo['pre_height_min'] ?? '',
            'height_max' => $this->userinfo['pre_height_max'] ?? '',
            'height_min' => $this->userinfo['pre_height_min'] ?? '',
            'partner_religions' => ! empty($pre_partner_religions) ? $pre_partner_religions : '',
            //            'country_preferences' => $this->preferred_countries() ?? "",
            'partner_study_level_id' => $this->pre_study_level_id ?? '',
            'partner_occupation' => $this->pre_occupation ?? '',
            'partner_ethnicities' => $this->pre_ethnicities ?? '',
        ];

        $isShortListde = false;
        $isTeamShortListde = false;
        if (! empty(self::getUserId())) {
            $shortList = $this->shortListed(self::getUserId());
            if (! empty($shortList) && in_array("$this->user_id", $shortList->toArray())) {
                $isShortListde = true;
            }
            $shortListTeam = $this->shortListedTeam(self::getUserId());
            if (! empty($shortListTeam) && in_array("$this->user_id", $shortListTeam->toArray())) {
                $isTeamShortListde = true;
            }
        }
        $candidateTeamInfo = $this->getCandidateTeamId($this->user_id);
        if (! empty($candidateTeamInfo) && ! empty($this->connected_team) && isset($this->connected_team)) {
            $connectedTeam = $this->connected_team;
            if (in_array($candidateTeamInfo->id, $connectedTeam)) {
                $is_connected = true;

            }
        }

        return [
            'id' => $this->id ?? null,
            'user_id' => $this->user_id ?? null,
            'candidate_team_table_id' => $candidateTeamInfo->id ?? null,
            'candidate_team_id' => $candidateTeamInfo->team_id ?? null,
            'is_shortlisted' => $isShortListde ?? null,
            'is_team_shortlisted' => $isTeamShortListde ?? null,
            'is_connected' => null,
            'candidate' => $candidateInfo,
            'partner' => $preFerences,
            'is_connected' => $is_connected ?? null,

        ];

    }

    /**
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

    /**
     * @return |null
     */
    public function getCandidateTeamId($userId)
    {
        if (empty($userId)) {
            return null;
        }
        $candidateID = TeamMember::select('teams.team_id', 'teams.id')
            ->join('teams', 'teams.id', '=', 'team_members.team_id')
            ->where('team_members.user_id', '=', $userId)->where('team_members.user_type', '=', 'Candidate')->first();
        if (! empty($candidateID)) {
            return $candidateID;
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function shortListed($userId)
    {
        return $list = ShortListedCandidate::where('shortlisted_by', '=', $userId)
            ->whereNull('shortlisted_for')
            ->pluck('user_id');
    }

    /**
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
        $user = auth()->authenticate();

        return $user['id'];
    }
}
