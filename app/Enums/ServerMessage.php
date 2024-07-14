<?php

namespace App\Enums;

enum ServerMessage: string
{
    case FORBIDDEN_MESSAGE = 'Insufficient privileges to perform this action';

    case BAD_REQUEST_MESSAGE = 'Bad Request';

    case INTERNAL_ERROR_PDO_MESSAGE = 'Sorry, cannot perform the action, something went wrong with data!';

    case INTERNAL_ERROR_FETAL_MESSAGE = 'Sorry, cannot perform the action, something went wrong!';

    case VALIDATION_ERROR_MESSAGE = 'Sorry! Item not exists!';
}
