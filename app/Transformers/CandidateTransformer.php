<?php


namespace App\Transformers;

use App\Models\CandidateImage;
use App\Models\CandidateInformation;
use App\Models\Religion;
use App\Models\User;
use Carbon\Carbon;
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

    /**
     * @param CandidateInformation $item
     * @return array
     */
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
    public function transformPersonalInfo(CandidateInformation $item): array
    {
        return array_merge(
            $this->basicInfo($item),
            [
                'personal' => $this->personalInfo($item)
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
    public function transformSearchResult(CandidateInformation $item): array
    {
        return $this->candidateCartData($item);
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function candidateSearchData(CandidateInformation $item)
    {
        return $this->candidateCartData($item);
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    public function transformShortListUser(User $item): array
    {
        return array_merge(
            $this->candidateCartData($item->getCandidate),
            $this->candidateShortListedAdditionalData($item->pivot)
        );
    }

    /**
     * @param CandidateInformation $item
     * @return array
     */
    private function candidateCartData(CandidateInformation $item): array
    {
        return [
            'user_id' => $item->user_id,
            'image' => CandidateImage::getCandidateMainImage($item->user_id),
            'first_name' => $item->first_name,
            'last_name' => $item->last_name,
            'screen_name' => $item->screen_name,
            'per_age' => Carbon::now()->diffInYears($item->dob),
            'per_gender' => CandidateInformation::getGender($item->per_gender),
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

    /**
     * @param array $item
     * @return array
     */
    private function candidateShortListedAdditionalData($item): array
    {
        $shortListedByUser = CandidateInformation::where('user_id', $item->shortlisted_by)->first();
        return [
            'short_listed_by' => $item->shortlisted_by,
            'short_listed_at' => $item->created_at,
        ];
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
            'per_nationality' => (int)$item->per_nationality,
            'per_country_of_birth' => (int)$item->per_country_of_birth,
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
            'per_gender_id' => $item->per_gender,
            'per_gender' => CandidateInformation::getGender($item->per_gender),
            'per_height' => (int)$item->per_height,
            'per_employment_status' => $item->per_employment_status,
            'per_education_level_id' => (int)$item->per_education_level_id,
            'per_education_level' => $item->candidateEducationLevel()->exists() ? $item->candidateEducationLevel->name : null,
            'per_religion_id' => (int)$item->per_religion_id,
            'per_religion' => $item->getReligion()->exists() ? $item->getReligion->name : null,
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
        $pre_partner_religions_id = [];
        if (!empty($item->pre_partner_religions)) {
            $pre_partner_religions_id = explode(',', $item->pre_partner_religions);
            $pre_partner_religions = implode("", Religion::where('id',$pre_partner_religions_id)->pluck('name')->toArray());
        }

        return [
            'pre_partner_age_min' => (int)$item->pre_partner_age_min,
            'pre_partner_age_max' => (int)$item->pre_partner_age_max,
            'pre_height_min' => (int)$item->pre_height_min,
            'pre_height_max' => (int)$item->pre_height_max,
            'pre_has_country_allow_preference' => boolval($item->pre_has_country_allow_preference),
            'preferred_countries' => $item->preferred_countries,
            'preferred_cities' => $item->preferred_cities,
            'pre_has_country_disallow_preference' => boolval($item->pre_has_country_disallow_preference),
            'bloked_countries' => $item->bloked_countries,
            'blocked_cities' => $item->blocked_cities,
            'preferred_nationality' => $item->preferred_nationality,
            'pre_partner_religion_id' => $pre_partner_religions_id,
            'pre_partner_religion' => $pre_partner_religions,
            'pre_ethnicities' => $item->pre_ethnicities,
            'pre_study_level_id' => $item->pre_study_level_id,
            'pre_study_level' => $item->preEducationLevel()->exists() ? $item->preEducationLevel->name : null,
            'pre_employment_status' => $item->pre_employment_status,
            'pre_occupation' => $item->pre_occupation,
            'pre_occupation_list' => $item->pre_occupation ? $item->pre_occupation : json_decode($item->pre_occupation),
            'pre_preferred_divorcee' => $item->pre_preferred_divorcee,
            'pre_preferred_divorcee_child' => $item->pre_preferred_divorcee_child,
            'pre_other_preference' => $item->pre_other_preference,
            'pre_description' => $item->pre_description,
            "pre_pros_part_status" => $item->pre_pros_part_status,
            'pre_strength_of_character_rate' => (int)$item->pre_strength_of_character_rate,
            'pre_look_and_appearance_rate' => (int)$item->pre_look_and_appearance_rate,
            'pre_religiosity_or_faith_rate' => (int)$item->pre_religiosity_or_faith_rate,
            'pre_manners_socialskill_ethics_rate' => (int)$item->pre_manners_socialskill_ethics_rate,
            'pre_emotional_maturity_rate' => (int)$item->pre_emotional_maturity_rate,
            'pre_good_listener_rate' => (int)$item->pre_good_listener_rate,
            'pre_good_talker_rate' => (int)$item->pre_good_talker_rate,
            'pre_wiling_to_learn_rate' => (int)$item->pre_wiling_to_learn_rate,
            'pre_family_social_status_rate' => (int)$item->pre_family_social_status_rate,
            'pre_employment_wealth_rate' => (int)$item->pre_employment_wealth_rate,
            'pre_education_rate' => (int)$item->pre_education_rate,
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
            'per_gender' => (int)$item->per_gender,
            'per_height' => (int)$item->per_height,
            'per_employment_status' => $item->per_employment_status,
            'per_education_level_id' => (int)$item->per_education_level_id,
            'per_education_level' => $item->candidateEducationLevel()->exists() ? $item->candidateEducationLevel->name : null,
            'per_religion_id' => (int)$item->per_religion_id,
            'per_ethnicity' => $item->per_ethnicity,
            'per_mother_tongue' => $item->per_mother_tongue,
            'per_nationality' => (int)$item->per_nationality,
            'per_country_of_birth_id' => (int)$item->per_country_of_birth,
            'per_country_of_birth' => $item->getCountryOFBirth()->exists() ? $item->getCountryOFBirth->name : null,
            'per_current_residence_id' => (int)$item->per_current_residence_country,
            'per_current_residence' => $item->getCurrentResidenceCountry()->exists() ? $item->getCurrentResidenceCountry->name : null,
            'per_address' => $item->per_address,
            'per_marital_status' => $item->per_marital_status,
            'per_have_children' => boolval($item->per_have_children),
            'per_children' => $item->per_children,
            'per_currently_living_with' => $item->per_currently_living_with,
            'per_willing_to_relocate' => (int)$item->per_willing_to_relocate,
            'per_smoker' => boolval($item->per_smoker),
            'per_language_speak' => $item->per_language_speak,
            'per_hobbies_interests' => $item->per_hobbies_interests,
            'per_food_cuisine_like' => $item->per_food_cuisine_like,
            'per_things_enjoy' => $item->per_things_enjoy,
            'per_thankfull_for' => $item->per_thankfull_for,
            'per_about' => $item->per_about,
            // 'per_avatar_url' => $item->per_avatar_url ? env('IMAGE_SERVER') . '/' . $item->per_avatar_url : '',
            'per_avatar_url' => $item->per_avatar_url ? $item->per_avatar_url : '',
            'per_main_image_url' => CandidateImage::getCandidateMainImage($item->user_id),
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
            'per_current_residence_country_name' => $item->getCurrentResidenceCountry()->exists() ? $item->getCurrentResidenceCountry->name : null,
            'per_current_residence_city' => $item->per_current_residence_city,
            'per_permanent_country' => $item->per_permanent_country,
            'per_permanent_country_name' => $item->getPermanentCountry()->exists() ? $item->getPermanentCountry->name :null,
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
        $per_improve_myself = [];
        if (!empty($item->per_improve_myself)) {
            $per_improve_myself = json_decode($item->per_improve_myself);
        }
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
            'per_improve_myself' => $per_improve_myself,
            'per_additional_info_text' => $item->per_additional_info_text,
            'per_additional_info_doc' => $item->downloadable_doc,
            'per_additional_info_doc_title' => $item->per_additional_info_doc_title,
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
            'ver_image_front' => $item->ver_image_front ? env('IMAGE_SERVER') . '/' . $item->ver_image_front : '',
            'ver_image_back' => $item->ver_image_back ? env('IMAGE_SERVER') . '/' . $item->ver_image_back : '',
            'ver_recommences_title' => $item->ver_recommences_title,
            'ver_recommences_first_name' => $item->ver_recommences_first_name,
            'ver_recommences_last_name' => $item->ver_recommences_last_name,
            'ver_recommences_occupation' => $item->ver_recommences_occupation,
            'ver_recommences_address' => $item->ver_recommences_address,
            'ver_recommences_mobile_no' => $item->ver_recommences_mobile_no,
            'ver_status' => $item->ver_status,
        ];
    }

    /**
     * Create Candidate Other image Image data
     * Change image to lock image if there is no permission to see
     * @param object $item
     * @param bool $isPermit
     * @return object
     */
    public function candidateOtherImage(object $item,bool $isPermit = false): object
    {
        for ($i = 0; $i < count($item); $i++) {
            if($item[$i]->image_type == 1){
                $item[$i]->image_path = $item[$i]->image_path ? env('IMAGE_SERVER') . '/' . $item[$i]->image_path : '';
            }else{
                if($isPermit){
                    $item[$i]->image_path = $item[$i]->image_path ? env('IMAGE_SERVER') . '/' . $item[$i]->image_path : '';
                }else{
                    $item[$i]->image_path = env('IMAGE_SERVER') . '/' . 'site_img/image_lock.jpg';
                }
            }
        }
        return $item;
    }

}
