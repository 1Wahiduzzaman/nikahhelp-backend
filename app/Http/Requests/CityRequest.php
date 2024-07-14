<?php

namespace App\Http\Requests;

class CityRequest extends APIRequest
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
            'id' => 'sometimes|required',
            'country_id' => 'required',
            'name' => 'required|max:255|unique:cities',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'This city name already exists',
        ];
    }
}
