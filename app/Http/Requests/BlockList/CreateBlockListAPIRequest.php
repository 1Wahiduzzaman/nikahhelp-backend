<?php

namespace App\Http\Requests\BlockList;

use App\Http\Requests\APIRequest;

class CreateBlockListAPIRequest extends APIRequest
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
            'user_id' => 'required',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'user_id.required' => 'Candidate ID is required',
        ];
    }
}
