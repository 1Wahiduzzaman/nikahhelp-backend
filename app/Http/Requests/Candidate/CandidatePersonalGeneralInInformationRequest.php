<?php


namespace App\Http\Requests\Candidate;

use App\Http\Requests\APIRequest;
use Illuminate\Validation\Rule;


class CandidatePersonalGeneralInInformationRequest extends APIRequest
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
            'per_ethnicity'=> 'required',
            'per_mother_tongue'=> 'nullable|string',
            'per_nationality'=> 'required',
            'per_country_of_birth'=> 'required',
            'per_health_condition'=> 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'per_ethnicity.required' => 'The ethnicity field is required.',
            'per_nationality.required' => 'The nationality field is required.',
            'per_country_of_birth.required' => 'The country of birth field is required.',
        ];
    }
}
