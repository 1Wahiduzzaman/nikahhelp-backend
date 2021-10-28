<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use App\Models\RepresentativeInformation;
use App\Models\CandidateImage;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use \Illuminate\Support\Facades\DB;
use App\Transformers\CandidateTransformer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\RepresentativeResource;

class RepresentativeService extends ApiBaseService
{
    use CrudTrait;

    const DOCUMENT_TYPE = [
        '0' => 'Driving license',
        '1' => 'Passport',
        '2' => 'National id card',
        '3' => 'Residence permit'
    ];

    use CrudTrait;

    /**
     * @var RepresentativeRepository
     */
    protected $representativeRepository;

    public function __construct(RepresentativeRepository $representativeRepository)
    {
        $this->representativeRepository = $representativeRepository;

    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function storeScreenName($request)
    {
        try {
            $userId = self::getUserId();
            $checkRepresentative = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if ($checkRepresentative) {
                return $this->sendErrorResponse('Representative Information Already Exists', [], FResponse::HTTP_CONFLICT);
            }
            $request['user_id'] = $userId;
            $representative = $this->representativeRepository->save($request);
            if ($representative) {
                $userInfo = User::find($userId);
                if ($userInfo) {
                    $userInfo->full_name = trim($request['first_name']) . ' ' . trim($request['last_name']);
                    $userInfo->save();
                }
                return $this->sendSuccessResponse($representative->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function storeEssentialInformation($request)
    {
        try {
            $userId = self::getUserId();
            $representativeInfomation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInfomation) {
                return $this->sendErrorResponse('Representative information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $representative = $representativeInfomation->update($request);
            if ($representative) {
                return $this->sendSuccessResponse($representativeInfomation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function storeContactInformation($request)
    {
        try {
            $userId = self::getUserId();
            $representativeInfomation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInfomation) {
                return $this->sendErrorResponse('Representative information  Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $representative = $representativeInfomation->update($request);
            if ($representative) {
                return $this->sendSuccessResponse($representativeInfomation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    /**
     * @return JsonResponse
     */
    public function getRepresentativeInformation():JsonResponse
    {
        $userId = self::getUserId();
        $representativeInformation = $this->representativeRepository->findBy(['user_id' => $userId]);

        if ($representativeInformation) {
            $result = RepresentativeResource::collection($representativeInformation);
            return $this->sendSuccessResponse($result, 'Representative Information', [], HttpStatusCode::SUCCESS);
        } else {
            return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */

    public function storeVerifyIdentity($request)
    {
        if ($request['is_document_upload'] == 1 && !empty($request['ver_document_frontside'])) {
            $ver_document_frontside = self::uploadFile($request, 'ver_document_frontside');
            $request['ver_document_frontside'] = $ver_document_frontside['image_path'];
        }
        if ($request['is_document_upload'] == 1 && !empty($request['ver_document_backside'])) {
            $ver_document_backside = self::uploadFile($request, 'ver_document_backside');
            $request['ver_document_backside'] = $ver_document_backside['image_path'];
        }
        try {
            $userId = self::getUserId();
            $representativeInfomation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInfomation) {
                return $this->sendErrorResponse('Representative information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $representative = $representativeInfomation->update($request);
            if ($representative) {

                return $this->sendSuccessResponse($representativeInfomation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */

    public function imageUpload($request)
    {
        if (!empty($request['per_avatar_url'])) {
            $per_avatar_url = self::uploadFile($request, 'per_avatar_url');
            $request['per_avatar_url'] = $per_avatar_url['image_path'];
        }
        if (!empty($request['per_main_image_url'])) {
            $per_main_image_url = self::uploadFile($request, 'per_main_image_url');
            $request['per_main_image_url'] = $per_main_image_url['image_path'];
        }
        try {
            $userId = self::getUserId();
            $representativeInfomation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInfomation) {
                return $this->sendErrorResponse('Representative information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['data_input_status'] = 1;
            $request['user_id'] = $userId;
            $representative = $representativeInfomation->update($request);
            Notificationhelpers::add('Picture update successfully complete', 'single', null, $userId);
            if ($representative) {
                return $this->sendSuccessResponse($representativeInfomation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function uploadFile($request, $imageName = null)
    {
        $requestFile = $request[$imageName];
        $file = 'Representative-profile-' . self::getUserId();
        $image_type = $imageName;
        $disk = config('filesystems.default', 'local');
        $status = $requestFile->storeAs($file, $image_type . '-' . date('Ymd') . '-' . $requestFile->getClientOriginalName(), $disk);
        return [
            CandidateImage::IMAGE_PATH => $status,
            CandidateImage::IMAGE_DISK => $disk
        ];

    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function finalSubmit($request)
    {
        try {
            $userId = self::getUserId();
            $representativeInfomation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInfomation) {
                return $this->sendErrorResponse('Representative information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $representative = $representativeInfomation->update($request);
            if ($representative) {
                return $this->sendSuccessResponse($representativeInfomation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

}
