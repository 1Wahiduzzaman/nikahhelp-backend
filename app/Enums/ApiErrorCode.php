<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ApiErrorCode extends Enum
{
    public const VALIDATION_FAILED_ERROR   = 'ERROR_1001';
    public const TOKEN_NOT_FOUND_ERROR     = 'ERROR_4001';
    public const TOKEN_INVALID_ERROR       = 'ERROR_4002';
    public const NOT_FOUND_ERROR           = 'ERROR_4004';
    public const METHOD_NOT_ALLOWED_ERROR  = 'ERROR_4006';
    public const INTERNAL_SERVICE_ERROR    = 'ERROR_5000';
    public const CURL_EXCEPTION_ERROR      = 'ERROR_9000';

    // Validation Constant
    const VALIDATOR_REQUIRED     = 'required';
    const VALIDATOR_REQUIRED_MSG = 'The :attribute is a mandatory field.';

    // Response Constant
    const RESPONSE_STATUS  = 'status';
    const RESPONSE_CODE    = 'code';
    const RESPONSE_MESSAGE = 'message';
    const RESPONSE_DATA    = 'data';
    const RESPONSE_500     = 'Internal Server Error !!!';
    const RESPONSE_503     = 'Service Unavailable !!!';
    const RESPONSE_400     = 'Request param validation error.';
    const RESPONSE_401     = 'Unauthorized !!!';
    const RESPONSE_402     = 'Malformed request !!!';
    const RESPONSE_403     = 'Forbidden request !!!';
    const RESPONSE_404     = 'Not found !!!';
    const GUZZLE_ERROR     = 'Guzzle http error !!!';
}
