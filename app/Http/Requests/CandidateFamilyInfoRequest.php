<?php

namespace App\Http\Requests;

class CandidateFamilyInfoRequest extends APIRequest
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
            'uid' => 'nullable',
            'father_name' => 'nullable|string|max:255',
            'father_profession' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_profession' => 'nullable|string|max:255',
            'siblings_desc' => 'nullable|string|max:255',
            'country_of_origin' => 'nullable|string',
            'family_info' => 'nullable|string|max:255',
            'is_publish' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'uid.required' => 'User ID is required',
            'father_name.required' => 'Father name is required.',
            'father_name.string' => 'Father name must be a string.',
            'father_name.max' => 'Father name length can not be more than 255 characters.',
            'father_profession.max' => 'Father profession length can not be more than 255 characters.',
            'mother_name.required' => 'Mother name is required.',
            'mother_profession.max' => 'Mother profession length can not be more than 255 characters.',
            'siblings_desc.max' => 'Siblings Description length can not be more than 255 characters.',
            'country_of_origin.required' => 'Country of origin is required.',
            'family_info.max' => 'Family info length can not be more than 255 characters.',
            'is_publish.boolean' => 'Is publish flag needs to have a boolean value.',
        ];
    }
}
