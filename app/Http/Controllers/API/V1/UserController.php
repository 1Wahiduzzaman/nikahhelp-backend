<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormTypeRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use JWTAuth;
use App\Services\UserService;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Models\CandidateInformation;
use App\Models\RepresentativeInformation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mail;

class UserController extends Controller
{
    protected \App\Services\UserService $userService;

    /**
     * PurchaseController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(LoginRequest $request)
    {
        return $this->userService->authenticate($request);
    }

    /**
     * @param \App\Http\Requests\UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegistrationRequest $request)
    {
        return $this->userService->register($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser()
    {
        return $this->userService->getAuthenticatedUser();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile(Request $request)
    {
        return $this->userService->getUserProfile($request);
    }

    public function getUserInfo(Request $request)
    {
        return $this->userService->findUserInfo($request);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTokenRefresh()
    {
        return $this->userService->getTokenRefresh();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailVerify(Request $request, $token)
    {
        return $this->userService->emailVerify($request, $token);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tokenVerifyOrResend(LoginRequest $request)
    {
        return $this->userService->tokenVerifyOrResend($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchAccount(Request $request)
    {

        return $this->userService->switchAccount($request);

    }

    /**
     * @param ChangePasswordRequest $request
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->userService->changePassword($request->all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        return $this->userService->logout($token);
    }

    public function deleteAccount(Request $request)
    {
        return $this->userService->deleteUserAccount($request);
    }

    public function sendEmail()
    {
        $to_name = 'MatrimonyAssist';
        $to_email = 'ahabib@bs-23.net';

        $details = [
            'title' => 'Mail from matrimonyAssist.com',
            'body' => 'This is for testing email using smtp'
        ];

        Mail::to($to_email)->send(new \App\Mail\MyTestMail($details));

        dd("Email is Sent.");
    }

    //Raz
    public function postDocUpload(Request $request) {
        $is_exist = CandidateInformation::where('user_id', JWTAuth::parseToken()->authenticate()['id'])->first();
        if($is_exist) {
            $res = CandidateInformation::where('user_id', JWTAuth::parseToken()->authenticate()['id'])->update(['is_uplaoded_doc'=>$request->is_uplaoded_doc]);
            return $this->sendSuccessResponse([], 'Successfully Updated');
        } else {
            return $this->sendErrorResponse('Candidate not found');
        }
    }

    public function postDocUploadRep(Request $request) {
        $res = RepresentativeInformation::where('user_id', JWTAuth::parseToken()->authenticate()['id'])->update(['is_uplaoded_doc'=>$request->is_uplaoded_doc]);
        if($res) {
            return $this->sendSuccessResponse([], 'Successfully Updated');
        }  else {
            return $this->sendErrorResponse('Rep not found');
        }
    }

    public function getSuportUserId() {
        $data = Role::with('admins')->get();
        return $this->sendSuccessResponse($data, 'Support Admin Loaded Successfully');
    }

    public function getRejectedNotes($id) {
        if (!empty($id)) {
            $userId = $id;
        } else {
            return $this->sendErrorResponse('User Id is required');
        }
        $userInfo = User::with(['rejected_notes'])->where('id', $userId)->first();
        if ($userInfo) {
            return $this->sendSuccessResponse($userInfo, 'User info loaded successfully');
        } else {
            return $this->sendErrorResponse('Something went wrong please try again later');
        }
    }

    public function formTypeStatus(FormTypeRequest $request)
    {
        return $this->userService->formTypeStatus($request);
    }

    public function passwordExpiryCheck(Request $request, $token)
    {
        return $this->userService->passwordExpiryCheck($token);
    }

}
