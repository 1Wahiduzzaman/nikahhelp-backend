<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidateSearch extends FormRequest
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
            'min_height' => 'nullable|numeric',
            'max_height' => 'nullable|numeric',
            'country' => 'nullable|numeric',
            'gender' => 'required|numeric|min:1|max:2',
            'ethnicity' => 'nullable',
            'employment_status' => 'string|nullable',
            'nationality' => 'string|nullable',
            'religion' => 'nullable|numeric',
            'marital_status' => 'nullable',
            'min_age' => 'numeric|min:18',
            'max_age' => 'numeric|max:60'
        ];
    }
}
