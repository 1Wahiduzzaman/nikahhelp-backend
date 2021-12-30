<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class UserRegistrationRequest extends APIRequest
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
        //use Illuminate\Validation\Rules\Password;

/*$rules = [
    'password' => [
        'required',
        'string',
        Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised(),
        'confirmed'
    ],
]**/
        return [
            // "full_name" => 'required|max:800|min:3',
            "email" => 'required|email|unique:users',
            "password" => 'required|string|min:8',
            "first_name" => 'required|string|max:255',
            "last_name" => 'sometimes|required|string|max:255',
            "screen_name" => 'required|max:255|unique:candidate_information',
            "account_type" => 'required',
        ];
    }
}
