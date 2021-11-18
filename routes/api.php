<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\CountryController;
use App\Http\Controllers\API\V1\TeamController;
use App\Http\Controllers\API\V1\TeamMembersController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\CandidateController;
use App\Http\Controllers\API\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\API\V1\OccupationController;
use App\Http\Controllers\API\V1\ReligionController;
use App\Http\Controllers\API\V1\StudyLevelController;
use App\Http\Controllers\API\ShortListedCandidateController;
use App\Http\Controllers\API\V1\RepresentativeInformationController;
use App\Http\Controllers\API\V1\MembersInvitationController;
use App\Http\Controllers\API\V1\BlockListAPIController;
use App\Http\Controllers\API\V1\SubscriptionController;
use App\Http\Controllers\API\V1\DeleteReasonController;
use App\Http\Controllers\API\V1\TeamConnectionController;
use App\Http\Controllers\API\SearchAPIController;
use App\Http\Controllers\API\UserDashboardController;
use App\Http\Controllers\API\AdminDashboardController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\API\V1\ChatInfoController;
use App\Http\Controllers\API\V1\MatchMakerAPIController;
use App\Http\Controllers\API\V1\MessageController;
use App\Http\Middleware\CorsHandler;

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
// header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,Content-Length');
// header('Access-Control-Allow-Origin: https://app.arranzed.com');
// Route::GET('/v1/recent-join-candidate', [HomeController::class, 'recentJoinCandidate']);
Route::get('v1/home-searches', [SearchAPIController::class, 'filter']);
Route::post('v1/register', [UserController::class, 'register']);//validation incomplete
Route::POST('v1/login', [UserController::class, 'authenticate']);
Route::GET('v1/logout', [UserController::class, 'logout']);//will be used  in save and exit
//using ony get method
Route::get('v1/emailVerify/{token}', [UserController::class, 'emailVerify']);//incomplete
Route::GET('v1/token-refresh', [UserController::class, 'getTokenRefresh']);//inspect
Route::post('v1/forgot/password', ForgotPasswordController::class)->name('forgot.password');//inspect
Route::post('v1/forgot/password/verify', [ForgotPasswordController::class, 'forgetPasswordTokenVerification'])->name('forgot.password.verify');//inspect
Route::post('v1/forgot/password/update', [ForgotPasswordController::class, 'updatePassword'])->name('forgot.password.update');//inspect
// Route::post('v1/recent-join-candidate', [ForgotPasswordController::class, 'recentJoinCandidate'])->name('recent.join.candidate');
Route::GET('v1/recent-join-candidate', [HomeController::class, 'recentJoinCandidate']);//need to update if traffic changes

Route::get('v1/religions', [ReligionController::class, 'index'])->name('religion.list');//show all
// Countries API

