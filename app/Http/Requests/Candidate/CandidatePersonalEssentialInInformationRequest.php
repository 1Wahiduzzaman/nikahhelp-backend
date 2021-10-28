<?php


namespace App\Http\Requests\Candidate;

use App\Http\Requests\APIRequest;
use Illuminate\Validation\Rule;

class CandidatePersonalEssentialInInformationRequest  extends APIRequest
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
            'per_gender'=> 'required',
            'per_height'=> 'required',
            'per_employment_status'=> 'required',
            'per_education_level_id'=> 'required',
            'per_religion_id'=> 'required',
            'per_occupation'=> 'required',
        ];
    }

    public function messages()
    {
        return [
            'dob.required' => 'The date of birth field is required.',
            'dob.date' => 'The date of birth should be a valid date.',
            'dob.before' => 'The date of birth should be a valid date before today.',
            'per_gender.required' => 'The per gender field is required.',
            'per_height.required' => 'The height field is required.',
            'per_employment_status.required' => 'The employment status field is required.',
            'per_education_level_id.required' => 'The education level id field is required.',
            'per_religion_id.required' => 'The religion field is required.',
            'per_occupation.required' => 'The occupation field is required.',
        ];
    }
}
