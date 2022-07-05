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
            'gender' => 'nullable|numeric|1',
            'ethnicity' => 'nullable',
            'employment_status' => 'nullable',
            'nationality' => 'nullable',
            'religion' => 'nullable',
            'marital_status' => 'nullable',
        ];
    }
}
