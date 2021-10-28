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
        $firstName = $this->userinfo['first_name'] ?? "";
        $lastName = $this->userinfo['last_name'] ?? "";
        $candidateInfo = [
            'name' => $firstName . ' ' . $lastName,
            'location' => $this->userinfo['per_nationality'] ?? "",
            'location_name' => $this->userinfo->getNationality['name'] ?? "",
            'age' => $this->userinfo['dob'] ?? "",
            'religion' => $this->userinfo->getReligion['name'] ?? "",
            'study_level' => $this->candidateEducationLevel['name'] ?? "",
            'ethnicity' => $this->userinfo['per_ethnicity'] ?? "",
        ];
        if(!empty($this->userInfo['per_main_image_url'])):
            $image = url('storage/' . $this->userInfo['per_main_image_url']);
        else:
            $image=null;
        endif;
        $candidateInfo['image']=$image;
        return $candidateInfo;

    }

}
