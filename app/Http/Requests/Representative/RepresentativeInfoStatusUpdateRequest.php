<?php

namespace App\Http\Requests\Representative;

use App\Http\Requests\APIRequest;

class RepresentativeInfoStatusUpdateRequest extends APIRequest
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
            'data_input_status' => 'required|numeric',
        ];
    }
}
