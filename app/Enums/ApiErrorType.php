<?php

namespace App\Enums;

enum ApiErrorType
{
    public const VALIDATION_FAILED_ERROR = 'validation_error';

    public const TOKEN_NOT_FOUND_ERROR = 'unauthenticated';

    public const TOKEN_INVALID_ERROR = 'unauthenticated';

    public const NOT_FOUND_ERROR = 'not_found';

    public const METHOD_NOT_ALLOWED_ERROR = 'method_not_allowed';

    public const INTERNAL_SERVICE_ERROR = 'internal_error';

    public const CURL_EXCEPTION_ERROR = 'curl_request_error';
}
