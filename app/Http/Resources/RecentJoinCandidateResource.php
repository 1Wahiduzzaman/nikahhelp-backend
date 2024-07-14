<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecentJoinCandidateResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
       
        $candidateInfo = [
            'location' => $this->userinfo['per_nationality'] ?? "",
            'location_name' => $this->userinfo->getNationality['name'] ?? "",
            'age' => $this->userinfo['dob'] ?? "",
            'religion' => $this->userinfo->getReligion['name'] ?? "",
            'study_level' => $this->candidateEducationLevel['name'] ?? "",
            'ethnicity' => $this->userinfo['per_ethnicity'] ?? "",
            'image' => isset($this->userInfo['per_avatar_url']) ? $this->userInfo['per_avatar_url'] : null ,
            'gender' => $this->userinfo['per_gender'] ?? ""

        ];
        return $candidateInfo;

    }

}
