<?php

namespace App\Http\Requests;

class DeleteReasonSubmitRequest extends APIRequest
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
            'team_id' => 'required|string|exists:teams,team_id',
            'reason_type' => 'required|string|max:255',
            'reason_text' => 'required|string|max:255',
        ];
    }
}
