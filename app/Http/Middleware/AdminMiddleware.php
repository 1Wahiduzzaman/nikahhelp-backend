<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Models\Permission;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AdminMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  string[]  $guards
     * @param \Closure
     *
     * @throws AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards): JsonResponse
    {
        if (! Auth::guard('admin')->check()) {
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::VALIDATION_ERROR,
                'message' => 'Authorization Fail',
                'error' => ['details' => 'Authorization Fail'],
            ], HttpStatusCode::VALIDATION_ERROR);
        }

        $user = Auth::guard('admin')->authenticate();
        Auth::setUser($user);
        foreach ($this->getPermissions() as $permission) {
            Gate::define(strtoupper($permission->slug), function ($user) use ($permission) {
                return $user->hasRole($permission->roles);

            });
        }

        return $next($request);
    }

    /**
     * Get all permissions with role.
     *
     * @return array
     */
    protected function getPermissions()
    {
        if (Schema::hasTable('roles') && Schema::hasTable('permissions')) {
            return Permission::with('roles')->get();
        } else {
            return [];
        }
    }
}
