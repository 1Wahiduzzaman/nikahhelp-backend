<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketSubmissionRequest extends FormRequest
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
            'issue_type' => 'required|string',
            'issue' => 'required|string',
            'screen_shot' => 'sometimes|image|mimes:jpeg,png,jpg,webp,avf|max:2048',
            'user' => 'json|required'
        ];
    }
}
