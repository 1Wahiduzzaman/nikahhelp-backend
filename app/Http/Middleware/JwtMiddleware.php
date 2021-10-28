<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'status' => 'FAIL',
                    'status_code' => HttpStatusCode::VALIDATION_ERROR,
                    'message' => 'Token is Invalid',
                    'error' => ['details' => 'Token is Invalid']
                ], HttpStatusCode::VALIDATION_ERROR);

                return response()->json(['status' => 'Token is Invalid']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    'status' => 'FAIL',
                    'status_code' => HttpStatusCode::VALIDATION_ERROR,
                    'message' => 'Token is Expired',
                    'error' => ['details' => 'Token is Expired']
                ], HttpStatusCode::VALIDATION_ERROR);
            } else {
                return response()->json([
                    'status' => 'FAIL',
                    'status_code' => HttpStatusCode::VALIDATION_ERROR,
                    'message' => 'Authorization Token not found',
                    'error' => ['details' => 'Authorization Token not found']
                ], HttpStatusCode::VALIDATION_ERROR);
            }
        }
        return $next($request);
    }
}
