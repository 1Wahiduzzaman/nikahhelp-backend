<?php

use App\Http\Controllers\API\TeamListedCandidateController;
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
use App\Http\Controllers\API\ShortListedRepresentativeController;
use App\Http\Controllers\API\V1\AllNotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\API\V1\SubmitTicketController;
use App\Http\Controllers\API\V1\ChatInfoController;
use App\Http\Controllers\API\V1\MatchMakerAPIController;
use App\Http\Controllers\API\V1\MessageController;
use App\Http\Controllers\API\V1\PackageController;
use App\Http\Controllers\API\V1\VisitController;
use App\Http\Controllers\LocationController;
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
Route::get('v1/home-searches', [SearchAPIController::class, 'filter']);
Route::post('v1/register', [UserController::class, 'register']);//validation incomplete
Route::POST('v1/login', [UserController::class, 'authenticate']);
Route::GET('v1/logout', [UserController::class, 'logout']);//will be used  in save and exit
//using ony get method
Route::get('v1/emailVerify/{token}', [UserController::class, 'emailVerify']);//incomplete
Route::get('v1/password-reset/{token}', [UserController::class, 'passwordExpiryCheck']);
Route::GET('v1/token-refresh', [UserController::class, 'getTokenRefresh']);//inspect
Route::post('v1/forgot/password', ForgotPasswordController::class)->name('forgot.password');//inspect
Route::post('v1/forgot/password/verify', [ForgotPasswordController::class, 'forgetPasswordTokenVerification'])->name('forgot.password.verify');//inspect
Route::post('v1/forgot/password/update', [ForgotPasswordController::class, 'updatePassword'])->name('forgot.password.update');//inspect
// Route::post('v1/recent-join-candidate', [ForgotPasswordController::class, 'recentJoinCandidate'])->name('recent.join.candidate');
Route::get('v1/initial-dropdowns', [HomeController::class, 'initialDropdowns'])->name('initial.dropdowns');
Route::GET('v1/recent-join-candidate', [HomeController::class, 'recentJoinCandidate']);//need to update if traffic changes

