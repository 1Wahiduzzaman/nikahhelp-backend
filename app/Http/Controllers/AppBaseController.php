<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Response;
use Symfony\Component\HttpFoundation\Response as FResponse;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public function sendResponse($result, $message, $code = FResponse::HTTP_OK)
    {
        return $this->sendSuccessResponse($result, $message, [], $code);
    }

    public function sendError($message, $code = FResponse::HTTP_BAD_REQUEST)
    {
        return $this->sendErrorResponse($message, $errorMessages = [], $code);
    }

    public function sendSuccess($data, $message, $code)
    {
        return $this->sendSuccessResponse($data, $message, $code);
    }

    public function sendUnauthorizedResponse($message = '', $code = FResponse::HTTP_FORBIDDEN): JsonResponse
    {
        $message = $message ?: "You don't have permission to access";

        return $this->sendErrorResponse($message, $errorMessages = [], $code);
    }

    public function getUserId()
    {
        $user = auth()->authenticate();

        return $user['id'];
    }

    /**
     * Return Response with pagination
     *
     * @return array
     */
    public function paginationResponse($items)
    {
        return [
            'total_items' => $items->total(),
            'current_items' => $items->count(),
            'first_item' => $items->firstItem(),
            'last_item' => $items->lastItem(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'has_more_pages' => $items->hasMorePages(),
        ];
    }

    public function getBackHere()
    {
        return response()->json(['message' => 'It is working']);
    }
}
