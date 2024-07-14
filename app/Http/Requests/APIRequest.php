<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\HttpStatusCode;


abstract class APIRequest extends FormRequest
{
    /**
     * If validator fails return the exception in json form
     * @param Validator $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => HttpStatusCode::VALIDATION_ERROR,
            'message' => 'Input validation error',
            'data' =>  $validator->errors(),
        ];


        throw new HttpResponseException(response()->json($response));
    }
    abstract public function rules();
}
