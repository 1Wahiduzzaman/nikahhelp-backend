<?php

namespace App\Http\Requests\Candidate;

use App\Http\Requests\APIRequest;

class CandidatePreferenceRatingRequest extends APIRequest
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
            'pre_pros_part_status' => 'nullable|numeric|between:0,3',
            'pre_strength_of_character_rate' => 'nullable|numeric|between:0,5',
            'pre_look_and_appearance_rate' => 'nullable|numeric|between:0,5',
            'pre_religiosity_or_faith_rate' => 'nullable|numeric|between:0,5',
            'pre_manners_socialskill_ethics_rate' => 'nullable|numeric|between:0,5',
            'pre_emotional_maturity_rate' => 'nullable|numeric|between:0,5',
            'pre_good_listener_rate' => 'nullable|numeric|between:0,5',
            'pre_good_talker_rate' => 'nullable|numeric|between:0,5',
            'pre_wiling_to_learn_rate' => 'nullable|numeric|between:0,5',
            'pre_family_social_status_rate' => 'nullable|numeric|between:0,5',
            'pre_employment_wealth_rate' => 'nullable|numeric|between:0,5',
            'pre_education_rate' => 'nullable|numeric|between:0,5',
            'pre_things_important_status' => 'nullable|numeric|between:0,5',
        ];
    }

    public function messages()
    {
        return [
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
