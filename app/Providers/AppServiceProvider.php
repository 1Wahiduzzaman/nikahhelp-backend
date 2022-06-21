<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isRequestFromAdmin()) {
            \Illuminate\Support\Facades\Config::set('auth.providers.users.model', \App\Models\Admin::class);
        }
    }

    protected function isRequestFromAdmin()
    {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $baseUrl = parse_url($url);
        $status = false;
        if ($baseUrl['path'] == '/api/v1/admin/login') {
            $status = true;
        }
//        JWTAuth::toUser();
        try {
            $authType = JWTAuth::parseToken()->payload();
            if ($authType['authType'] == 'admin') {
                $status = true;
            }
        }catch (\Exception $e){

        }


        return $status;
    }
}
