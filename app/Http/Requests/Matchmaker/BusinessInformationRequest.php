<?php


namespace App\Http\Requests\Matchmaker;

use App\Models\MatchMaker;
use App\Http\Requests\APIRequest;

class BusinessInformationRequest extends APIRequest
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
            'capacity' => 'required|max:100',
            'company_or_other' => 'sometimes|required|max:255',
            'occupation' => 'sometimes|required|max:70',
            'match_maker_duration' => 'required|max:100',
            'match_qt' => 'required|max:10',
            'last_six_month_match_qt' => 'required|max:10',
            'match_per_county' => 'required|max:255',
            'match_community' => 'required|max:255',
            'have_previous_experience' => 'required|max:255',
            'previous_experience' => 'sometimes|required|max:255',
            'can_share_last_three_match' => 'required|max:10',
            'match_one' => 'sometimes|required|max:255',
            'match_two' => 'sometimes|required|max:255',
            'match_three' => 'sometimes|required|max:255'
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.'
        ];
    }

}
