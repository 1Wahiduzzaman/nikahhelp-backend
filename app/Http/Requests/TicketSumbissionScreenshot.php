<?php

namespace App\Http\Requests;

class TicketSumbissionScreenshot extends APIRequest
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
            'screen_shot' => 'sometimes|image|mimes:jpeg,png,jpg,webp,avf|max:2048',
        ];
    }
}
