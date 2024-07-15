<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeResource extends JsonResource
{
    /**
     * Resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (! empty($this->ver_document_frontside)) {
            $document_frontside = url('storage/'.$this->ver_document_frontside);
        } else {
            $document_frontside = null;
        }

        if (! empty($this->ver_document_backside)) {
            $ver_document_backside = url('storage/'.$this->ver_document_backside);
        } else {
            $ver_document_backside = null;
        }

        if (! empty($this->per_avatar_url)) {
            $per_avatar_url = url('storage/'.$this->per_avatar_url);
        } else {
            $per_avatar_url = null;
        }

        if (! empty($this->per_main_image_url)) {
            $per_main_image_url = url('storage/'.$this->per_main_image_url);
        } else {
            $per_main_image_url = null;
        }

        return [
            'id' => $this->id ?? null,
            'user_id' => $this->user_id ?? null,
            'first_name' => $this->first_name ?? null,
            'last_name' => $this->last_name ?? null,
            'screen_name' => $this->screen_name ?? null,
            'per_gender' => $this->per_gender ?? null,
            'dob' => $this->dob ?? null,
            'per_occupation' => $this->per_occupation ?? null,
            'per_email' => $this->per_email ?? null,
            'per_current_residence_country' => $this->per_current_residence_country ?? null,
            'per_current_residence_city' => $this->per_current_residence_city ?? null,
            'per_permanent_country' => $this->per_permanent_country ?? null,
            'per_permanent_city' => $this->per_permanent_city ?? null,
            'per_county' => $this->per_county ?? null,
            'per_telephone_no' => $this->per_telephone_no ?? null,
            'mobile_number' => $this->mobile_number ?? null,
            'mobile_country_code' => $this->mobile_country_code ?? null,
            'per_permanent_post_code' => $this->per_permanent_post_code ?? null,
            'per_permanent_address' => $this->per_permanent_address ?? null,
            'is_document_upload' => (bool) $this->is_document_upload ?? false,
            'ver_country' => $this->ver_country ?? null,
            'ver_city' => $this->ver_city ?? null,
            'ver_document_type' => $this->ver_document_type ?? null,
            'ver_document_frontside' => $document_frontside ?? null,
            'ver_document_backside' => $ver_document_backside ?? null,
            'ver_recommender_title' => $this->ver_recommender_title ?? null,
            'ver_recommender_first_name' => $this->ver_recommender_first_name ?? null,
            'ver_recommender_last_name' => $this->ver_recommender_last_name ?? null,
            'ver_recommender_occupation' => $this->ver_recommender_occupation ?? null,
            'ver_recommender_address' => $this->ver_recommender_address ?? null,
            'ver_recommender_mobile_no' => $this->ver_recommender_mobile_no ?? null,
            'per_avatar_url' => $per_avatar_url ?? null,
            'per_main_image_url' => $per_main_image_url ?? null,
            'anybody_can_see' => (bool) $this->anybody_can_see ?? false,
            'only_team_can_see' => (bool) $this->only_team_can_see ?? false,
            'team_connection_can_see' => (bool) $this->team_connection_can_see ?? false,
            'is_agree' => (bool) $this->is_agree ?? false,
            'data_input_status' => $this->data_input_status ?? null,

        ];

    }
}
