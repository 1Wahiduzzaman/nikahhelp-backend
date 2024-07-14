<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class CandidatePersonalInfoRequest extends APIRequest
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
            'dob' => 'required|date|before:'.date('Y-m-d'),
            'mobile_number' => 'phone:mobile_country_code',
            'mobile_country_code' => 'required_with:mobile_number',
            'per_telephone_no' => 'nullable|max:15',
            'per_gender' => 'required',
            'per_height' => 'required',
            'per_employment_status' => 'required',
            'per_education_level_id' => 'required',
            'per_religion_id' => 'required',
            'per_ethnicity' => 'required',
            'per_mother_tongue' => 'nullable|string',
            'per_nationality' => 'required',
            'per_country_of_birth' => 'required',
            'per_current_residence_country' => 'required',
            'per_current_residence_city' => 'required',
            'per_permanent_country' => 'required',
            'per_permanent_city' => 'required',
            'per_permanent_post_code' => 'required',
            'per_permanent_address' => 'required',
            'per_marital_status' => 'required|string',
            'per_have_children' => 'required_if:per_marital_status,divorced_with_children| boolean',
            'per_children' => [
                'required_if:per_have_children,true',
                'array',
                function ($attribute, $values, $fail) {
                    foreach ($values as $key => $value) {
                        if (! Arr::exists($value, 'type')) {
                            $fail('The '.$attribute.' is invalid.');
                        }
                        if (! Arr::exists($value, 'count')) {
                            $fail('The '.$attribute.' is invalid.');
                        }
                        if (! Arr::exists($value, 'age')) {
                            $fail('The '.$attribute.' is invalid.');
                        }
                    }

                }],
            'per_currently_living_with' => 'nullable|string',
            'per_willing_to_relocate' => 'required',
            'per_smoker' => 'required|string',
            'per_language_speak' => 'required|string',
            'per_hobbies_interests' => 'nullable|string',
            'per_food_cuisine_like' => 'nullable|string',
            'per_things_enjoy' => 'nullable|string',
            'per_thankfull_for' => 'nullable|string',
            'per_about' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'dob.required' => 'The date of birth field is required.',
            'dob.date' => 'The date of birth should be a valid date.',
            'dob.before' => 'The date of birth should be a valid date before today.',
            'mobile_number.required' => 'The mobile number field is required.',
            'per_gender.required' => 'The per gender field is required.',
            'per_height.required' => 'The height field is required.',
            'per_employment_status.required' => 'The employment status field is required.',
            'per_education_level_id.required' => 'The education level id field is required.',
            'per_religion_id.required' => 'The religion field is required.',
            'per_ethnicity.required' => 'The ethnicity field is required.',
            'per_nationality.required' => 'The nationality field is required.',
            'per_country_of_birth.required' => 'The country of birth field is required.',
            'per_current_residence.required' => 'The current residence field is required.',
            'per_willing_to_relocate.required' => 'The willing to relocate field is required.',
            'per_smoker.required' => 'The smoker field is required.',
            'per_language_speak.required' => 'The language speak field is required.',
        ];
    }
}
