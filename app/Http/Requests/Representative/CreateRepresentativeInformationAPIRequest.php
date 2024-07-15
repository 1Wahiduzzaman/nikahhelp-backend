<?php

namespace App\Http\Requests\Representative;

use App\Http\Requests\APIRequest;

//use InfyOm\Generator\Request\APIRequest;

class CreateRepresentativeInformationAPIRequest extends APIRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'screen_name' => 'required|max:255',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'first_name.required' => 'First name field is required',
            'first_name.max' => 'First name maximum input size 255',
            'screen_name.max' => 'Screen name maximum input size 255',
            'screen_name.required' => 'Screen name field is required',
        ];
    }
}
