<?php

namespace App\Transformers;

use App\Models\CandidateInformation;
use App\Models\RepresentativeInformation;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

/**
 * Class RepresentativeTransformer
 */
class RepresentativeTransformer extends TransformerAbstract
{
    /**
     * @return array|array[]
     */
    public function transform(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'basic' => $this->basicInfo($item),
            ],
            [
                'essential' => $this->essentialInfo($item),
            ],
            [
                'personal' => $this->personalInfo($item),
            ],
            [
                'verification' => $this->verificationInfo($item),
            ],
            [
                'image_upload' => $this->imageUploadInfo($item),
            ]
        );
    }

    public function profileInfo(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item),
            ],
            [
                'personal' => $this->personalInfo($item),
            ],
            [
                'verification' => $this->verificationInfo($item),
            ],
            [
                'image_upload' => $this->imageUploadInfo($item),
            ]
        );
    }

    public function transformProfileInitialInfo(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'personal' => $this->personalInfo($item),
            ],
            [
                'preference' => $this->preferenceInfo($item),
            ],
            [
                'family' => $this->familyInfo($item),
            ]
        );
    }

    public function transformVerificationInformation(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'verification' => $this->verificationInfo($item),
            ]
        );
    }

    public function transformPersonal(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item),
                'general' => $this->generellInfo($item),

                'contact' => $this->contactInfo($item),
                'more_about' => $this->moreabout($item),
            ],
        );
    }

    public function transformGallery(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'gallery' => $this->galleryInfo($item),
            ],
        );
    }

    public function transformPersonalBasic(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'basic' => $this->basicInfo($item),
            ],
        );
    }

    public function transformPersonalContact(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'contact' => $this->contactInfo($item),
            ],
        );
    }

    public function transformPersonalEssential(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item),
            ],
        );
    }

    public function transformPersonalGeneral(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'general' => $this->generellInfo($item),
            ],
        );
    }

    public function transformPersonalMoreAbout(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'more_about' => $this->moreabout($item),
            ],
        );
    }

    public function transformPreference(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'preference' => $this->preferenceInfo($item),
            ],
        );
    }

    public function transformFamily(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'family' => $this->familyInfo($item),
            ],
        );
    }

    /**
     * Extract Basic info only
     */
    private function basicInfo(RepresentativeInformation $item): array
    {
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'screen_name' => $item->screen_name,
            'data_input_status' => $item->data_input_status,
            'is_uplaoded_doc' => $item->is_uplaoded_doc,
        ];
    }

    /**
     * Extract Essential info only
     */
    private function essentialInfo(RepresentativeInformation $item): array
    {
        return [
            'per_gender' => $item->per_gender,
            'per_gender_text' => CandidateInformation::getGender($item->per_gender),
            'dob' => $item->dob,
            'per_occupation' => $item->per_occupation,
        ];
    }

    public function RepDetails(RepresentativeInformation $item)
    {
        return $this->personalInfo($item);
    }

    /**
     * Extract Personal info only
     */
    private function personalInfo(RepresentativeInformation $item): array
    {
        return [
            'per_email' => $item->per_email,
            'per_current_residence_country' => $item->per_current_residence_country,
            'per_current_residence_country_text' => $item->currentResidenceCountry ? $item->currentResidenceCountry->name : null,
            'per_current_residence_city' => $item->per_current_residence_city,
            'per_permanent_country' => $item->per_permanent_country,
            'per_permanent_country_text' => $item->permanentCountry ? $item->permanentCountry->name : null,
            'per_permanent_city' => $item->per_permanent_city,
            'per_county' => $item->per_county,
            'per_county_text' => $item->country ? $item->country->name : null,
            'per_telephone_no' => $item->per_telephone_no,
            'mobile_number' => $item->mobile_number,
            'mobile_country_code' => $item->mobile_country_code,
            'per_permanent_post_code' => $item->per_permanent_post_code,
            'per_permanent_address' => $item->per_permanent_address,
            'address_1' => $item->address_1,
            'address_2' => $item->address_2,
        ];
    }

    /**
     * Extract Verification info only
     */
    private function verificationInfo(RepresentativeInformation $item): array
    {
        return [
            'is_document_upload' => $item->is_document_upload,
            'ver_country' => $item->ver_country,
            'ver_city' => $item->ver_city,
            'ver_document_type' => $item->ver_document_type,
            'ver_document_frontside' => $item->ver_document_frontside,
            'ver_document_backside' => $item->ver_document_backside,
            'ver_recommender_title' => $item->ver_recommender_title,
            'ver_recommender_first_name' => $item->ver_recommender_first_name,
            'ver_recommender_last_name' => $item->ver_recommender_last_name,
            'ver_recommender_occupation' => $item->ver_recommender_occupation,
            'ver_recommender_address' => $item->ver_recommender_address,
            'ver_recommender_mobile_no' => $item->ver_recommender_mobile_no,
            'ver_recommender_email' => $item->ver_recommender_email,
        ];
    }

    /**
     * Extract Verification info only
     */
    private function imageUploadInfo(RepresentativeInformation $item): array
    {
        return [
            'per_avatar_url' => $item->per_avatar_url,
            'per_main_image_url' => $item->per_main_image_url,
            'anybody_can_see' => $item->anybody_can_see,
            'only_team_can_see' => $item->only_team_can_see,
            'team_connection_can_see' => $item->team_connection_can_see,
            'is_agree' => $item->is_agree,
        ];
    }

    private function galleryInfo(RepresentativeInformation $item)
    {
        return [
            'ver_document_frontside' => $item->ver_document_frontside,
            'ver_document_backside' => $item->ver_document_backside,
            'per_avatar_url' => $item->per_avatar_url,
            'per_main_image_url' => $item->per_main_image_url,
        ];
    }

    /**
     * @param  CandidateInformation  $item
     */
    public function transformSearchResult(RepresentativeInformation $item): array
    {
        return $this->candidateCartData($item);
    }

    private function candidateCartData(CandidateInformation $item): array
    {
        return [
            'user_id' => $item->user_id,
            'image' => $item->avatar_url,
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'screen_name' => $item->screen_name,
            'per_age' => (int) Carbon::now()->diffInYears($item->dob, true),
            'per_gender' => $item->per_gender,
            'per_nationality_id' => $item->per_nationality,
            'per_nationality' => $item->getNationality()->exists() ? $item->getNationality->name : null,
            'per_religion_id' => $item->per_religion_id,
            'per_religion' => $item->getReligion()->exists() ? $item->getReligion->name : null,
            'per_ethnicity' => $item->per_ethnicity,
            'height' => $item->per_height,
            'is_short_listed' => $item->is_short_listed ?? null,
            'is_block_listed' => $item->is_block_listed ?? null,
            'is_connect' => $item->is_connect ?? null,
            'is_teamListed' => $item->is_teamListed ?? null,
            'team_id' => $item->team_id ?? null,
            'connectionRequestSendType' => $item->connectionRequestSendType ?? null,
            'teamConnectStatus' => $item->teamConnectStatus ?? null,
            'verification_status' => $item->user->status,
            'team' => $item->team_info ?? null,
            'representative_status' => $item->representative_status ?? null,
        ];
    }
}
