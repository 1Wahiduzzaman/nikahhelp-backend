<?php


namespace App\Http\Requests\Representative;

use App\Models\RepresentativeInformation;
use App\Http\Requests\APIRequest;
use Validator;

class VerifyIdentityRequest extends APIRequest
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
            'is_document_upload' => 'nullable|boolean|max:2',
            'ver_country' => 'nullable|max:255',
            'ver_city' => 'nullable|max:255',
            'ver_document_frontside' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,pdf,tiff,jiff',
            'ver_document_backside' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,pdf,tiff,jiff',
            'ver_recommender_title' => 'nullable|max:255',
            'ver_recommender_first_name' => 'nullable|max:255',
            'ver_recommender_last_name' => 'nullable|max:255',
            'ver_recommender_occupation' => 'nullable|max:255',
            'ver_recommender_address' => 'nullable|max:255',
            'ver_recommender_mobile_no' => 'nullable|max:255',
            'ver_recommender_email' => 'nullable|string|email'
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
