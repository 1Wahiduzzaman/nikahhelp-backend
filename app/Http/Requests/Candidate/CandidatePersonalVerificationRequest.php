<?php


namespace App\Http\Requests\Candidate;

use App\Http\Requests\APIRequest;
use Illuminate\Validation\Rule;

class CandidatePersonalVerificationRequest  extends APIRequest
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
            'ver_country_id'=> 'nullable|numeric',
            'ver_city_id'=> 'nullable|numeric',
            'ver_document_type'=> 'nullable|string|max:255',
            'ver_image_front'=> 'nullable|string',
            'ver_image_back'=> 'nullable|string',
            'ver_recommences_title'=> 'nullable|string|max:255',
            'ver_recommences_first_name'=> 'nullable|string|max:255',
            'ver_recommences_last_name'=> 'nullable|string|max:255',
            'ver_recommences_occupation'=> 'nullable|string|max:255',
            'ver_recommences_address'=> 'nullable|string|max:255',
            'ver_recommences_mobile_no'=> 'nullable|string|max:255',
            'ver_status'=> 'nullable|numeric',
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
