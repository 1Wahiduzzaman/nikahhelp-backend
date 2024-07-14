<?php

namespace App\Contracts;

interface ApiBaseServiceInterface
{
    /**
     * Send Success Response
     *
     * @return mixed
     */
    public function sendSuccessResponse($result, $message, $pagination, $http_status, $status_code);

    /**
     * Send Error Response
     *
     * @return mixed
     */
    public function sendErrorResponse($message, $errorMessages, $status_code);
}
