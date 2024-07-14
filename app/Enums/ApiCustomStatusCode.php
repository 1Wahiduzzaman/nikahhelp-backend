<?php

namespace App\Enums;

enum ApiCustomStatusCode: int
{
    case SUCCESS = 200;

    case INSUFFICIENT_BALANCE = 413;
}
