<?php

namespace App\Services;

use App\Enums\ApiCustomStatusCode;
use App\Enums\HttpStatusCode;
use App\Models\PictureServerToken;
use App\Models\User;
use GuzzleHttp\Client;
use http\Env\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Contracts\ApiBaseServiceInterface;
use Illuminate\Http\UploadedFile;
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
     * @param \Illuminate\Http\Request $images
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImageThrowGuzzle(String $imageName, \Illuminate\Http\Request $images)
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

        try {
            $user = User::find($userId);
                $picture_db = PictureServerToken::where('user_id', $user->id)->first();

                $picture_db->user_uuid = $userUUID;

                $picture_db->save();


            $token = ImageServerService::getTokenFromDatabase($user);
//            dd($token);
//        Log::info('file', ['data' => file_get_contents($images->file($imageName)->openFile())]);
            if (isset($token)) {
//                $requestc = Http::asMultipart()->withToken($token)->post(config('chobi.chobi') . '/api/img/' . $userUUID, [
//                    'image' => $images->file($imageName)->getContent()
//                ]);
             $client = new Client();
            $requestc = $client->request('post', config('chobi.chobi').'/api/img/'. $userUUID, [
                 'multipart' => [
                     [   'name' => 'image',
                         'contents' => $images->file($imageName)->getContent(),
                         'filename' => $imageName.'.'.$images->file($imageName)->getClientOriginalExtension(),
                     ],

                 ],
                'headers' => [
                    'Authorization' => 'Bearer '.$token
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
