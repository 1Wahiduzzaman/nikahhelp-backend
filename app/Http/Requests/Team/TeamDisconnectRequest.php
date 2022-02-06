<?php

namespace App\Http\Requests\Team;


use App\Models\TeamMember;
use Illuminate\Validation\Rule;
use App\Http\Requests\APIRequest;

class TeamDisconnectRequest extends APIRequest
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
            'team_id' => 'required',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [];
    }
}
