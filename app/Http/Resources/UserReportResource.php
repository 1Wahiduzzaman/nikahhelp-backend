<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReportResource extends JsonResource
{

    const ACCOUNT_TYPE = [
        0 => 'In-active',
        1 => 'Candidate',
        2 => 'Representative',
        3 => 'Matchmaker',
        4 => 'Admin'
    ];

    const ACCOUNT_STATUS = [
        0 => 'Block',
        1 => 'Active',
        2 => 'Delete Account',
        3 => 'Suspend',
        4 => 'Temporary Block'
    ];

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $candidateInfo = array();
        $representative = array();
        $matchmaker = array();
        if ($this->account_type == 1) {
            $firstName = $this->getCandidate['first_name'] ?? "";
            $lastName = $this->getCandidate['last_name'] ?? "";
            $candidateInfo = [
                'name' => $firstName . ' ' . $lastName,
                'location' => $this->getCandidate['per_nationality'] ?? "",
                'location_name' => $this->getCandidate->getNationality['name'] ?? "",
                'age' => $this->getCandidate['dob'] ?? "",
                'gender' => $this->getCandidate['per_gender'] ?? "",
                'religion' => $this->getCandidate->getReligion['name'] ?? "",
                'study_level' => $this->getCandidate->candidateEducationLevel['name'] ?? "",
                'ethnicity' => $this->getCandidate['per_ethnicity'] ?? "",
            ];
            if (!empty($this->getCandidate['per_main_image_url'])):
                $image = url('storage/' . $this->getCandidate['per_main_image_url']);
            else: $image = null; endif;
            $candidateInfo['image'] = $image;
        }elseif ($this->account_type==2){
            $repFirstName = $this->getRepresentative['first_name'] ?? "";
            $repLastName = $this->getRepresentative['last_name'] ?? "";
            $representative = [
                'name' => $repFirstName . ' ' . $repLastName,
                'location' => $this->getRepresentative['per_county'] ?? "",
                'location_name' => $this->getRepresentative['per_county'] ?? "",
                'age' => $this->getRepresentative['dob'] ?? "",
                'gender' => $this->getRepresentative['per_gender'] ?? "",
                'occupation' => $this->getRepresentative['per_occupation'] ?? "",
                'study_level' => "",
                'ethnicity' =>  "",
            ];
            if (!empty($this->getRepresentative['per_main_image_url'])):
                $repImage = url('storage/' . $this->getRepresentative['per_main_image_url']);
            else: $repImage = null; endif;
            $representative['image'] = $repImage;
        }elseif ($this->account_type==3){
            $matchMakerFirstName = $this->getMatchmaker['first_name'] ?? "";
            $matchMakerLastName = $this->getMatchmaker['last_name'] ?? "";
            $matchmaker = [
                'name' => $matchMakerFirstName . ' ' . $matchMakerLastName,
                'location' => $this->getMatchmaker['per_county'] ?? "",
                'location_name' => $this->getMatchmaker['per_county'] ?? "",
                'age' => $this->getMatchmaker['dob'] ?? "",
                'gender' => $this->getMatchmaker['per_gender'] ?? "",
                'occupation' => $this->getMatchmaker['per_occupation'] ?? "",
                'study_level' => "",
                'ethnicity' =>  "",
            ];
            if (!empty($this->getMatchmaker['per_main_image_url'])):
                $repImage = url('storage/' . $this->getMatchmaker['per_main_image_url']);
            else: $repImage = null; endif;
            $matchmaker['image'] = $repImage;
        }
        $result = [
            'id' => $this->id ?? null,
            'full_name' => $this->full_name ?? null,
            'email' => $this->email ?? null,
            'email_verified_at' => $this->email_verified_at ?? null,
            'is_verified' => $this->is_verified ?? null,
            'status' => $this->status ?? null,
            'status_meaning' => self::ACCOUNT_STATUS[$this->status] ?? null,
            'locked_at' => $this->locked_at ?? null,
            'locked_end' => $this->locked_end ?? null,
            'account_type' => $this->account_type ?? null,
            'account_type_meaning' => self::ACCOUNT_TYPE[$this->account_type] ?? null,
            'candidateInfo' => $candidateInfo,
            'representative' => $representative,
            'matchmaker' => $matchmaker
        ];

        return $result;

    }


}
