<?php

namespace App\Http\Requests;

use App\Enums\HttpStatusCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class APIRequest extends FormRequest
{
    /**
     * If validator fails return the exception in json form
     *
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => HttpStatusCode::VALIDATION_ERROR,
            'message' => 'Input validation error',
            'data' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response));
    }

    abstract public function rules();
}
