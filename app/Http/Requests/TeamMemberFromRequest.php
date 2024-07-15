<?php

namespace App\Http\Requests;

use App\Models\TeamMember;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeamMemberFromRequest extends APIRequest
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
        $request = $this->request->all();

        switch (Str::upper($this->method())) {
            case 'POST':
                return [
                    TeamMember::TEAM_ID => [
                        'required',
                        'numeric',
                        'exists:teams,id',
                        Rule::unique('team_members')
                            ->where(function ($query) use ($request) {
                                return $query->where('team_id', $request['team_id'])
                                    ->where('user_id', $request['user_id']);
                            }),
                    ],
                    TeamMember::USER_ID => 'required|numeric|exists:users,id',
                    TeamMember::USER_TYPE => 'required|string|max:255',
                    TeamMember::ROLE => 'required|string|max:255',
                ];
                break;
        }

    }

    public function messages()
    {
        return [
            TeamMember::TEAM_ID.'.unique' => 'This user is already part of the Team',
        ];

    }
}
