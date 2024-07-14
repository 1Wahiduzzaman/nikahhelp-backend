<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use Closure;
use Exception;
use Illuminate\Auth\Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => 'FAIL',
                    'status_code' => HttpStatusCode::VALIDATION_ERROR,
                    'message' => 'Token is Invalid',
                    'error' => ['details' => 'Token is Invalid'],
                ], HttpStatusCode::VALIDATION_ERROR);

            } elseif ($e instanceof TokenExpiredException) {
                return response()->json([
                    'status' => 'FAIL',
                    'status_code' => HttpStatusCode::VALIDATION_ERROR,
                    'message' => 'Token is Expired',
                    'error' => ['details' => 'Token is Expired'],
                ], HttpStatusCode::VALIDATION_ERROR);
            } else {
                return response()->json([
                    'status' => 'FAIL',
                    'status_code' => HttpStatusCode::VALIDATION_ERROR,
                    'message' => 'Authorization Token not found',
                    'error' => ['details' => 'Authorization Token not found'],
                ], HttpStatusCode::VALIDATION_ERROR);
            }
        }

        return $next($request);
    }
}
