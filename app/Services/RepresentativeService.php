<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use App\Models\Occupation;
use App\Models\RepresentativeInformation;
use App\Models\CandidateImage;
use App\Models\User;
use App\Repositories\CountryRepository;
use App\Transformers\RepresentativeTransformer;
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


    const INFORMATION_FETCHED_SUCCESSFULLY = 'Information fetched Successfully!';
    const INFORMATION_UPDATED_SUCCESSFULLY = 'Information updated Successfully!';
    const IMAGE_DELETED_SUCCESSFULLY = 'Image Deleted successfully!';


    /**
     * @var RepresentativeRepository
     */
    protected $representativeRepository;
    protected $countryRepository;

    /**
     * @var RepresentativeTransformer
     */
    private $representativeTransformer;


    public function __construct(
        RepresentativeRepository $representativeRepository,
        CountryRepository $countryRepository,
        RepresentativeTransformer $representativeTransformer
    )
    {
        $this->representativeRepository = $representativeRepository;
        $this->countryRepository = $countryRepository;
        $this->representativeTransformer = $representativeTransformer;
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


    public function getRepresentativeProfileInfo($userId)
    {
        try {
            $representativeInformation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$representativeInformation) {
                throw (new ModelNotFoundException)->setModel(get_class($this->representativeRepository->getModel()), $userId);
            }
            $data = $this->representativeTransformer->profileInfo($representativeInformation);

            return $this->sendSuccessResponse($data, self::INFORMATION_FETCHED_SUCCESSFULLY);


        }catch (Exception $exception){
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
        $representativeInformation = $this->representativeRepository->findOneByProperties(['user_id' => $userId]);

        if (!$representativeInformation) {
            throw (new ModelNotFoundException)->setModel(get_class($this->representativeRepository->getModel()), [$userId]);
        }
        $data = $this->representativeTransformer->transform($representativeInformation);
        $data['countries'] = $this->countryRepository->findAll()->where('status', '=', 1);
        $data['occupations'] = Occupation::all();
        return $this->sendSuccessResponse($data, self::INFORMATION_FETCHED_SUCCESSFULLY);

    }

    /**
     * @param $request
     * @return JsonResponse
     */

    public function storeVerifyIdentity($request)
    {
        $requestData = $request->all();
        if (!empty($request['ver_document_frontside'])) {
            $image = $this->uploadImageThrowGuzzle([
                'ver_document_frontside'=>$request->file('ver_document_frontside'),
            ]);
            $requestData['ver_document_frontside'] = $image->ver_document_frontside;
        }
        if (!empty($request['ver_document_backside'])) {
            $image = $this->uploadImageThrowGuzzle([
                'ver_document_backside'=>$request->file('ver_document_backside'),
            ]);
            $requestData['ver_document_backside'] = $image->ver_document_backside;
        }
        try {
            $userId = self::getUserId();
            $representativeInformation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInformation) {
                return $this->sendErrorResponse('Representative information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }

            $representative = $representativeInformation->update($requestData);

            $data = $this->representativeTransformer->transformVerificationInformation($representativeInformation);

            if ($representative) {

                return $this->sendSuccessResponse($data, 'Information save Successfully!', [], HttpStatusCode::CREATED);
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
            $image = $this->uploadImageThrowGuzzle([
                'per_avatar_url'=>$request->file('per_avatar_url'),
            ]);
            $representative['per_avatar_url'] = $image->per_avatar_url;
        }
        if (!empty($request['per_main_image_url'])) {
            $image = $this->uploadImageThrowGuzzle([
                'per_main_image_url'=>$request->file('per_main_image_url'),
            ]);
            $representative['per_main_image_url'] = $image->per_main_image_url;
        }
        try {
            $userId = self::getUserId();
            $representativeInformation = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$representativeInformation) {
                return $this->sendErrorResponse('Representative information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }

            $representative = $representativeInformation->update($representative);
            $data = $this->representativeTransformer->transformGallery($representativeInformation);

            Notificationhelpers::add('Picture update successfully complete', 'single', null, $userId);
            if ($representative) {
                return $this->sendSuccessResponse($data, 'Information save Successfully!', [], HttpStatusCode::CREATED);
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

    /**
     * This function is for update Representative info status ( DB field representative_information.data_input_status ) update
     * @param Request $request
     * @return JsonResponse
     */
    public function updateInfoStatus(Request $request): JsonResponse
    {

        $userId = self::getUserId();

        try {
            $representative = $this->representativeRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$representative) {
                throw (new ModelNotFoundException)->setModel(get_class($this->representativeRepository->getModel()), $userId);
            }

            DB::beginTransaction();
            $info['data_input_status'] = $request->data_input_status;
            $representative->update($info);

            $candidate_basic_info = $this->representativeTransformer->transformPersonalBasic($representative);
            DB::commit();
            return $this->sendSuccessResponse($candidate_basic_info, self::INFORMATION_UPDATED_SUCCESSFULLY);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

}
