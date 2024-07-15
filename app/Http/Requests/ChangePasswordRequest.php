<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends APIRequest
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
            'oldpassword' => 'required',
            'newpassword' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'oldpassword.required' => 'Old Password field is required',
            'newpassword.required' => 'New Password field is required',

        ];
    }
}
