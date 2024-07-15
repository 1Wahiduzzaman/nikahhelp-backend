<?php

namespace App\Http\Requests\Matchmaker;

use App\Http\Requests\APIRequest;

class ImageUploadRequest extends APIRequest
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
            'per_avatar_url' => 'sometimes|required|string',
            'per_main_image_url' => 'sometimes|required|string',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.',
        ];
    }
}
