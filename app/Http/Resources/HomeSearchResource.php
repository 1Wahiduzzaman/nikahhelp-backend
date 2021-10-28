<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DB;
use App\Models\TeamMember;
use App\Models\ShortListedCandidate;
use Carbon\Carbon;

class HomeSearchResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $firstName = $this->userinfo['first_name'] ?? "";
        $lastName = $this->userinfo['last_name'] ?? "";
        $religionInfo = $this->getReligion['name'] ?? "";
        $religion = "";
        if (!empty($religionInfo)) {
            $religion = explode(',', $religionInfo);
            $religion = $religion[0];
        }
        $age = Carbon::parse($this->userinfo['dob'])->diff(Carbon::now())->y .' Year';;
        $candidateInfo = [
            'name' => $firstName . ' ' . $lastName,
            'location' => $this->userinfo['per_nationality'] ?? "",
            'location_name' => $this->getNationality['name'] ?? "",
            'age' => $age ?? "",
            'religion' => $religion,
            'ethnicity' => $this->userinfo['per_ethnicity'] ?? "",
            'educationLevel' => $this->candidateEducationLevel['name'] ?? "",
            'height' => $this->per_height ?? "",
            'country_of_birth' => $this->getCountryOFBirth['name'] ?? "",
            'per_country_of_birth_id' => $this->getCountryOFBirth['id'] ?? "",
            'profession' => $this->per_occupation ?? "",
            'image' => null,
        ];

        if ($this->anybody_can_see == 1) {
            if (!empty($this->per_main_image_url)):
                $candidateInfo['image'] = url('storage/' . $this->per_main_image_url);
            else:
                $candidateInfo['image'] = null;
            endif;
        }
        $pre_partner_religions = self::partnerReligions($this->pre_partner_religions);
        $preFerences = [
            'partner_age_min' => $this->userinfo['pre_partner_age_min'] ?? "",
            'partner_age_max' => $this->userinfo['pre_partner_age_max'] ?? "",
            'height_min' => $this->userinfo['pre_height_min'] ?? "",
            'height_max' => $this->userinfo['pre_height_max'] ?? "",
            'height_min' => $this->userinfo['pre_height_min'] ?? "",
            'partner_religions' => !empty($pre_partner_religions) ? $pre_partner_religions : "",
            'partner_study_level_id' => $this->partner_study_level_id ?? "",
            'partner_occupation' => $this->pre_occupation ?? "",
            'partner_ethnicities' => $this->pre_ethnicities ?? "",
        ];
        if (!empty($this->userInfo['per_main_image_url'])):
            $image = url('storage/' . $this->userInfo['per_main_image_url']);
        else:
            $image = null;
        endif;
        $isShortListde = false;
        $isTeamShortListde = false;

        return [
            'id' => $this->id ?? null,
            'user_id' => $this->user_id ?? null,
            'candidate_team_id' => null,
            'is_shortlisted' => $isShortListde ?? null,
            'is_team_shortlisted' => $isTeamShortListde ?? null,
            'candidate' => $candidateInfo,
            'partner' => $preFerences
        ];

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

}
