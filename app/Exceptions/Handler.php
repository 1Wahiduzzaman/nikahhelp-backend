<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Enums\ServerMessage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            dd($e);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return $this->sendErrorResponse($e->getMessage(), HttpStatusCode::UNAUTHORIZED->value);
        }
        if ($e instanceof AuthorizationException) {
            return $this->sendErrorResponse(ServerMessage::FORBIDDEN_MESSAGE->value, HttpStatusCode::FORBIDDEN->value);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->sendErrorResponse(ServerMessage::BAD_REQUEST_MESSAGE->value, HttpStatusCode::NOT_FOUND->value);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->sendErrorResponse(ServerMessage::VALIDATION_ERROR_MESSAGE->value, HttpStatusCode::NOT_FOUND->value);
        }

        if (env('APP_ENV') !== 'local') {
            if ($e instanceof \PDOException) {
                return $this->sendErrorResponse(ServerMessage::INTERNAL_ERROR_PDO_MESSAGE->value, HttpStatusCode::INTERNAL_ERROR->value);
            }

            if ($e instanceof \Error) {
                return $this->sendErrorResponse(ServerMessage::INTERNAL_ERROR_FETAL_MESSAGE->value, HttpStatusCode::INTERNAL_ERROR->value);
            }
        }
    }

    /**
     * Return error response.
     *
     * @param int|HttpStatusCode $status_code
     */
    private function sendErrorResponse($message, int|HttpStatusCode $status_code = HttpStatusCode::VALIDATION_ERROR->value): JsonResponse
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => $status_code,
            'message' => $message,
        ];

        return response()->json($response, $status_code);
    }
}
