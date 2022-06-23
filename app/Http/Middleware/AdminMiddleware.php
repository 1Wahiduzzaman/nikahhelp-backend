<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Models\Permission;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AdminMiddleware extends BaseMiddleware
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
        if(!Auth::guard('admin')->check()){
            return response()->json([
                'status' => 'FAIL',
                'status_code' => HttpStatusCode::VALIDATION_ERROR,
                'message' => 'Authorization Fail',
                'error' => ['details' => 'Authorization Fail']
            ], HttpStatusCode::VALIDATION_ERROR);
        }

        $user = Auth::guard('admin')->authenticate();
        foreach ($this->getPermissions() as $permission) {
            Gate::define(strtolower($permission->slug), function ($user) use ($permission) {
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
