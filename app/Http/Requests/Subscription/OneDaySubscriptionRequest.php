<?php

namespace App\Http\Requests\Subscription;

use App\Http\Requests\APIRequest;

class OneDaySubscriptionRequest extends APIRequest
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
            'stripeToken' => 'required',
            'plane' => 'required',
            'team_id' => 'required',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'stripeToken.required' => 'Stripe token field is required',
            'plane.required' => 'Subscription plane field is required',
            'team_id.required' => 'Team ID field is required',
        ];
    }
}
