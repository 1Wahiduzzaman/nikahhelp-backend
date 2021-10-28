<?php


namespace App\Http\Requests\Matchmaker;

use App\Models\RepresentativeInformation;
use App\Http\Requests\APIRequest;

class EssentialInformationRequest extends APIRequest
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
            'per_gender' => 'required|max:255',
            'dob' => 'sometimes|required|string|max:255',
            'per_occupation' => 'required|max:255',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'per_gender.required' => 'Gender field is required',
            'per_occupation.required' => 'Occupation field is required',
            'dob.required' => 'Date of birth field is required'
        ];
    }

}
