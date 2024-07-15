<?php

namespace App\Http\Requests;

class MemberInvitationRequest extends APIRequest
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
            'team_id' => 'required|string|exists:teams,team_id',
            'members' => 'present|array',
            'members.*.role' => 'required|max:55',
            'members.*.add_as_a' => 'required|max:255',
            'members.*.relationship' => 'required|max:100',
            'members.*.invitation_link' => 'required|max:150|unique:App\Models\TeamMemberInvitation,link|distinct',
        ];
    }

    public function messages()
    {
        return [
            'team_id.required' => 'Team ID is required',
            'team_id.integer' => 'Team ID must be an Integer',
            'team_id.exists' => 'Team not found',
            'members.*.role.required' => 'Member role is required',
            'members.*.role.max' => 'Member role can not be more than 55 charecter',
            'members.*.add_as_a.required' => 'Add as field is required',
            'members.*.add_as_a.max' => 'Add as field can not be more than 255 charecter',
            'members.*.relationship.required' => 'Relationship field is required',
            'members.*.relationship.max' => 'Relationship field can not be more than 100 charecter',
            'members.*.invitation_link.required' => 'Invitation_link field is required',
            'members.*.invitation_link.max' => 'Invitation_link field can not be more than 100 charecter',
        ];
    }
}
