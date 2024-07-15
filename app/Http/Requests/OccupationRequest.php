<?php

namespace App\Http\Requests;

class OccupationRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:occupations',
        ];
    }
}
