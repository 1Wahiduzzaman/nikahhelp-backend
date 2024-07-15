<?php

namespace App\Enums;

/**
 * Class HttpStatusCode
 */
enum HttpStatusCode: int
{
    case SUCCESS = 200;

    case CREATED = 201;

    case BAD_REQUEST = 400;

    case VALIDATION_ERROR = 422;

    case UNAUTHORIZED = 401;

    case FORBIDDEN = 403;

    case NOT_FOUND = 404;

    case INTERNAL_ERROR = 500;

}
