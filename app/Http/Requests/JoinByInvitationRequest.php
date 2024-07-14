<?php

namespace App\Http\Requests;

class JoinByInvitationRequest extends APIRequest
{
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
            'invitation_link' => 'required|string',
            'team_password' => 'required|string',
        ];
    }
}
