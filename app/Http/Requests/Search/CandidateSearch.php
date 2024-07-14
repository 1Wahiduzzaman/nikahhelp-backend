<?php

namespace App\Http\Requests\Search;

use App\Http\Requests\APIRequest;

class CandidateSearch extends APIRequest
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
            'min_height' => 'required|nullable|numeric',
            'max_height' => 'required|nullable|numeric',
            'country' => 'nullable|numeric',
            'gender' => 'required|numeric|min:1|max:2',
            'ethnicity' => 'nullable',
            'employment_status' => 'string|nullable',
            'nationality' => 'string|nullable',
            'religion' => 'nullable|numeric',
            'marital_status' => 'nullable',
            'min_age' => 'numeric|min:18',
            'max_age' => 'numeric|max:60',
        ];
    }
}
