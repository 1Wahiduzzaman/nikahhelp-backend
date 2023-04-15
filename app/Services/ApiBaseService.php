<?php

namespace App\Services;

use App\Enums\ApiCustomStatusCode;
use App\Enums\HttpStatusCode;
use App\Models\PictureServerToken;
use App\Models\User;
use http\Env\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Contracts\ApiBaseServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JWTAuth;

/**
 * Class ApiBaseService
 * @package App\Services
 */
class ApiBaseService implements ApiBaseServiceInterface
{

    /**
     * Success response method.
     *
     * @param array $result
     * @param $message
     * @param array $pagination
     * @param int $http_status
     * @param int $status_code
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendSuccessResponse(
        $result,
        $message,
        $pagination = [],
        $http_status = HttpStatusCode::SUCCESS,
        $status_code = ApiCustomStatusCode::SUCCESS
    ) {
        $response = [
            'status' => 'SUCCESS',
            'status_code' => $status_code,
            'message' => $message,
            'data' => $result
        ];

        if (!empty($pagination)) {
            $response ['pagination'] = $pagination;
        }

        return response()->json($response, $http_status);
    }


    /**
     * Return error response.
     *
     * @param $message
     * @param array $errorMessages
     * @param int $status_code
     *
     */
    public function sendErrorResponse($message, $errorMessages = [], $status_code = HttpStatusCode::VALIDATION_ERROR)
    {
        $response = [
            'status' => 'FAIL',
            'status_code' => $status_code,
            'message' => $message,
        ];

        if (!empty($errorMessages)) {
            $response['error'] = $errorMessages;
        }

        return response()->json($response, $status_code);
    }


    /**
     * Return Response with pagination
     *
     * @param $items
     * @return array
     */
    public function paginationResponse($items)
    {
        return array(
            'total_items' => $items->total(),
            'current_items' => $items->count(),
            'first_item' => $items->firstItem(),
            'last_item' => $items->lastItem(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'has_more_pages' => $items->hasMorePages(),
        );
    }

    public function getUserId(){
         $user = JWTAuth::parseToken()->authenticate();
         return $user['id'];
    }
    public function getUserInfo(){
        return $user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Upload image Throw Guzzle
     * @param array $images
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImageThrowGuzzle(array $images)
    {
        $userId = self::getUserId();
        $userUUID = (string) Str::uuid();

//        $folderType = array_key_exists('screenshot', $images) ? 'candidate_support/screenshot_'.$userUUID. '/':
//            'candidate/candidate_'.$userUUID.'/';

//        $output = [];
//        $i = 0;
//        foreach ($images as $key=>$image){
//            $data[$i] = [
//                [
//                    'name'     => 'image['.$i.'][name]',
//                    'contents' => $key,
//                ],
//                [
//                    'name'     => 'image['.$i.'][file]',
//                    'contents' => file_get_contents($image),
//                    'filename' => $key.'.'.$image->getClientOriginalExtension(),
//                ],
//                [
//                    'name'     => 'image['.$i.'][path]',
//                    'contents' => $folderType,
//                ],
//            ];
//
//            $output = array_merge($output,$data[$i]);
//
//            $i++;
//        }


        $user = User::find($userId);

        DB::beginTransaction();
        try {
            $picture_db =  PictureServerToken::find($user);
            $picture_db->user_uuid = $userUUID;
            $picture_db->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }


        $token = ImageServerService::getTokenFromDatabase($user);

        if (isset($token)) {
            $client = new \GuzzleHttp\Client();
            $requestc = $client->request('POST',env('IMAGE_SERVER').'/api/img',[
                'image' => $images,
                'user_id' => $userUUID,
                'headers' =>
                    [
                        'Authorization' => "Bearer {$token}"
                    ]
                
            ]);

            $response = $requestc->getBody();

            return json_decode($response);

        }

        return json_decode(response()->json(['per_avatar_url' => 'failed', 'per_main_image_url' => 'failed', 'other_images' => 'failed']));
    }

    public function deleteImageGuzzle(String $filename)
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
                    ['headers' =>
                        [
                            'Authorization' => "Bearer {$token}"
                        ]
                    ]
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
