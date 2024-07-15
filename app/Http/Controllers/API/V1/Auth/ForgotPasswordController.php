<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use App\Models\PasswordReset;
use App\Repositories\UserRepository;
use App\Services\ApiBaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    protected \App\Services\ApiBaseService $apiBaseService;

    protected \App\Repositories\UserRepository $userRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(ApiBaseService $apiBaseService, UserRepository $userRepository)
    {
        $this->middleware('guest');
        $this->apiBaseService = $apiBaseService;
        $this->userRepository = $userRepository;
    }

    /**
     * This method use for password reset mail send and token generation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $input = $request->all();
        $rules = [
            'email' => 'required|email',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->apiBaseService->sendErrorResponse('validation error', $validator->errors()->first(), HttpStatusCode::BAD_REQUEST->value);
        } else {
            try {
                $user = $this->userRepository->findOneByProperties(['email' => $input['email']]);
                if ($user) {
                    $token = Str::random(60);
                    $passwordUpdate = new PasswordReset();
                    $passwordUpdate->email = $input['email'];
                    $passwordUpdate->token = $token;
                    $passwordUpdate->created_at = now();
                    $passwordUpdate->save();
                    Mail::to($user->email)->send(new ForgetPasswordMail($user, $token));

                    try {
                        dispatch(function () use ($passwordUpdate) {
                            $passwordUpdate->forceDelete();
                        })->delay(now()->addMinutes(4));
                    } catch (Exception $exception) {
                        return $this->sendErrorResponse($exception->getMessage(), [], 'Failed reset password');
                    }

                    return $this->apiBaseService->sendSuccessResponse([], 'Reset link sent to your email');
                } else {
                    return $this->apiBaseService->sendErrorResponse('Invalid Email', ['detail' => 'User Not found'],
                        HttpStatusCode::BAD_REQUEST->value
                    );
                }

            } catch (Exception $exception) {
                return $this->sendErrorResponse($exception->getMessage(), [], $exception->getStatusCode());
            }

        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPasswordTokenVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse('validation error', $validator->errors(), HttpStatusCode::VALIDATION_ERROR->value);
        }

        $input = $request->all();
        $verified = PasswordReset::where('token', $input['token'])->first();
        if ($verified) {
            $data = [
                'token' => $input['token'],
                'email' => $verified->email,
                'validation' => true,
            ];

            return $this->apiBaseService->sendSuccessResponse($data, 'This token is valid token');
        } else {
            return $this->apiBaseService->sendErrorResponse('Token not found', [], HttpStatusCode::NOT_FOUND->value);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'min:8|max:64|required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse('validation error', $validator->errors(), HttpStatusCode::VALIDATION_ERROR->value);
        }

        $verified = PasswordReset::where('token', $request['token'])->first();

        if ($verified) {
            $verified->delete();
        } else {
            return $this->sendErrorResponse('validation error', $validator->errors(), HttpStatusCode::VALIDATION_ERROR->value);
        }

        $input = $request->all();
        try {
            $user = $this->userRepository->findOneBy(['email' => $input['email']]);
            if ($user) {
                $user->password = Hash::make($input['password']);
                $user->save();
                // PasswordReset::where('token', (string)$input['token'])->delete();

                return $this->apiBaseService->sendSuccessResponse($user, 'Password updated successfully');

            } else {
                return $this->apiBaseService->sendErrorResponse('Invalid Token', ['detail' => 'User Not found'],
                    HttpStatusCode::BAD_REQUEST
                );
            }

        } catch (Exception $exception) {
            return $this->apiBaseService->sendErrorResponse($exception->getMessage(), [], $exception->getStatusCode());
        }
    }
}
