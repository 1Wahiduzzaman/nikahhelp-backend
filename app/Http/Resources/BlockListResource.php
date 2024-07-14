<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockListResource extends JsonResource
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
            'ethnicity' => $this->userinfo['per_ethnicity'] ?? "",
        ];
        if(!empty($this->userInfo['per_main_image_url'])):
        $image = url('storage/' . $this->userInfo['per_main_image_url']);
        else:
            $image=null;
        endif;
        return [
            'id' => $this->id ?? null,
            'user_id' => $this->user_id ?? null,
            'block_by' => $this->block_by ?? null,
            'block_by_name' => $this->getBlocklistedBy['full_name'] ?? null,
            'type' => $this->type ?? null,
            'candidate' => $candidateInfo,
            'team_id' => $this->block_for ?? null,
            'team_name' => $this->getTeam['name'] ?? null,
            'block_date' => $this->block_date ?? null,
            'image' => $image ?? null,
        ];

    }

}
