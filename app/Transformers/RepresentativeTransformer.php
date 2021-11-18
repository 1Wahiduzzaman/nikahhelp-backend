<?php


namespace App\Transformers;

use App\Models\RepresentativeInformation;
use League\Fractal\TransformerAbstract;

/**
 * Class CandidateTransformer
 * @package App\Transformers
 */
class RepresentativeTransformer extends TransformerAbstract
{

    /**
     * @param RepresentativeInformation $item
     * @return array|array[]
     */
    public function transform(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'basic' => $this->basicInfo($item)
            ],
            [
                'essential' => $this->essentialInfo($item)
            ],
            [
                'personal' => $this->personalInfo($item)
            ],
            [
                'verification' => $this->verificationInfo($item)
            ],
            [
                'image_upload' => $this->imageUploadInfo($item)
            ]
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformProfileInitialInfo(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'personal' => $this->personalInfo($item)
            ],
            [
                'preference' => $this->preferenceInfo($item)
            ],
            [
                'family' => $this->familyInfo($item)
            ]
        );
    }

    public function transformPersonalVerification(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'verification' => $this->personalVerification($item)
            ]
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPersonal(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item),
                'general' => $this->generellInfo($item),

                'contact' => $this->contactInfo($item),
                'more_about' => $this->moreabout($item)
            ],
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPersonalBasic(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'basic' => $this->basicInfo($item)
            ],
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPersonalContact(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'contact' => $this->contactInfo($item)
            ],
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPersonalEssential(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item)
            ],
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPersonalGeneral(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'general' => $this->generellInfo($item)
            ],
        );
    }


    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPersonalMoreAbout(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'more_about' => $this->moreabout($item)
            ],
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformPreference(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'preference' => $this->preferenceInfo($item)
            ],
        );
    }

    /**
     * @param RepresentativeInformation $item
     * @return array
     */
    public function transformFamily(RepresentativeInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'family' => $this->familyInfo($item)
            ],
        );
    }


    /**
     * Extract Basic info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function basicInfo(RepresentativeInformation $item): array
    {
        return [
            'id' => $item->id,
            'user_id'=>$item->user_id,
            'first_name'=>$item->first_name,
            'last_name'=>$item->last_name,
            'screen_name'=>$item->screen_name,
            'data_input_status' => $item->data_input_status
        ];
    }

    /**
     * Extract Essential info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function essentialInfo(RepresentativeInformation $item): array
    {
        return [
            'per_gender' => $item->per_gender,
            'dob' => $item->dob,
            'per_occupation' => $item->per_occupation,
        ];
    }

    /**
     * Extract Personal info only
     * @param RepresentativeInformation $item
     * @return array
     */
    private function personalInfo(RepresentativeInformation $item): array
    {
        return [
            'per_email' => $item->per_email,
            'per_current_residence_country' => $item->per_current_residence_country,
            'per_current_residence_city' => $item->per_current_residence_city,
            'per_permanent_country' => $item->per_permanent_country,
            'per_permanent_city' => $item->per_permanent_city,
            'per_county' => $item->per_county,
            'per_telephone_no' => $item->per_telephone_no,
            'mobile_number' => $item->mobile_number,
            'mobile_country_code' => $item->mobile_country_code,
            'per_permanent_post_code' => $item->per_permanent_post_code,
            'per_permanent_address' => $item->per_permanent_address,
        ];
    }

    /**
     * Extract Verification info only
     * @param RepresentativeInformation $item
     * @return array
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
        ];
    }

    /**
     * Extract Verification info only
     * @param RepresentativeInformation $item
     * @return array
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
}
