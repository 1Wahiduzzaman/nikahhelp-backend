<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('emails.verifyUser');
});

Route::get('/migration', function () {
    if(env('APP_ENV') === 'local'){
        Artisan::call('migrate:fresh', ['--seed'=>true]);
        echo "Migration and seed done successfully";
    }else {
        echo "Production Mode, Unable to Fresh database";
    }

});
