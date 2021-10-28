<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
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
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return $this->sendErrorResponse($e->getMessage(), HttpStatusCode::UNAUTHORIZED);
        }
        if ($e instanceof AuthorizationException) {
            return $this->sendErrorResponse(HttpStatusCode::FORBIDDEN_MESSAGE, HttpStatusCode::FORBIDDEN);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->sendErrorResponse(HttpStatusCode::BAD_REQUEST_MESSAGE, HttpStatusCode::NOT_FOUND);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->sendErrorResponse(HttpStatusCode::VALIDATION_ERROR_MESSAGE, HttpStatusCode::NOT_FOUND);
        }

        if (env('APP_ENV') !== 'local') {
            if ($e instanceof \PDOException) {
                return $this->sendErrorResponse(HttpStatusCode::INTERNAL_ERROR_PDO_MESSAGE, HttpStatusCode::INTERNAL_ERROR);
            }

            if ($e instanceof \Error) {
                return $this->sendErrorResponse(HttpStatusCode::INTERNAL_ERROR_FETAL_MESSAGE, HttpStatusCode::INTERNAL_ERROR);
            }
        }
    }

    /**
     * Return error response.
     *
     * @param $message
     * @param int $status_code
     * @return JsonResponse
     */
    private function sendErrorResponse($message, $status_code = HttpStatusCode::VALIDATION_ERROR): JsonResponse
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => $status_code,
            'message' => $message,
        ];
        return response()->json($response, $status_code);
    }
}
