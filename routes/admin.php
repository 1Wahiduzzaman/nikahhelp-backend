<?php

use App\Http\Controllers\API\AdminDashboardController;
use App\Http\Controllers\API\V1\AllNotificationController;
use App\Http\Controllers\API\V1\TeamController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Middleware\CorsHandler;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\MessageController;
use App\Http\Controllers\API\V1\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::POST('v1/admin/login', [AdminController::class, 'login']);

Route::group(['prefix' => 'v1/admin','middleware'=>'admin'], function () {

    Route::POST('support-send-message', [MessageController::class, 'sendMessageToSupport'])->name('support.support-send-message');
    Route::POST('individual-support-user-chat-history', [MessageController::class, 'individualSupportChatHistory'])->name('support.individual-support-user-chat-history');
    Route::GET('support-chat-list', [MessageController::class, 'supportChatHistory'])->name('support.support-chat-list');
    Route::GET('support-admin', [UserController::class, 'getSuportUserId'])->name('support.support-admin');
    //End Support Chat
    // User report
    Route::get('dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('count-can-rep', [AdminDashboardController::class, 'count_can_rep'])->name('user.count-can-rep');
    Route::get('users-report', [AdminDashboardController::class, 'userReport'])->name('user.report');
    // Awating for approval list
    Route::get('pending-user', [AdminDashboardController::class, 'pendingUserList'])->name('user.pending');

    Route::get('approved-user-list', [AdminDashboardController::class, 'approvedUserList'])->name('user.approved');
    Route::get('verified-user-list', [AdminDashboardController::class, 'verifiedUserList'])->name('user.verified');
    Route::get('rejected-user-list', [AdminDashboardController::class, 'rejectedUserList'])->name('user.rejected');
    Route::get('user-info/{id}', [AdminDashboardController::class, 'UserInfo'])->name('user.user-info');

    // new API Raz
    Route::get('candidate-user-info/{id}', [AdminDashboardController::class, 'CandidateUserInfo'])->name('user.candidate-user-info');
    Route::get('representative-info/{id}', [AdminDashboardController::class, 'RepresentativeUserInfo'])->name('user.user-info');

    Route::post('user-verify-reject', [AdminDashboardController::class, 'verifyRejectUser'])->name('user.verify_reject');
    Route::get('subscription-report', [AdminDashboardController::class, 'subscription'])->name('team.subscription.report');

    //Team
    Route::GET('team-list', [TeamController::class, 'adminTeamList'])->name('team.list');
    Route::GET('deleetd-team-list', [TeamController::class, 'adminDeletedTeamList'])->name('team.deleted-team-list');
    Route::GET('connected-team-list/{id}', [TeamController::class, 'adminConnectedTeamList'])->name('team.connected-list'); // param team pk
    Route::DELETE('team-del', [TeamController::class, 'adminTeamDelete'])->name('team.team-del'); // param team pk

    Route::post('submitTicketRequests', [\App\Http\Controllers\API\SupportTicketController::class, 'saveRequest'])->name('save-request-ticket');
    Route::get('get-users-with-tickets', [\App\Http\Controllers\API\SupportTicketController::class, 'getALlTicket'])->name('all-ticket');
    Route::get('getTickets/{id}', [App\Http\Controllers\API\SupportTicketController::class, 'getUserTickets'])->name('userTickets');
    Route::get('getAllTicketMessages/{id}', [\App\Http\Controllers\API\SupportTicketController::class, 'getTicketMessages'])->name('ticketMessages');
    Route::post('ticketResolve', [\App\Http\Controllers\API\SupportTicketController::class, 'ticketResolve']);
    // Send Global Notification

    Route::GET('all-user', [AllNotificationController::class, 'getAllUsers'])->name('all-notification.all-user');
    Route::POST('send-notification', [AllNotificationController::class, 'sendGlobalNotification'])->name('all-notification.send-notification');
});


//Route::resource('match_makers', App\Http\Controllers\API\MatchMakerAPIController::class);
