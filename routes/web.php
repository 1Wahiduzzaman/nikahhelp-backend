<?php

use App\Http\Controllers\API\V1\SubscriptionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
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
Route::get('/logo',  function () {
   $image = File::get(resource_path('/images/email/white@2x-100.jpg'));
    return response()->make($image, 200)->header('Content-Type', 'image/jpeg');
})->name('logo');

Route::get('/', function () {
    return view('emails.subscription.new_subscription');
});

 // Raz - Cron Job Expire Subscription Sending Mail
 Route::get('/subscription-expiring/{days}', [SubscriptionController::class, 'subscriptionExpiring'])->name('subscription.expiring');
 Route::get('/subscription-expired/{days}', [SubscriptionController::class, 'subscriptionExpired'])->name('subscription.expired');

Route::get('/migration', function () {
    if(env('APP_ENV') === 'local'){
        Artisan::call('migrate:fresh', ['--seed'=>true]);
        echo "Migration and seed done successfully";
    }else {
        echo "Production Mode, Unable to Fresh database";
    }

});
