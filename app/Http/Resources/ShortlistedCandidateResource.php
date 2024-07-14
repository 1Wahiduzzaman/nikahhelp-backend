<?php

namespace App\Http\Resources;

use App\Models\TeamMember;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortlistedCandidateResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $firstName = $this->userinfo['first_name'] ?? '';
        $lastName = $this->userinfo['last_name'] ?? '';
        $age = Carbon::parse($this->userinfo['dob'])->diff(Carbon::now())->y.' Year';
        $candidateInfo = [
            'candidate_team_id' => $this->getCandidateTeamId($this->userinfo['user_id']) ?? null,
            'name' => $firstName.' '.$lastName,
            'location' => $this->userinfo['per_nationality'] ?? '',
            'location_name' => $this->userinfo->getNationality['name'] ?? '',
            'age' => $age ?? '',
            'educationLevel' => $this->userinfo->candidateEducationLevel['name'] ?? '',
            'religion' => $this->userinfo->getReligion['name'] ?? '',
            'ethnicity' => $this->userinfo['per_ethnicity'] ?? '',
            'height' => $this->userinfo['per_height'] ?? '',
            'country_of_birth' => $this->userinfo->getCountryOFBirth['name'] ?? '',
            'per_country_of_birth_id' => $this->userinfo->getCountryOFBirth['id'] ?? '',
            'profession' => $this->userinfo['per_occupation'] ?? '',
            'image' => null,
        ];
        if ($this->userinfo['anybody_can_see'] == 1 && ! empty($this->userInfo['per_main_image_url'])) {
            $candidateInfo['image'] = $image = url('storage/'.$this->userInfo['per_main_image_url']);
        } else {
            $image = null;
        }
        $pre_partner_religions = self::partnerReligions($this->userinfo['pre_partner_religions']);
        $preFerences = [
            'partner_age_min' => $this->userinfo['pre_partner_age_min'] ?? '',
            'partner_age_max' => $this->userinfo['pre_partner_age_max'] ?? '',
            'height_min' => $this->userinfo['pre_height_min'] ?? '',
            'height_max' => $this->userinfo['pre_height_max'] ?? '',
            'height_min' => $this->userinfo['pre_height_min'] ?? '',
            'partner_religions' => ! empty($pre_partner_religions) ? $pre_partner_religions : '',
            'partner_study_level_id' => $this->userinfo['pre_study_level_id'] ?? '',
            'partner_study_level' => $this->userinfo->preEducationLevel['name'] ?? '',
            'partner_occupation' => $this->userinfo['pre_occupation'] ?? '',
            'partner_ethnicities' => $this->userinfo['pre_ethnicities'] ?? '',
        ];

        return [
            'id' => $this->id ?? null,
            'user_id' => $this->user_id ?? null,
            'shortlisted_by' => $this->shortlisted_by ?? null,
            'shortlisted_by_name' => $this->getShortlistedBy['full_name'] ?? null,
            'candidate' => $candidateInfo,
            'partner' => $preFerences,
            'team_id' => $this->shortlisted_for ?? null,
            'team_name' => $this->getTeam['name'] ?? null,
            'shortlisted_date' => $this->shortlisted_date ?? null,
            'image' => $image ?? null,
        ];

    }

    /**
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
        if (! empty($candidateID)) {
            return $candidateID->team_id;
        } else {
            return null;
        }
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
}