Route::get('v1/religions', [ReligionController::class, 'index'])->name('religion.list');//show all
// Countries API
Route::get('v1/utilities/countries', [CountryController::class, 'index'])->name('utilities.countries.lists');//ok
Route::get('v1/utilities/cities', [CountryController::class, 'getCityList'])->name('utilities.city.lists');//ok
Route::get('v1/utilities/religions', [ReligionController::class, 'index'])->name('utilities.religions.lists');//ok
Route::post('v1/feed-back', [\App\Http\Controllers\FeedBackController::class, 'feedBack'])->name('help.feedback');
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['prefix' => 'v1'], function () {
        //  Settings
        Route::POST('switch-account', [UserController::class, 'switchAccount']);
        Route::POST('change-password', [UserController::class, 'changePassword']);
        Route::GET('delete-account', [UserController::class, 'deleteAccount']);
        Route::POST('user-form-type', [UserController::class, 'formTypeStatus']);
        //create endpoint to fill up the profile form

        // Candidate information API

        Route::POST('candidate/create', [CandidateController::class, 'create'])->name('candidate.information.create');
        Route::get('candidate/info/{user_id}', [CandidateController::class, 'index'])->name('candidate.information.all');
        Route::get('candidate/initial-info', [CandidateController::class, 'candidateProfileInitialInfo'])->name('candidate.initial.information');
        Route::get('candidate/personal-info', [CandidateController::class, 'candidatePersonalInfo'])->name('candidate.personal.information');
        Route::POST('candidate/basic-info/{user_id}', [CandidateController::class, 'storeCandidateBasicInformation'])->name('store.candidate.basic.information');
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
        Route::DELETE('candidate/image-upload/{candidate_image}', [CandidateController::class, 'deleteImageByType'])->name('delete.candidate.image.upload');
        Route::DELETE('candidate-image/{image_type}', [CandidateController::class, 'deleteImageByType'])->name('delete.candidate.image.upload');

        // Chat | Message API | By Raz
        Route::GET('team-chat', [MessageController::class, 'teamChatList'])->name('team-chat.list');
        Route::GET('chat-history', [MessageController::class, 'chatHistory'])->name('team-chat.chat-history');
        //will be use for both single and group msg history
        Route::POST('individual-chat-history', [MessageController::class, 'individualChatHistory'])->name('team-chat.individual-chat-history');
        Route::POST('send-message', [MessageController::class, 'sendMessage'])->name('team-chat.send-message');
        Route::POST('send-message-to-team', [MessageController::class, 'sendMessageToTeam'])->name('team-chat.send-message-to-team');
        //Connected (Team to Team chat)
        Route::POST('connection-list-chat', [MessageController::class, 'connectedTeamData'])->name('connected-team.chat.connection-list-chat');
        Route::POST('send-message-team-to-team', [MessageController::class, 'sendMessageTeamToTeam'])->name('connected-team-chat.send-message-team-to-team');
        Route::POST('private-chat-request-accept-reject', [MessageController::class, 'privateChatRequestAcceptOrReject'])->name('connected-team-chat.private-chat-request-accept-reject');
        Route::GET('get-all-private-chat-requests', [MessageController::class, 'getAllPrivateChatRequest'])->name('connected-team-chat.get-all-private-chat-requests');
        Route::POST('connected-team-chat-history', [MessageController::class, 'connectedTeamChatHistory'])->name('connected-team-chat.connected-team-chat-history');
        Route::POST('connected-send-private-message', [MessageController::class, 'sendPrivateMessage'])->name('connected-team-chat.connected-send-private-message');
        Route::POST('connected-private-chat-history', [MessageController::class, 'privateChatHistory'])->name('connected-team-chat.connected-private-chat-history');
        Route::POST('connected-team-chat-seen', [MessageController::class, 'teamChatSeen'])->name('connected-team-chat.connected-team-chat-seen');

        //Seen Message (Managing from individual-chat-history so no needed)
        //Route::POST('seen-message', [MessageController::class, 'seenMessage'])->name('team-chat.seen-message');
        //End Chat Module

        //Send Notification | By Raz
        Route::POST('send-notification', [AllNotificationController::class, 'saveNotifications'])->name('all-notification.send-notification');
        Route::GET('list-notification', [AllNotificationController::class, 'listNotifications'])->name('all-notification.list-notification');
        Route::GET('seen-notification', [AllNotificationController::class, 'seenNotification'])->name('all-notification.seen-notification');
        // End Notification


        //User / Raz
        Route::get('individual-rejected-notes/{id}', [UserController::class, 'getRejectedNotes'])->name('user.rejected-notes');
        Route::post('candidate-upload-doc', [UserController::class, 'postDocUpload'])->name('user.candidate-upload-doc');
        Route::post('rep-upload-doc', [UserController::class, 'postDocUploadRep'])->name('user.rep-upload-doc');

        //Package List |Raz
        Route::get('package-list', [PackageController::class, 'index'])->name('package-list');


        //Support Chat By Raz


        // Teams API
        Route::DELETE('member-invitation-delete', [MembersInvitationController::class, 'destroy'])->name('team.member-invitation-delete');
        Route::GET('team-invitation-information/{link}', [TeamMembersController::class, 'teamInvitationInformation'])->name('team.invitation_information');
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
        Route::POST('invite-team-member-update', [MembersInvitationController::class, 'update'])->name('team.members.invitations.edit');
        Route::POST('join-team-by-invitation', [MembersInvitationController::class, 'joinTeamByInvitation'])->name('team.members.invitations.join');
        Route::POST('change-team-member-access', [TeamMembersController::class, 'changeTeamMemberAccess'])->name('team.members.change.access');
        Route::POST('delete-reason-submit', [DeleteReasonController::class, 'store'])->name('team.delete.reason.submit');
        Route::POST('leave-team', [TeamMembersController::class, 'teamLeave'])->name('team.leave');
        Route::POST('user-info', [UserController::class, 'getUserInfo'])->name('team.user_info');
        Route::get('candidate-of-team', [TeamController::class, 'candidateOfTeam'])->name('candidate.of.team');

        // Team Connection API
        Route::POST('send-connection-request', [TeamConnectionController::class, 'store'])->name('send.team.connection.request');
        Route::POST('respond-connection-request', [TeamConnectionController::class, 'respond'])->name('respond.team.connection.request');
        Route::POST('connection-report', [TeamConnectionController::class, 'report'])->name('team.connection.report');

        // Team Connection API | by Rabbi
        Route::DELETE('team-connection-detach', [TeamConnectionController::class, 'teamDisconnect'])->name('team.connection.report');

        Route::POST('connection-reports', [TeamConnectionController::class, 'reports'])->name('team.connection.reports');

        Route::POST('connection-overview', [TeamConnectionController::class, 'overview'])->name('team.connection.overview');
        Route::POST('disconnect-team', [TeamConnectionController::class, 'disconnect'])->name('team.connection.disconnect');

        // Chat
        Route::POST('chat-info', [ChatInfoController::class, 'getInfo'])->name('chat.info');
        Route::POST('chat-user-info', [ChatInfoController::class, 'getUserInfoList'])->name('chat.user.info');
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

        // Short listed | by Rabbi
        Route::get('short-listed-candidates', [ShortListedCandidateController::class, 'index'])->name('get_short_listed_candidates');
        Route::POST('short-listed-candidates/store', [ShortListedCandidateController::class, 'store'])->name('short.list.store');
        Route::delete('delete-short-listed-by-candidates', [ShortListedCandidateController::class, 'destroyByCandidate'])->name('delete.shortlisted.item.by.candidate');
        Route::get('short-listed-representative', [ShortListedRepresentativeController::class, 'index'])->name('get_short_listed_candidates');
        Route::POST('short-listed-representative/store', [ShortListedRepresentativeController::class, 'store'])->name('short.list.store');
        Route::delete('delete-short-listed-by-representative', [ShortListedRepresentativeController::class, 'destroyByCandidate'])->name('delete.shortlisted.item.by.candidate');

        // Team listed | by Rabbi
        Route::get('team-short-listed-candidates', [ShortListedCandidateController::class, 'teamShortListedCandidate'])->name('get_team_short_listed_candidates');
        Route::POST('team-short-listed-candidates/store', [TeamListedCandidateController::class, 'store'])->name('team.list.store');
        Route::delete('delete-team-short-listed-by-candidates', [TeamListedCandidateController::class, 'destroyByCandidate'])->name('team.list.store');

        // Short listed
        Route::get('show-short-listed-candidates/{id}', [ShortListedCandidateController::class, 'show'])->name('show_short_listed_candidates');
        Route::get('deleted-candidates', [ShortListedCandidateController::class, 'deletedCandidate'])->name('deleted_candidates');
        Route::get('delete-short-listed-candidates/{id}', [ShortListedCandidateController::class, 'destroy'])->name('delete_short_listed_candidates');
        Route::PATCH('update-shortlisted-candidates/{id}', [ShortListedCandidateController::class, 'update'])->name('updateShortlistedcandidates');

        // Block listed | by Rabbi
        Route::get('block-list', [BlockListAPIController::class, 'index'])->name('block.list');
        Route::get('block-by-team-list', [BlockListAPIController::class, 'blockByTeamList'])->name('block.list.by.team');
        Route::POST('store-block-list', [BlockListAPIController::class, 'store'])->name('block.create');
        Route::delete('unblock-by-candidate', [BlockListAPIController::class, 'destroyByCandidate'])->name('unblock.by.candidate');

        // Block listed
        Route::get('unblock-candidate/{id}', [BlockListAPIController::class, 'destroy'])->name('unblock.candidate');

        // Representative
        Route::get('representative-information', [RepresentativeInformationController::class, 'index'])->name('representativeInformation');
        Route::get('representative/info/{user_id}', [RepresentativeInformationController::class, 'profileInfo'])->name('candidate.information.all');
        Route::get('representative/profile', [RepresentativeInformationController::class, 'representativeInfo'])->name('candidate.information.all');
        Route::POST('representative-screen-name', [RepresentativeInformationController::class, 'representativeScreenName'])->name('representativeScreenName');
        Route::POST('representative/essentialInformation', [RepresentativeInformationController::class, 'essentialInformation'])->name('essentialInformation');
        Route::POST('representative/contactinfo', [RepresentativeInformationController::class, 'contactInformation'])->name('rcontactInformation');
        Route::POST('representative/verify/identity', [RepresentativeInformationController::class, 'verifyIdentity'])->name('representative.verify.identity');
        Route::POST('representative/image/upload', [RepresentativeInformationController::class, 'imageUpload'])->name('representative.image.upload');
        Route::POST('representative/final/submit', [RepresentativeInformationController::class, 'finalSubmit'])->name('representative.final.submit');
        Route::delete('representative/remove-img/{imageType}', [RepresentativeInformationController::class, 'deleteImage'])->name('representative.image.delete');
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


        //Raz - Visitor count in site
        Route::post('site-visit', [VisitController::class, 'visit'])->name('user.site-visit');
        Route::get('site-visit-graph/{id}', [VisitController::class, 'visitGraph'])->name('user.site-visit-graph');

        //location requests
       // Route::post('search/location', [LocationController::class, 'postcode']);

        Route::post('/ticket-submission', [SubmitTicketController::class, 'submitTicket']);
        Route::post('/issue-screen-shot', [SubmitTicketController::class, 'screenShot']);

        Route::get('/getAllTickets/{id}', [SubmitTicketController::class, 'allTicket']);

        Route::post('/resolveTicket', [SubmitTicketController::class, 'resolveTicket']);
        Route::post('send-support-message', [SubmitTicketController::class, 'sendTicketMessage']);
    });
/*
    Route::group(['prefix' => 'v1/admin'], function () {

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

        // Send Global Notification
        Route::GET('all-user', [AllNotificationController::class, 'getAllUsers'])->name('all-notification.all-user');
        Route::POST('send-notification', [AllNotificationController::class, 'sendGlobalNotification'])->name('all-notification.send-notification');
    });
    */
});
