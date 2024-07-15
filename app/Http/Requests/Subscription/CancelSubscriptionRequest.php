<?php

namespace App\Http\Requests\Subscription;

use App\Http\Requests\APIRequest;

class CancelSubscriptionRequest extends APIRequest
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
            'subscription_id' => 'required',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'subscription_id.required' => 'Subscription id field is required',
        ];
    }
}
