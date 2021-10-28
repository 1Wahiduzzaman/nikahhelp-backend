<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use App\Http\Resources\MatchmakerResource;
use App\Models\RepresentativeInformation;
use App\Models\CandidateImage;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use App\Repositories\MatchMakerRepository;
use \Illuminate\Support\Facades\DB;
use App\Transformers\CandidateTransformer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response as FResponse;
use App\Http\Resources\RepresentativeResource;

class MatchmakerService extends ApiBaseService
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
    /**
     * @var MatchMakerRepository
     */
    protected $matchMakerRepository;

    public function __construct(RepresentativeRepository $representativeRepository, MatchMakerRepository $matchMakerRepository)
    {
        $this->representativeRepository = $representativeRepository;
        $this->matchMakerRepository = $matchMakerRepository;

    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function storeScreenName($request)
    {
        try {
            $userId = self::getUserId();
            $checkRepresentative = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if ($checkRepresentative) {
                return $this->sendErrorResponse('Matchmaker Information Already Exists', [], FResponse::HTTP_CONFLICT);
            }
            $request['user_id'] = $userId;
            $representative = $this->matchMakerRepository->save($request);
            if ($representative) {
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
            $matchMakerInformation = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$matchMakerInformation) {
                return $this->sendErrorResponse('Matchmaker information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $matchmaker = $matchMakerInformation->update($request);
            if ($matchmaker) {
                return $this->sendSuccessResponse($matchMakerInformation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
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
    public function businessInformation($request)
    {
        try {
            $userId = self::getUserId();
            $matchmakerInformation = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$matchmakerInformation) {
                return $this->sendErrorResponse('Matchmaker information  Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $matchmaker = $matchmakerInformation->update($request);
            if ($matchmaker) {
                return $this->sendSuccessResponse($matchmakerInformation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
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
            $matchmakerInformation = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$matchmakerInformation) {
                return $this->sendErrorResponse('Matchmaker information  Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $matchmaker = $matchmakerInformation->update($request);
            if ($matchmaker) {
                return $this->sendSuccessResponse($matchmakerInformation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
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
    public function getMatchMakerInformation(): JsonResponse
    {
        $userId = self::getUserId();
        $matchMakerInformation = $this->matchMakerRepository->findBy(['user_id' => $userId]);

        if ($matchMakerInformation) {
            $result = MatchmakerResource::collection($matchMakerInformation);
            return $this->sendSuccessResponse($result, 'Matchmaker Information', [], HttpStatusCode::SUCCESS);
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
            $matchMakerInformation = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$matchMakerInformation) {
                return $this->sendErrorResponse('Match maker information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $matchmaker = $matchMakerInformation->update($request);
            if ($matchmaker) {

                return $this->sendSuccessResponse($matchMakerInformation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
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
            $matchMakerInformation = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$matchMakerInformation) {
                return $this->sendErrorResponse('Match maker information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['data_input_status'] = 1;
            $request['user_id'] = $userId;
            $matchmaker = $matchMakerInformation->update($request);
            Notificationhelpers::add('Picture update successfully complete', 'single', null, $userId);
            if ($matchmaker) {
                $matchMakerInformation['per_avatar_url'] = url('storage/' . $matchMakerInformation->per_avatar_url) ?? null;
                $matchMakerInformation['per_main_image_url'] = url('storage/' . $matchMakerInformation->per_main_image_url) ?? null;
                return $this->sendSuccessResponse($matchMakerInformation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
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
        $file = 'Matchmaker-profile-' . self::getUserId();
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
            $matchMakerInformation = $this->matchMakerRepository->findOneByProperties([
                'user_id' => $userId
            ]);
            if (!$matchMakerInformation) {
                return $this->sendErrorResponse('Match maker information is Not fund', [], HttpStatusCode::NOT_FOUND);
            }
            $request['user_id'] = $userId;
            $matchmaker = $matchMakerInformation->update($request);
            if ($matchmaker) {
                return $this->sendSuccessResponse($matchMakerInformation->toArray(), 'Information save Successfully!', [], HttpStatusCode::CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

}
