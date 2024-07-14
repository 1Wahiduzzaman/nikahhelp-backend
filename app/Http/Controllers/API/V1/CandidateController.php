<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\CandidateInfoStatusUpdateRequest;
use App\Http\Requests\Candidate\CandidatePersonalAboutMoreRequest;
use App\Http\Requests\Candidate\CandidatePersonalContactInformationRequest;
use App\Http\Requests\Candidate\CandidatePersonalEssentialInInformationRequest;
use App\Http\Requests\Candidate\CandidatePersonalGeneralInInformationRequest;
use App\Http\Requests\Candidate\CandidatePersonalVerificationRequest;
use App\Http\Requests\Candidate\CandidatePreferenceAboutRequest;
use App\Http\Requests\Candidate\CandidatePreferenceRatingRequest;
use App\Http\Requests\CandidateCreateRequest;
use App\Http\Requests\CandidateFamilyInfoRequest;
use App\Http\Requests\CandidateImageUploadRequest;
use App\Http\Requests\CandidatePersonalInfoRequest;
use App\Http\Requests\CandidatePreferenceInfoRequest;
use App\Models\CandidateImage;
use App\Services\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

//use Illuminate\Support\Facades\Request;

class CandidateController extends Controller
{
    protected \App\Services\CandidateService $candidateService;

    /**
     * PurchaseController constructor.
     */
    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function index(int $userId): JsonResponse
    {
        return $this->candidateService->fetchCandidateInfo($userId);
    }

    /**
     * @param  \Illuminate\Http\CandidateCreateRequest  $request
     * @return mixed
     */
    public function create(CandidateCreateRequest $request)
    {
        return $this->candidateService->store($request->all());
    }

    public function candidateProfileInitialInfo(): JsonResponse
    {
        return $this->candidateService->fetchProfileInitialInfo();
    }

    public function candidateStatus(): JsonResponse
    {
        return $this->candidateService->candidateStatus();
    }

    /**
     * @param  int  $userId
     */
    public function candidatePersonalInfo(): JsonResponse
    {
        return $this->candidateService->fetchCandidatePersonalInfo();
    }

    /**
     * Display a listing of the resource.
     */
    public function storeCandidateBasicInformation(Request $request, int $userId): JsonResponse
    {
        return $this->candidateService->candidateBasicInfoStore($request, $userId);
    }

    public function updatePersonalInformation(CandidatePersonalInfoRequest $request, int $userId): JsonResponse
    {
        return $this->candidateService->candidatePersonalInfoUpdate($request, $userId);
    }

    /**
     * @param  int  $userId
     */
    public function updatePersonalEssentialInInformation(CandidatePersonalEssentialInInformationRequest $request): JsonResponse
    {
        return $this->candidateService->candidateEssentialPersonalInfoUpdate($request);
    }

    /**
     * @param  int  $userId
     */
    public function updatePersonalGeneralInInformation(CandidatePersonalGeneralInInformationRequest $request): JsonResponse
    {
        return $this->candidateService->candidatePersonalGeneralInfoUpdate($request);
    }

    public function updatePersonalContactInformation(CandidatePersonalContactInformationRequest $request): JsonResponse
    {
        return $this->candidateService->candidatePersonalContactInfoUpdate($request);
    }

    public function updatePersonalInformationMoreAbout(CandidatePersonalAboutMoreRequest $request): JsonResponse
    {
        return $this->candidateService->candidatePersonalMoreAboutInfoUpdate($request);
    }

    /**
     * Display a listing of family info.
     *
     * @return Response
     */
    public function listCandidateFamilyInformation(Request $request)
    {
        return $this->candidateService->candidateFamilyInfoList($request);
    }

    /**
     * Update a family info.
     */
    public function updateCandidateFamilyInformation(CandidateFamilyInfoRequest $request): JsonResponse
    {
        return $this->candidateService->candidateFamilyInfoUpdate($request);
    }

    /**
     * Fetch Candidate Preference info
     */
    public function fetchCandidatePreference(int $userId): JsonResponse
    {
        return $this->candidateService->fetchPreferenceInfo($userId);
    }

    /**
     * Update Candidate Preference info
     *
     * @param  int  $userId
     */
    public function storeCandidatePreference(CandidatePreferenceInfoRequest $request): JsonResponse
    {

        return $this->candidateService->storePreferenceInfo($request);
    }

    /**
     * Update Candidate Preference info
     *
     * @param  int  $userId
     */
    public function storeCandidatePreferenceAbout(CandidatePreferenceAboutRequest $request): JsonResponse
    {
        return $this->candidateService->storePreferenceAbout($request);
    }

    /**
     * Update Candidate Preference info
     */
    public function storeCandidatePreferenceRating(CandidatePreferenceRatingRequest $request): JsonResponse
    {
        return $this->candidateService->storePreferenceRate($request);
    }

    /**
     * Get Candidate Validation info
     */
    public function getCandidatePersonalVerification(): JsonResponse
    {
        return $this->candidateService->getVerificationInfo();
    }

    /**
     * Store Candidate Validation info
     */
    public function updateCandidatePersonalVerification(CandidatePersonalVerificationRequest $request): JsonResponse
    {
        return $this->candidateService->updateVerificationInfo($request);
    }

    /**
     * Update Candidate info status
     */
    public function updateCandidateInfoStatus(CandidateInfoStatusUpdateRequest $request): JsonResponse
    {
        return $this->candidateService->updateInfoStatus($request);
    }

    /**
     * @return JsonResponse
     */
    public function avatarImgUpload(Request $request)
    {
        return $this->candidateService->avatarImgUpload($request);
    }

    public function viewImage(Request $request): JsonResponse
    {
        return $this->candidateService->listImage();
    }

    public function storeImage(CandidateImageUploadRequest $request): JsonResponse
    {
        return $this->candidateService->uploadImage($request);
    }

    /**
     * @param  CandidateImage  $candidateImage
     */
    public function updateImage(CandidateImageUploadRequest $request): JsonResponse
    {
        return $this->candidateService->updateImage($request);
    }

    public function deleteImage(Request $request): JsonResponse
    {
        return $this->candidateService->removeImage($request->name);
    }

    public function deleteImageByType(Request $request, int $imageType): JsonResponse
    {
        return $this->candidateService->deleteImageByType($request, $imageType);
    }

    /**
     * @return JsonResponse
     */
    public function viewGallery(Request $request)
    {
        return $this->candidateService->getCandidateGallery($request);
    }

    /**
     * @return JsonResponse
     */
    public function profileSuggestions(Request $request)
    {
        return $this->candidateService->suggestions();
    }
}
