<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class HttpStatusCode
 * @package App\Enums
 */
final class HttpStatusCode extends Enum
{

    const SUCCESS          =   200;
    const CREATED          =   201;
    const BAD_REQUEST      =   400;
    const VALIDATION_ERROR =   422;
    const UNAUTHORIZED     =   401;
    const FORBIDDEN        =   403;
    const NOT_FOUND        =   404;
    const INTERNAL_ERROR   =   500;

    const FORBIDDEN_MESSAGE = 'Insufficient privileges to perform this action';
    const BAD_REQUEST_MESSAGE = "Bad Request";
    const INTERNAL_ERROR_PDO_MESSAGE = "Sorry, cannot perform the action, something went wrong with data!";
    const INTERNAL_ERROR_FETAL_MESSAGE = "Sorry, cannot perform the action, something went wrong!";
    const VALIDATION_ERROR_MESSAGE = "Sorry! Item not exists!";

    protected static function setImageUploadLocation()
    {
        // self::$imageUploadLocation = config('app.url');
    }
}
