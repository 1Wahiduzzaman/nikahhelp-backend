<?php

namespace App\Http\Requests;

use App\Models\Team;

class TeamFromRequest extends APIRequest
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
            //            Team::TEAM_ID => 'required|string|max:10',
            Team::NAME => 'required|string|max:255',
            Team::DESCRIPTION => 'required|string|max:255',
            Team::PASSWORD => 'required|string|max:80',
            Team::LOGO => 'required|string',

            //            Team::MEMBER_COUNT => 'sometimes|numeric',
            //            Team::SUBSCRIPTION_EXPIRE_AT => 'sometimes|date',
            //            Team::STATUS => 'sometimes|numeric|max:4',
            //            Team::CREATED_BY => 'required|numeric|exists:users',
        ];
    }
}
