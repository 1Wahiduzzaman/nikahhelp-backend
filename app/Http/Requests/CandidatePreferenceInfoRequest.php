<?php

namespace App\Http\Requests;

class CandidatePreferenceInfoRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pre_partner_age_min' => 'required|digits_between:1,100',
            'pre_partner_age_max' => 'required|digits_between:1,100',
            'pre_height_min' => 'required|digits_between:1,100',
            'pre_height_max' => 'required|digits_between:1,100',
            'pre_has_country_allow_preference' => 'sometimes|boolean',
            'pre_countries' => 'required_if:pre_has_country_allow_preference,1|array|min:1',
            'pre_countries.*' => 'required_if:pre_has_country_allow_preference,1|numeric|distinct|exists:countries,id',
            'pre_cities' => 'required_if:pre_has_country_allow_preference,1|array|min:1',
            'pre_cities.*' => 'required_if:pre_has_country_allow_preference,1|numeric|distinct|exists:cities,id',
            'pre_nationality' => 'sometimes|array|min:1',
            'pre_nationality.*' => 'sometimes|numeric|distinct|exists:countries,id',
            'pre_has_country_disallow_preference' => 'sometimes|boolean',
            'pre_disallow_countries' => 'required_if:pre_has_country_disallow_preference,1|array|min:1',
            'pre_disallow_countries.*' => 'required_if:pre_has_country_disallow_preference,1|numeric|distinct|exists:countries,id',
            'pre_disallow_cities' => 'required_if:pre_has_country_disallow_preference,1|array|min:1',
            'pre_disallow_cities.*' => 'required_if:pre_has_country_disallow_preference,1|numeric|distinct|exists:cities,id',
            'pre_partner_religions' => 'sometimes|string',
            'pre_ethnicities' => 'sometimes|string|max:255',
            'pre_study_level_id' => 'sometimes|exists:study_level,id',
            'pre_employment_status' => 'sometimes',
            'pre_occupation' => 'sometimes',
            'pre_preferred_divorcee' => 'sometimes|boolean',
            'pre_preferred_divorcee_child' => 'sometimes|boolean',
            'pre_other_preference' => 'sometimes|string',
            'pre_description' => 'sometimes|string',
            'pre_pros_part_status' => 'sometimes|digits_between:1,3',

            'pre_strength_of_character_rate' => 'sometimes|digits_between:1,5',
            'pre_look_and_appearance_rate' => 'sometimes|digits_between:1,5',
            'pre_religiosity_or_faith_rate' => 'sometimes|digits_between:1,5',
            'pre_manners_socialskill_ethics_rate' => 'sometimes|digits_between:1,5',
            'pre_emotional_maturity_rate' => 'sometimes|digits_between:1,5',
            'pre_good_listener_rate' => 'sometimes|digits_between:1,5',
            'pre_good_talker_rate' => 'sometimes|digits_between:1,5',
            'pre_wiling_to_learn_rate' => 'sometimes|digits_between:1,5',
            'pre_family_social_status_rate' => 'sometimes|digits_between:1,5',
            'pre_employment_wealth_rate' => 'sometimes|digits_between:1,5',
            'pre_education_rate' => 'sometimes|digits_between:1,5',
            'pre_things_important_status' => 'sometimes|digits_between:1,3',
        ];
    }

    public function messages()
    {
        return [
            'pre_partner_age_min.*' => 'Value required and must be between 1 to 100',
            'pre_partner_age_max.*' => 'Value required and must be between 1 to 100',
            'pre_height_min.*' => 'Value required and must be between 1 to 100',
            'pre_height_max.*' => 'Value required and must be between 1 to 100',
            'pre_has_country_allow_preference.*' => 'Value should be (boolean) `Yes` or `No`',
            'pre_countries.*' => 'Value should be valid country',
            'pre_cities.*' => 'Value should be valid City',
            'pre_disallow_countries.*' => 'Value should be valid country',
            'pre_disallow_cities.*' => 'Value should be valid City',
            'pre_has_country_disallow_preference.*' => 'Value should be (boolean) `Yes` or `No`',
            'pre_partner_religion_id.*' => 'Value should be a valid Religion',
            'pre_ethnicities.*' => 'Value can be at most 255 character.',
            'pre_study_level_id.*' => 'Value should be a valid level of education',
            'pre_employment_status.*' => 'Value can be at most 255 character.',
            'pre_occupation.*' => 'Value can be at most 255 character.',
            'pre_preferred_divorcee.*' => 'Should be a valid value',
            'pre_preferred_divorcee_child.*' => 'Should be a valid value',
            'pre_other_preference.*' => 'Should be a valid value',
            'pre_description.*' => 'Should be a valid value',
            'pre_pros_part_status.*' => 'Value required and must be between 1 to 3',

            'pre_strength_of_character_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_look_and_appearance_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_religiosity_or_faith_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_manners_socialskill_ethics_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_emotional_maturity_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_good_listener_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_good_talker_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_wiling_to_learn_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_family_social_status_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_employment_wealth_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_education_rate.*' => 'Value required and must be between `Important` to `Extremely Important`',
            'pre_things_important_status.*' => 'Value required and must be between 1 to 3',
        ];
    }
}
