<?php


namespace App\Http\Requests\Candidate;
use App\Http\Requests\APIRequest;
use Illuminate\Validation\Rule;

class CandidatePersonalContactInformationRequest  extends APIRequest
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
            'per_email' => 'nullable|max:255',
            'mobile_country_code' => 'nullable|string',
            'mobile_number' => 'nullable|max:255',
            'per_current_residence_country' => 'nullable|max:255',
            'per_current_residence_city' => 'nullable|max:255',
            'per_permanent_country' => 'nullable|max:255',
            'per_permanent_city' => 'nullable|max:255',
            'per_county' => 'nullable|max:255',
            'per_permanent_post_code' => 'nullable|max:255',
            'per_permanent_address' => 'nullable|max:255',
            'address_1' => 'nullable|string',
            'address_2' => 'nullable|string',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.'
        ];
    }
}
