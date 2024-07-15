<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\APIRequest;

class TeamLeaveRequest extends APIRequest
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
            'team_id' => 'required',
            'new_owner' => 'sometimes|required',
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
