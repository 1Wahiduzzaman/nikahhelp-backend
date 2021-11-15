<?php


namespace App\Transformers;

use App\Models\CandidateInformation;
use League\Fractal\TransformerAbstract;

/**
 * Class CandidateTransformer
 * @package App\Transformers
 */
class CandidateTransformer extends TransformerAbstract
{

    /**
     * @param CandidateInformation $item
     * @return array|array[]
     */
    public function transform(CandidateInformation $item): array
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

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformProfileInitialInfo(CandidateInformation $item): array
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

    public function transformPersonalVerification(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'verification' => $this->personalVerification($item)
            ]
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPersonal(CandidateInformation $item): array
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
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPersonalBasic(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'basic' => $this->basicInfo($item)
            ],
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPersonalContact(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'contact' => $this->contactInfo($item)
            ],
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPersonalEssential(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'essential' => $this->essentialInfo($item)
            ],
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPersonalGeneral(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'general' => $this->generellInfo($item)
            ],
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function generellInfo(CandidateInformation $item): array
    {
        return [
            'per_ethnicity' => $item->per_ethnicity,
            'per_mother_tongue' => $item->per_mother_tongue,
            'per_nationality' => +$item->per_nationality,
            'per_country_of_birth' => +$item->per_country_of_birth,
            'per_health_condition' => $item->per_health_condition,
        ];
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function essentialInfo(CandidateInformation $item): array
    {
        return [
            'dob' => $item->dob,
            'per_occupation' => $item->per_occupation,
//            'mobile_number' => $item->mobile_number,
//            'mobile_country_code' => $item->mobile_country_code,
            'per_telephone_no' => $item->per_telephone_no,
            'per_gender' => +$item->per_gender,
            'per_height' => +$item->per_height,
            'per_employment_status' => $item->per_employment_status,
            'per_education_level_id' => +$item->per_education_level_id,
            'per_religion_id' => +$item->per_religion_id,
        ];
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPersonalMoreAbout(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'more_about' => $this->moreabout($item)
            ],
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformPreference(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'preference' => $this->preferenceInfo($item)
            ],
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformFamily(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'family' => $this->familyInfo($item)
            ],
        );
    }


    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function basicInfo(CandidateInformation $item): array
    {
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'screen_name' => $item->screen_name,
            'data_input_status' => $item->data_input_status
        ];
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function preferenceInfo(CandidateInformation $item): array
    {
        $pre_partner_religions = [];
        if (!empty($item->pre_partner_religions)) {
            $pre_partner_religions = explode(",", $item->pre_partner_religions);
        }

        return [
            'pre_partner_age_min' => +$item->pre_partner_age_min,
            'pre_partner_age_max' => +$item->pre_partner_age_max,
            'pre_height_min' => +$item->pre_height_min,
            'pre_height_max' => +$item->pre_height_max,
            'pre_has_country_allow_preference' => boolval($item->pre_has_country_allow_preference),
            'preferred_countries' => $item->preferred_countries,
            'preferred_cities' => $item->preferred_cities,
            'pre_has_country_disallow_preference' => boolval($item->pre_has_country_disallow_preference),
            'bloked_countries' => $item->bloked_countries,
            'blocked_cities' => $item->blocked_cities,
            'preferred_nationality' => $item->preferred_nationality,
            'pre_partner_religion_id' => $pre_partner_religions,
            'pre_ethnicities' => $item->pre_ethnicities,
            'pre_study_level_id' => $item->pre_study_level_id,
            'pre_employment_status' => $item->pre_employment_status,
            'pre_occupation' => $item->pre_occupation,
            'pre_preferred_divorcee' => $item->pre_preferred_divorcee,
            'pre_preferred_divorcee_child' => $item->pre_preferred_divorcee_child,
            'pre_other_preference' => $item->pre_other_preference,
            'pre_description' => $item->pre_description,
            "pre_pros_part_status" => $item->pre_pros_part_status,

            'pre_strength_of_character_rate' => +$item->pre_strength_of_character_rate,
//            'pre_strength_of_character_rate_string' => $item->pre_strength_of_character_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_strength_of_character_rate] : '',
            'pre_look_and_appearance_rate' => +$item->pre_look_and_appearance_rate,
//            'pre_look_and_appearance_rate_string' => $item->pre_look_and_appearance_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_look_and_appearance_rate] : '',
            'pre_religiosity_or_faith_rate' => +$item->pre_religiosity_or_faith_rate,
//            'pre_religiosity_or_faith_rate_string' => $item->pre_religiosity_or_faith_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_religiosity_or_faith_rate] : '',
            'pre_manners_socialskill_ethics_rate' => +$item->pre_manners_socialskill_ethics_rate,
//            'pre_manners_socialskill_ethics_rate_string' => $item->pre_manners_socialskill_ethics_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_manners_socialskill_ethics_rate] : '',
            'pre_emotional_maturity_rate' => +$item->pre_emotional_maturity_rate,
//            'pre_emotional_maturity_rate_string' => $item->pre_emotional_maturity_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_emotional_maturity_rate] : '',
            'pre_good_listener_rate' => +$item->pre_good_listener_rate,
//            'pre_good_listener_rate_string' => $item->pre_good_listener_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_good_listener_rate] : '',
            'pre_good_talker_rate' => +$item->pre_good_talker_rate,
//            'pre_good_talker_rate_string' => $item->pre_good_talker_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_good_talker_rate] : '',
            'pre_wiling_to_learn_rate' => +$item->pre_wiling_to_learn_rate,
//            'pre_wiling_to_learn_rate_string' => $item->pre_wiling_to_learn_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_wiling_to_learn_rate] : '',
            'pre_family_social_status_rate' => +$item->pre_family_social_status_rate,
//            'pre_family_social_status_rate_string' => $item->pre_family_social_status_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_family_social_status_rate] : '',
            'pre_employment_wealth_rate' => +$item->pre_employment_wealth_rate,
//            'pre_employment_wealth_rate_string' => $item->pre_employment_wealth_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_employment_wealth_rate] : '',
            'pre_education_rate' => +$item->pre_education_rate,
//            'pre_education_rate_string' => $item->pre_education_rate ? CandidateInformation::RATE_TO_STRING[$item->pre_education_rate] : '',
            'pre_things_important_status' => $item->pre_things_important_status,
        ];
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function personalInfo(CandidateInformation $item): array
    {
        return [
            'dob' => $item->dob,
            'mobile_number' => $item->mobile_number,
            'mobile_country_code' => $item->mobile_country_code,
            'per_telephone_no' => $item->per_telephone_no,
            'per_gender' => +$item->per_gender,
            'per_height' => +$item->per_height,
            'per_employment_status' => $item->per_employment_status,
            'per_education_level_id' => +$item->per_education_level_id,
            'per_religion_id' => +$item->per_religion_id,
            'per_ethnicity' => $item->per_ethnicity,
            'per_mother_tongue' => $item->per_mother_tongue,
            'per_nationality' => +$item->per_nationality,
            'per_country_of_birth' => +$item->per_country_of_birth,
            'per_current_residence' => +$item->per_current_residence,
            'per_address' => $item->per_address,
            'per_marital_status' => $item->per_marital_status,
            'per_have_children' => boolval($item->per_have_children),
            'per_children' => $item->per_children,
            'per_currently_living_with' => $item->per_currently_living_with,
            'per_willing_to_relocate' => +$item->per_willing_to_relocate,
            'per_smoker' => boolval($item->per_smoker),
            'per_language_speak' => $item->per_language_speak,
            'per_hobbies_interests' => $item->per_hobbies_interests,
            'per_food_cuisine_like' => $item->per_food_cuisine_like,
            'per_things_enjoy' => $item->per_things_enjoy,
            'per_thankfull_for' => $item->per_thankfull_for,
            'per_about' => $item->per_about,
            'per_avatar_url' => url('storage/' . $item->per_avatar_url),
            'per_main_image_url' => url('storage/' . $item->per_main_image_url),
            'anybody_can_see' => $item->anybody_can_see,
            'only_team_can_see' => $item->only_team_can_see,
            'team_connection_can_see' => $item->team_connection_can_see,
        ];
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function contactInfo(CandidateInformation $item): array
    {
        return [
            'per_email' => $item->per_email,
            'per_current_residence_country' => $item->per_current_residence_country,
            'per_current_residence_city' => $item->per_current_residence_city,
            'per_permanent_country' => $item->per_permanent_country,
            'per_permanent_city' => $item->per_permanent_city,
            'per_county' => $item->per_county,
            'per_permanent_post_code' => $item->per_permanent_post_code,
            'per_permanent_address' => $item->per_permanent_address,
            'mobile_country_code' => $item->mobile_country_code,
            'mobile_number' => $item->mobile_number,
        ];
    }


    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function moreabout(CandidateInformation $item): array
    {
        return [
            'per_marital_status' => $item->per_marital_status,
            'per_have_children' => $item->per_have_children,
            'per_children' => $item->per_children,
            'per_currently_living_with' => $item->per_currently_living_with,
            'per_willing_to_relocate' => (int)$item->per_willing_to_relocate,
            'per_smoker' => $item->per_smoker,
            'per_language_speak' => $item->per_language_speak,
            'per_hobbies_interests' => $item->per_hobbies_interests,
            'per_food_cuisine_like' => $item->per_food_cuisine_like,
            'per_things_enjoy' => $item->per_things_enjoy,
            'per_thankfull_for' => $item->per_thankfull_for,
            'per_about' => $item->per_about,
        ];
    }


    /**
     * Extract family info only
     * @param CandidateInformation $item
     * @return array
     */
    private function familyInfo(CandidateInformation $item): array
    {
        return [
            "father_name" => $item->fi_father_name,
            "father_profession" => $item->fi_father_profession,
            "mother_name" => $item->fi_mother_name,
            "mother_profession" => $item->fi_mother_profession,
            "siblings_desc" => $item->fi_siblings_desc,
            "country_of_origin" => $item->fi_country_of_origin,
            "family_info" => $item->fi_family_info,
            "is_publish" => boolval($item->is_publish),
        ];
    }

    /**
     * Extract verification info only
     * @param CandidateInformation $item
     * @return array
     */
    private function personalVerification(CandidateInformation $item): array
    {
        return [
            'ver_country_id' => $item->ver_country_id,
            'ver_city_id' => $item->ver_city_id,
            'ver_document_type' => $item->ver_document_type,
            'ver_image_front' => $item->ver_image_front,
            'ver_image_back' => $item->ver_image_back,
            'ver_recommences_title' => $item->ver_recommences_title,
            'ver_recommences_first_name' => $item->ver_recommences_first_name,
            'ver_recommences_last_name' => $item->ver_recommences_last_name,
            'ver_recommences_occupation' => $item->ver_recommences_occupation,
            'ver_recommences_address' => $item->ver_recommences_address,
            'ver_recommences_mobile_no' => $item->ver_recommences_mobile_no,
            'ver_status' => $item->ver_status,
        ];
    }
}
