<?php

namespace App\Http\Requests\Candidate;

use App\Http\Requests\APIRequest;
use Illuminate\Validation\Rule;

class CandidatePreferenceAboutRequest extends APIRequest
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
            'pre_partner_age_min' => 'nullable|numeric|between:1,100',
            'pre_partner_age_max' => 'nullable|numeric|between:1,100',
            'pre_height_min' => 'nullable|numeric|between:1,1000',
            'pre_height_max' => 'nullable|numeric|between:1,1000',
            'pre_has_country_allow_preference' => 'nullable|boolean',
            'pre_partner_comes_from.*.country' =>
            'nullable|required_if:pre_has_country_allow_preference,1|numeric|exists:countries,id',
            'pre_partner_comes_from.*.city' =>
            'nullable|required_if:pre_has_country_allow_preference,1|numeric|exists:cities,id',
            'pre_has_country_disallow_preference' => 'nullable||boolean',
            'pre_disallow_countries.*.country' =>
            'nullable|required_if:pre_has_country_disallow_preference,1|numeric|distinct|exists:countries,id',
            'pre_disallow_countries.*.city' =>
            'nullable|required_if:pre_has_country_disallow_preference,1|numeric|distinct|exists:countries,id',
            'pre_partner_religions' => 'nullable|string',
            'pre_nationality' => 'nullable|array|min:1',
            'pre_nationality.*' =>
            'nullable|numeric|distinct|exists:countries,id',
            'pre_ethnicities' => 'nullable|string|max:255',
            'pre_study_level_id' => 'nullable|exists:study_level,id',
            'pre_employment_status' => 'sometimes',
            'pre_occupation' => 'sometimes',
            'pre_preferred_divorcee' => 'nullable|boolean',
            'pre_preferred_divorcee_child' => 'nullable|boolean',
            'pre_other_preference' => 'nullable|string',
            'pre_description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'pre_partner_age_min.*' =>
            'Value required and must be between 1 to 100',
            'pre_partner_age_max.*' =>
            'Value required and must be between 1 to 100',
            'pre_height_min.*' =>
            'Value required and must be between 1 to 100',
            'pre_height_max.*' =>
            'Value required and must be between 1 to 100',
            'pre_has_country_allow_preference.*' =>
            'Value should be (boolean) `Yes` or `No`',
//            'pre_partner_comes_from.*' => 'Value should be valid country',
            //            'pre_cities.*' => 'Value should be valid City',
            'pre_disallow_countries.*.country' =>
            'Value should be valid country',
            'pre_partner_comes_from.*.city' => 'Value should be valid City',
            'pre_has_country_disallow_preference.*' =>
            'Value should be (boolean) `Yes` or `No`',
            'pre_partner_religion_id.*' => 'Value should be a valid Religion',
            'pre_ethnicities.*' => 'Value can be at most 255 character.',
            'pre_study_level_id.*' =>
            'Value should be a valid level of education',
            'pre_employment_status.*' => 'Value can be at most 255 character.',
            'pre_occupation.*' => 'Value can be at most 255 character.',
            'pre_preferred_divorcee.*' => 'Should be a valid value',
            'pre_preferred_divorcee_child.*' => 'Should be a valid value',
            'pre_other_preference.*' => 'Should be a valid value',
            'pre_description.*' => 'Should be a valid value',
        ];
    }

}
