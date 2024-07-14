<?php

namespace App\Http\Controllers;

use App\Enums\ApiCustomStatusCode;
use App\Enums\HttpStatusCode;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Success response method.
     *
     * @param  array  $result
     * @param  array  $pagination
     * @param  int  $http_status
     * @param  int  $status_code
     */
    public function sendSuccessResponse(
        $result,
        $message,
        $pagination = [],
        $http_status = HttpStatusCode::SUCCESS->value,
        $status_code = ApiCustomStatusCode::SUCCESS->value
    ): JsonResponse {
        $response = [
            'status' => 'SUCCESS',
            'status_code' => $status_code,
            'message' => $message,
            'data' => $result,
        ];

        if (! empty($pagination)) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $http_status);
    }

    /**
     * Return error response.
     *
     * @param array $errorMessages
     * @param int $status_code
     * @return JsonResponse
     */
    public function sendErrorResponse($message, array $errorMessages = [], int $status_code = HttpStatusCode::VALIDATION_ERROR->value)
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => $status_code,
            'message' => $message,
        ];

        if (! empty($errorMessages)) {
            $response['error'] = $errorMessages;
        }

        return response()->json($response, $status_code);
    }
}