Route::get('v1/utilities/countries', [CountryController::class, 'index'])->name('utilities.countries.lists');//ok

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['prefix' => 'v1'], function () {

        //  Settings
        Route::POST('switch-account', [UserController::class, 'switchAccount']);
        Route::POST('change-password', [UserController::class, 'changePassword']);
        Route::GET('delete-account', [UserController::class, 'deleteAccount']);
        //create endpoint to fill up the profile form

        // Candidate information API

        Route::POST('candidate/create', [CandidateController::class, 'create'])->name('candidate.information.create');
        Route::get('candidate/info/{user_id}', [CandidateController::class, 'index'])->name('candidate.information.all');
        Route::get('candidate/initial-info', [CandidateController::class, 'candidateProfileInitialInfo'])->name('candidate.initial.information');
        Route::get('candidate/personal-info', [CandidateController::class, 'candidatePersonalInfo'])->name('candidate.personal.information');
        Route::Post('candidate/basic-info/{user_id}', [CandidateController::class, 'storeCandidateBasicInformation'])->name('store.candidate.basic.information');
        Route::PATCH('candidate/personal-info/{user_id}', [CandidateController::class, 'updatePersonalInformation'])->name('update.candidate.personal.information');
        Route::POST('candidate/personal-essentialInformation', [CandidateController::class, 'updatePersonalEssentialInInformation'])->name('update.candidate.personal.essential.information');
        Route::POST('candidate/personal-generalinformation', [CandidateController::class, 'updatePersonalGeneralInInformation'])->name('update.candidate.personal.general.information');
        Route::POST('candidate/personal-cotactinformation', [CandidateController::class, 'updatePersonalContactInformation'])->name('update.candidate.personal.contact.information');
        Route::POST('candidate/personal-more-about', [CandidateController::class, 'updatePersonalInformationMoreAbout'])->name('update.candidate.personal.information.moreabout');
        Route::patch('candidate/personal-info-status', [CandidateController::class, 'updateCandidateInfoStatus'])->name('update.candidate.info.status');
        Route::get('candidate/personal-verification-info', [CandidateController::class, 'getCandidatePersonalVerification'])->name('get.candidate.personal.verification');
        Route::post('candidate/personal-verification-info', [CandidateController::class, 'updateCandidatePersonalVerification'])->name('update.candidate.personal.verification');

        // Candidate Preference Information
        Route::get('candidate/preference/{user_id}', [CandidateController::class, 'fetchCandidatePreference'])->name('candidate.preference.information');
        Route::POST('candidate/preference-info', [CandidateController::class, 'storeCandidatePreference'])->name('update.candidate.preference.information');
        Route::POST('candidate/preference-about', [CandidateController::class, 'storeCandidatePreferenceAbout'])->name('candidate.preference.about');
        Route::POST('candidate/preference-rating', [CandidateController::class, 'storeCandidatePreferenceRating'])->name('candidate.preference.rating');


        Route::get('user', [UserController::class, 'getAuthenticatedUser']);
        Route::get('user-profile', [UserController::class, 'getUserProfile']);
        Route::get('candidate/family-info', [CandidateController::class, 'listCandidateFamilyInformation'])->name('list.candidate.family.information');
        Route::PATCH('candidate/family-info', [CandidateController::class, 'updateCandidateFamilyInformation'])->name('update.candidate.family.information');

        // Candidate Image upload.
        Route::get('candidate/image-gallery', [CandidateController::class, 'viewGallery'])->name('view.candidate.image.gallery');
        Route::get('candidate/image-upload', [CandidateController::class, 'viewImage'])->name('view.candidate.image.uploads');
        Route::POST('candidate/image-upload', [CandidateController::class, 'storeImage'])->name('store.candidate.image.upload');
        // PATCH and PUT request do not support File upload
        Route::POST('candidate/image-update', [CandidateController::class, 'updateImage'])->name('update.candidate.image.upload');
        Route::DELETE('candidate/image-upload/{candidate_image}', [CandidateController::class, 'deleteImage'])->name('delete.candidate.image.upload');

        // Chat | Message API | By Raz
        Route::GET('team-chat', [MessageController::class, 'teamChatList'])->name('team-chat.list');
        Route::GET('chat-history', [MessageController::class, 'chatHistory'])->name('team-chat.chat-history');
        //will be use for both single and group msg history
        Route::POST('individual-chat-history', [MessageController::class, 'individualChatHistory'])->name('team-chat.individual-chat-history');
        Route::POST('send-message', [MessageController::class, 'sendMessage'])->name('team-chat.send-message');
        Route::POST('send-message-to-team', [MessageController::class, 'sendMessageToTeam'])->name('team-chat.send-message-to-team');
        //Connected (Team to Team chat)
        Route::POST('connection-list-chat', [MessageController::class, 'report'])->name('connected-team.chat.connection-list-chat');
        Route::POST('send-message-team-to-team', [MessageController::class, 'sendMessageTeamToTeam'])->name('connected-team-chat.send-message-team-to-team');
        Route::POST('private-chat-request-accept-reject', [MessageController::class, 'privateChatRequestAcceptOrReject'])->name('connected-team-chat.private-chat-request-accept-reject');
        Route::POST('connected-team-chat-history', [MessageController::class, 'connectedTeamChatHistory'])->name('connected-team-chat.connected-team-chat-history');
        Route::POST('connected-send-private-message', [MessageController::class, 'sendPrivateMessage'])->name('connected-team-chat.connected-send-private-message');
        Route::POST('connected-private-chat-history', [MessageController::class, 'privateChatHistory'])->name('connected-team-chat.connected-private-chat-history');
        Route::POST('connected-team-chat-seen', [MessageController::class, 'teamChatSeen'])->name('connected-team-chat.connected-team-chat-seen');
        // Teams API

        Route::GET('team-information/{id}', [TeamController::class, 'teamInformation'])->name('team.information');
        Route::GET('team-list', [TeamController::class, 'teamList'])->name('team.list');
        Route::GET('check-team-active-status/{id}', [TeamController::class, 'teamActiveStatusCheck'])->name('team.active.status.check');
        Route::POST('team-edit-check', [TeamController::class, 'teamEditCheck'])->name('team.team_edit_check');
        Route::POST('team-turn-on', [TeamController::class, 'teamTurnOn'])->name('team.turn.on');
        Route::POST('team', [TeamController::class, 'store'])->name('team.add');
        Route::POST('team-update/{id}', [TeamController::class, 'update'])->name('team.update');
        Route::POST('team-login', [TeamController::class, 'login'])->name('team.login');
        Route::POST('team-members', [TeamMembersController::class, 'store'])->name('team.members.add');
        Route::DELETE('team-members-delete', [TeamMembersController::class, 'destroy'])->name('team.members.delete');
        Route::DELETE('delete-team', [TeamController::class, 'destroy'])->name('team.delete');
        Route::POST('invite-team-members', [MembersInvitationController::class, 'store'])->name('team.members.invitations.add');
        Route::POST('join-team-by-invitation', [MembersInvitationController::class, 'joinTeamByInvitation'])->name('team.members.invitations.join');
        Route::POST('change-team-member-access', [TeamMembersController::class, 'changeTeamMemberAccess'])->name('team.members.change.access');
        Route::POST('delete-reason-submit', [DeleteReasonController::class, 'store'])->name('team.delete.reason.submit');
        Route::POST('leave-team', [TeamMembersController::class, 'teamLeave'])->name('team.leave');

        // Team Connection API
        Route::POST('send-connection-request', [TeamConnectionController::class, 'store'])->name('send.team.connection.request');
        Route::POST('respond-connection-request', [TeamConnectionController::class, 'respond'])->name('respond.team.connection.request');
        Route::POST('connection-report', [TeamConnectionController::class, 'report'])->name('team.connection.report');

        Route::POST('connection-reports', [TeamConnectionController::class, 'reports'])->name('team.connection.reports');

        Route::POST('connection-overview', [TeamConnectionController::class, 'overview'])->name('team.connection.overview');
        Route::POST('disconnect-team', [TeamConnectionController::class, 'disconnect'])->name('team.connection.disconnect');

        // Chat
        Route::POST('chat-info', [ChatInfoController::class, 'getInfo'])->name('chat.info');
        Route::POST('chat-user-info', [ChatInfoController::class, 'getUserInfoList'])->name('chat.user.info');


        // Occupations API

        Route::get('occupations', [OccupationController::class, 'index'])->name('getoccupations');
        Route::POST('occupations/store', [OccupationController::class, 'store'])->name('store.occupations');
        Route::POST('occupations/update/{id}', [OccupationController::class, 'update'])->name('update.occupations');

        // Study levels API

        Route::get('studylevels', [StudyLevelController::class, 'index'])->name('getstudylevels');
        Route::POST('studylevels/store', [StudyLevelController::class, 'store'])->name('store.studylevels');
        Route::POST('studylevels/update/{id}', [StudyLevelController::class, 'update'])->name('update.studylevels');

        // Religions API

        Route::get('religions', [ReligionController::class, 'index'])->name('getreligions');
        Route::POST('religions/store', [ReligionController::class, 'store'])->name('store.religions');
        Route::POST('religions/update/{id}', [ReligionController::class, 'update'])->name('update.religions');

        // Countries API
        Route::get('utilities/countries', [CountryController::class, 'index'])->name('utilities.countries.lists');
        Route::get('utilities/cities/{country}', [CountryController::class, 'getCities'])->name('utilities.cities.of.country');
        Route::POST('utilities/create-city', [CountryController::class, 'createCity'])->name('utilities.countries.City');

        // Short listed

        Route::get('team-short-listed-candidates', [ShortListedCandidateController::class, 'teamShortListedCandidate'])->name('get_team_short_listed_candidates');
        Route::get('short-listed-candidates', [ShortListedCandidateController::class, 'index'])->name('get_short_listed_candidates');
        Route::get('show-short-listed-candidates/{id}', [ShortListedCandidateController::class, 'show'])->name('show_short_listed_candidates');
        Route::POST('short-listed-candidates/store', [ShortListedCandidateController::class, 'store'])->name('store.religions');
        Route::get('deleted-candidates', [ShortListedCandidateController::class, 'deletedCandidate'])->name('deleted_candidates');
        Route::get('delete-short-listed-candidates/{id}', [ShortListedCandidateController::class, 'destroy'])->name('delete_short_listed_candidates');
        Route::PATCH('update-shortlisted-candidates/{id}', [ShortListedCandidateController::class, 'update'])->name('updateShortlistedcandidates');

        // Block listed
        Route::get('block-list', [BlockListAPIController::class, 'index'])->name('block.list');
        Route::POST('store-block-list', [BlockListAPIController::class, 'store'])->name('block.create');
        Route::get('unblock-candidate/{id}', [BlockListAPIController::class, 'destroy'])->name('unblock.candidate');

        // Representative
        Route::get('representative-information', [RepresentativeInformationController::class, 'index'])->name('representativeInformation');
        Route::POST('representative-screen-name', [RepresentativeInformationController::class, 'representativeScreenName'])->name('representativeScreenName');
        Route::POST('representative/essentialInformation', [RepresentativeInformationController::class, 'essentialInformation'])->name('essentialInformation');
        Route::POST('representative/contactinfo', [RepresentativeInformationController::class, 'contactInformation'])->name('rcontactInformation');
        Route::POST('representative/verify/identity', [RepresentativeInformationController::class, 'verifyIdentity'])->name('representative.verify.identity');
        Route::POST('representative/image/upload', [RepresentativeInformationController::class, 'imageUpload'])->name('representative.image.upload');
        Route::POST('representative/final/submit', [RepresentativeInformationController::class, 'finalSubmit'])->name('representative.final.submit');

        Route::patch('representative/personal-info-status', [RepresentativeInformationController::class, 'updateRepresentativeInfoStatus'])->name('update.candidate.info.status');



        // Matchmaker
        Route::get('matchmaker-information', [MatchMakerAPIController::class, 'index'])->name('matchmakerInformation');
        Route::POST('matchmaker-screen-name', [MatchMakerAPIController::class, 'matchMakerScreenName'])->name('match.maker.screen.name');
        Route::POST('matchmaker/essentialInformation', [MatchMakerAPIController::class, 'essentialInformation'])->name('matchmaker.essentialInformation');
        Route::POST('matchmaker/contact-info', [MatchMakerAPIController::class, 'contactInformation'])->name('mcontactInformation');
        Route::POST('matchmaker/business-information', [MatchMakerAPIController::class, 'businessInformation'])->name('matchmaker.business.information');
        Route::POST('matchmaker/verify/identity', [MatchMakerAPIController::class, 'verifyIdentity'])->name('matchmaker.verify.identity');
        Route::POST('matchmaker/image/upload', [MatchMakerAPIController::class, 'imageUpload'])->name('matchmaker.image.upload');
        Route::POST('matchmaker/final/submit', [MatchMakerAPIController::class, 'finalSubmit'])->name('matchmaker.final.submit');



        // Stripe subscription

        Route::POST('/subscription/oneday_subscription', [SubscriptionController::class, 'oneDaySubscription'])->name('subscription.oneday');
        Route::POST('/subscription/new_subscription', [SubscriptionController::class, 'createNewSubscription'])->name('subscription.create');
        Route::POST('/subscription/cancel_subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
        Route::get('/subscription/payment_initialization', [SubscriptionController::class, 'initializationPayment'])->name('subscription.initialization.payment');


        // Notification

        Route::get('/notifications', [App\Http\Controllers\API\NotificationAPIController::class, 'index'])->name('notifications.list');

        // Search Candidate Information
        Route::get('/searches', [SearchAPIController::class, 'filter'])->name('searches.list');

        // User Dashboard
        Route::get('user-dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
        Route::POST('profile-view-log', [UserDashboardController::class, 'profileLog'])->name('profile.log.store');
        Route::get('profile-log', [UserDashboardController::class, 'getprofileLog'])->name('get.profile.log');

        // ProfileSuggestions
        Route::get('profile-suggestions', [CandidateController::class, 'profileSuggestions'])->name('profile.suggestions');


        // Email
        Route::get('email', [UserController::class, 'sendEmail'])->name('email');

    });



    Route::group(['prefix' => 'v1/admin'], function () {

        // User report
        Route::get('dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('users-report', [AdminDashboardController::class, 'userReport'])->name('user.report');
        Route::get('pending-user', [AdminDashboardController::class, 'pendingUserList'])->name('user.pending.user');
        Route::get('user-approved/{id}', [AdminDashboardController::class, 'approveUaser'])->name('user.approved');
        Route::get('subscription-report', [AdminDashboardController::class, 'subscription'])->name('team.subscription.report');

    });

});



//Route::resource('match_makers', App\Http\Controllers\API\MatchMakerAPIController::class);
