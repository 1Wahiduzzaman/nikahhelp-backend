<?php

namespace App\Http\Requests\API;

//use InfyOm\Generator\Request\APIRequest;
use App\Http\Requests\APIRequest;
use App\Models\TeamListedCandidate;

class CreateTeamListedCandidateAPIRequest extends APIRequest
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
        return TeamListedCandidate::$rules;
    }
}
