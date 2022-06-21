<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use JWTAuth;
use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if ($status = $this->isRequestFromAdmin()) {
            \Illuminate\Support\Facades\Config::set('auth.providers.users.model', \App\Models\Admin::class);

            foreach ($this->getPermissions() as $permission) {
                Gate::define(strtolower($permission->slug), function ($user) use ($permission) {
                    return $user->hasRole($permission->roles);

                });
            }
        }
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

    /**
     * @return bool
     */
    protected function isRequestFromAdmin()
    {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        $baseUrl = parse_url($url);

        $status = false;

        if ($baseUrl['path'] == '/api/v1/admin/login') {
            $status = true;
        }

        if(isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])){
            $token = substr($_SERVER['HTTP_AUTHORIZATION'], 6) ;
            $tokenParts = explode(".", $token);
            $tokenPayload = json_decode(base64_decode($tokenParts[1]));
            if(isset($tokenPayload->authType) && $tokenPayload->authType == 'admin'){
                $status = true;
            }
        }


        return $status;
    }


}
