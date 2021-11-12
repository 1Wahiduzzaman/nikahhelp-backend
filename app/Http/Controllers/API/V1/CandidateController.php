<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\CandidatePersonalVerificationRequest;
use App\Http\Requests\CandidateImageUploadRequest;
use App\Http\Requests\CandidatePreferenceInfoRequest;
use App\Http\Requests\Candidate\CandidatePreferenceAboutRequest;
use App\Http\Requests\Candidate\CandidatePreferenceRatingRequest;
use App\Http\Requests\Candidate\CandidatePersonalEssentialInInformationRequest;
use App\Http\Requests\Candidate\CandidatePersonalGeneralInInformationRequest;
use App\Http\Requests\Candidate\CandidatePersonalContactInformationRequest;
use App\Http\Requests\Candidate\CandidatePersonalAboutMoreRequest;
use App\Models\CandidateImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\CandidateService;
use App\Http\Requests\CandidatePersonalInfoRequest;
use App\Http\Requests\CandidateFamilyInfoRequest;
use App\Http\Requests\CandidateCreateRequest;
use Illuminate\Http\Response;

//use Illuminate\Support\Facades\Request;

class CandidateController extends Controller
{
    /**
     * @var CandidateService
     */
    protected $candidateService;

    /**
     * PurchaseController constructor.
     * @param CandidateService $candidateService
     */
    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function index(int $userId): JsonResponse
    {
        return $this->candidateService->fetchCandidateInfo($userId);
    }

    /**
     * @param \Illuminate\Http\CandidateCreateRequest $request
     * @return mixed
     */
    public function create(CandidateCreateRequest $request)
    {
        return $this->candidateService->store($request->all());
    }

    /**
     * @return JsonResponse
     */
    public function candidateProfileInitialInfo(): JsonResponse
    {
        return $this->candidateService->fetchProfileInitialInfo();
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function candidatePersonalInfo(): JsonResponse
    {
        return $this->candidateService->fetchCandidatePersonalInfo();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeCandidateBasicInformation(Request $request,int $userId): JsonResponse
    {
        return $this->candidateService->candidateBasicInfoStore($request, $userId);
    }

    /**
     * @param CandidatePersonalInfoRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updatePersonalInformation(CandidatePersonalInfoRequest $request, int $userId): JsonResponse
    {
        return $this->candidateService->candidatePersonalInfoUpdate($request, $userId);
    }

    /**
     * @param CandidatePersonalEssentialInInformationRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updatePersonalEssentialInInformation(CandidatePersonalEssentialInInformationRequest $request): JsonResponse
    {
        return $this->candidateService->candidateEssentialPersonalInfoUpdate($request);
    }

    /**
     * @param CandidatePersonalGeneralInInformationRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updatePersonalGeneralInInformation(CandidatePersonalGeneralInInformationRequest $request): JsonResponse
    {
        return $this->candidateService->candidatePersonalGeneralInfoUpdate($request);
    }

    /**
     * @param CandidatePersonalContactInformationRequest $request
     * @return JsonResponse
     */
    public function updatePersonalContactInformation(CandidatePersonalContactInformationRequest $request): JsonResponse
    {
        return $this->candidateService->candidatePersonalContactInfoUpdate($request);
    }

    /**
     * @param CandidatePersonalAboutMoreRequest $request
     * @return JsonResponse
     */
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
     * @param CandidateFamilyInfoRequest $request
     * @return JsonResponse
     */
    public function updateCandidateFamilyInformation(CandidateFamilyInfoRequest $request): JsonResponse
    {
        return $this->candidateService->candidateFamilyInfoUpdate($request);
    }

    /**
     * Fetch Candidate Preference info
     * @param int $userId
     * @return JsonResponse
     */
    public function fetchCandidatePreference(int $userId): JsonResponse
    {
        return $this->candidateService->fetchPreferenceInfo($userId);
    }

    /**
     * Update Candidate Preference info
     * @param CandidatePreferenceInfoRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function storeCandidatePreference(CandidatePreferenceInfoRequest $request): JsonResponse
    {

        return $this->candidateService->storePreferenceInfo($request);
    }

    /**
     * Update Candidate Preference info
     * @param CandidatePreferenceAboutRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function storeCandidatePreferenceAbout(CandidatePreferenceAboutRequest $request): JsonResponse
    {
        return $this->candidateService->storePreferenceAbout($request);
    }

    /**
     * Update Candidate Preference info
     * @param CandidatePreferenceRatingRequest $request
     * @return JsonResponse
     */
    public function storeCandidatePreferenceRating(CandidatePreferenceRatingRequest $request): JsonResponse
    {
        return $this->candidateService->storePreferenceRate($request);
    }

    /**
     * Get Candidate Validation info
     * @return JsonResponse
     */
    public function getCandidatePersonalVerification(): JsonResponse
    {
        return $this->candidateService->getVerificationInfo();
    }

    /**
     * Store Candidate Validation info
     * @param CandidatePersonalVerificationRequest $request
     * @return JsonResponse
     */
    public function updateCandidatePersonalVerification(CandidatePersonalVerificationRequest $request): JsonResponse
    {
        return $this->candidateService->updateVerificationInfo($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function viewImage(Request $request): JsonResponse
    {
        $filter = [
            'user_id' => $request->user()->id
        ];
        return $this->candidateService->listImage($filter);
    }

    /**
     * @param CandidateImageUploadRequest $request
     * @return JsonResponse
     */
    public function storeImage(CandidateImageUploadRequest $request): JsonResponse
    {
        return $this->candidateService->uploadImage($request);
    }

    /**
     * @param CandidateImageUploadRequest $request
     * @param CandidateImage $candidateImage
     * @return JsonResponse
     */
    public function updateImage(CandidateImageUploadRequest $request): JsonResponse
    {
        return $this->candidateService->updateImage($request);
    }

    /**
     * @param CandidateImage $candidateImage
     * @return JsonResponse
     */
    public function deleteImage(CandidateImage $candidateImage): JsonResponse
    {
        return $this->candidateService->deleteImage($candidateImage);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function viewGallery(Request $request){
        return $this->candidateService->getCandidateGallery($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function profileSuggestions(Request $request){
        return $this->candidateService->suggestions();
    }


}
