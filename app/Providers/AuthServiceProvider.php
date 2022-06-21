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
//        dd($this->isRequestFromAdmin());

////        dd($this->isRequestFromAdmin());
//        if($this->isRequestFromAdmin()){
////            dd('Hi');
//            \Illuminate\Support\Facades\Config::set('auth.providers.users.model', \App\Models\Admin::class);
//        }
//        dd($this->isRequestFromAdmin());
//        if ($this->isRequestFromAdmin()) {
//            \Illuminate\Support\Facades\Config::set('auth.providers.users.model', \App\Models\Admin::class);
//        }else{
//            \Illuminate\Support\Facades\Config::set('auth.providers.users.model', \App\Models\User::class);
//        }
        $this->registerPolicies();

        foreach ($this->getPermissions() as $permission) {
            Gate::define(strtolower($permission->slug), function ($user) use ($permission) {
                return $user->hasRole($permission->roles);

            });
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


}
