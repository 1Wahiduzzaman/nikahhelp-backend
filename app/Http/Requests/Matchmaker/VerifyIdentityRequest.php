<?php


namespace App\Http\Requests\Matchmaker;

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
            'is_document_upload' => 'required|boolean|max:2',
            'ver_country' => 'required_if:is_document_upload,1|required|max:255',
            'ver_city' => 'sometimes|required|max:255',
            'ver_document_frontside' => 'sometimes|required_if:is_document_upload,1|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ver_document_backside' => 'sometimes|required_if:is_document_upload,1|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ver_recommender_title' => 'required_if:is_document_upload,1|required|max:255',
            'ver_recommender_first_name' => 'required_if:is_document_upload,1|required|max:255',
            'ver_recommender_last_name' => 'required_if:is_document_upload,1|required|max:255',
            'ver_recommender_occupation' => 'required_if:is_document_upload,1|required|max:255',
            'ver_recommender_address' => 'required_if:is_document_upload,1|required|max:255',
            'ver_recommender_mobile_no' => 'required_if:is_document_upload,1|required|max:255',
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
