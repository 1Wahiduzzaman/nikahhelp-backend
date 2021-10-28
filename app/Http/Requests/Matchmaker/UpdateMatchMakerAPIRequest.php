<?php

namespace App\Http\Requests\Matchmaker;

use App\Models\RepresentativeInformation;
use InfyOm\Generator\Request\APIRequest;

class UpdateMatchMakerAPIRequest extends APIRequest
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
        $rules = RepresentativeInformation::$rules;

        return $rules;
    }
}
