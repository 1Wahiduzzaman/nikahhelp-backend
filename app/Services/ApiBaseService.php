<?php

namespace App\Services;

use App\Contracts\ApiBaseServiceInterface;
use App\Enums\ApiCustomStatusCode;
use App\Enums\HttpStatusCode;
use App\Models\PictureServerToken;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class ApiBaseService
 */
class ApiBaseService implements ApiBaseServiceInterface
{
    /**
     * Success response method.
     *
     * @param  array  $result
     * @param  array  $pagination
     * @param  int  $http_status
     * @param  int  $status_code
     * @return JsonResponse
     */
    public function sendSuccessResponse(
        $result,
        $message,
        $pagination = [],
        $http_status = HttpStatusCode::SUCCESS->value,
        $status_code = ApiCustomStatusCode::SUCCESS->value
    ) {
        $response = [
            'status' => 'SUCCESS',
            'status_code' => $status_code,
            'message' => $message,
            'data' => $result,
        ];

        if (! empty($pagination)) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $http_status);
    }

    /**
     * Return error response.
     *
     * @param  array  $errorMessages
     * @param  int  $status_code
     */
    public function sendErrorResponse($message, $errorMessages = [], $status_code = HttpStatusCode::VALIDATION_ERROR->value)
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => $status_code,
            'message' => $message,
        ];

        if (! empty($errorMessages)) {
            $response['error'] = $errorMessages;
        }

        return response()->json($response, $status_code);
    }

    /**
     * Return Response with pagination
     *
     * @return array
     */
    public function paginationResponse($items)
    {
        return [
            'total_items' => $items->total(),
            'current_items' => $items->count(),
            'first_item' => $items->firstItem(),
            'last_item' => $items->lastItem(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'has_more_pages' => $items->hasMorePages(),
        ];
    }

    public function getUserId()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $user['id'];
    }

    /**
     * @throws AuthenticationException
     */
    public function getUserInfo()
    {
        return $user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Upload image Throw Guzzle
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImageThrowGuzzle(string $imageName, \Illuminate\Http\Request $images)
    {
        $userId = self::getUserId();
        $userUUID = (string) Str::uuid();

        try {
            $user = User::find($userId);
            $picture_db = PictureServerToken::where('user_id', $user->id)->first();

            $picture_db->user_uuid = $userUUID;

            $picture_db->save();

            $token = ImageServerService::getTokenFromDatabase($user);
            if (isset($token)) {
                $client = new Client();
                $requestc = $client->request('post', config('chobi.chobi').'/api/img/'.$userUUID, [
                    'multipart' => [
                        ['name' => 'image',
                            'contents' => $images->file($imageName)->getContent(),
                            'filename' => $imageName.'.'.$images->file($imageName)->getClientOriginalExtension(),
                        ],

                    ],
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                    ],
                ]);
                $response = $requestc->getBody();

                return json_decode($response);
            }

            return json_decode(response()->json([$imageName => 'failed']));

        } catch (\Exception $exception) {
            return json_decode(response()->json([$imageName => 'error']));
        }
    }

    public function deleteImageGuzzle(string $filename)
    {
        $userId = self::getUserId();
        try {
            $user = User::find($userId);
            $token = ImageServerService::getTokenFromDatabase($user);
            $picture_db = PictureServerToken::find($userId);
            $user_UUID = $picture_db->user_uuid;
            if (isset($token)) {
                $client = new \GuzzleHttp\Client();

                $response = $client->request('delete', config('chobi.chobi').'/api/img', [
                    'path' => 'candidate/candidate_'.$user_UUID.'/',
                    'file' => $filename,
                    ['headers' => [
                        'Authorization' => "Bearer {$token}",
                    ],
                    ],
                ]);
                $response = $response->getBody();

                return json_decode($response);
            }

            return json_decode(response()->json(['per_avatar_url' => 'failed', 'per_main_image_url' => 'failed', 'other_images' => 'failed']));

        } catch (\Exception $exception) {
            Log::log('error', $exception->getMessage());
        }
    }
}
