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
            'per_email' => 'required|max:255',
            'mobile_number' => 'required|max:255',
            'per_current_residence_country' => 'required|max:255',
            'per_current_residence_city' => 'required|max:255',
            'per_permanent_country' => 'required|max:255',
            'per_permanent_city' => 'required|max:255',
            'per_county' => 'required|max:255',
            'per_permanent_post_code' => 'required|max:255',
            'per_permanent_address' => 'required|max:255'
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
