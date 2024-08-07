<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representative\ContactInformationRequest;
use App\Http\Requests\Representative\CreateRepresentativeInformationAPIRequest;
use App\Http\Requests\Representative\EssentialInformationRequest;
use App\Http\Requests\Representative\ImageUploadRequest;
use App\Http\Requests\Representative\RepresentativeInfoStatusUpdateRequest;
use App\Http\Requests\Representative\UpdateRepresentativeInformationAPIRequest;
use App\Http\Requests\Representative\VerifyIdentityRequest;
use App\Models\RepresentativeInformation;
use App\Repositories\RepresentativeInformationRepository;
use App\Services\RepresentativeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;

/**
 * Class RepresentativeInformationController
 */
class RepresentativeInformationController extends Controller
{
    private \App\Repositories\RepresentativeInformationRepository $representativeInformationRepository;

    private \App\Services\RepresentativeService $representativeService;

    public function __construct(
        RepresentativeInformationRepository $representativeInformationRepo,
        RepresentativeService $representativeService
    ) {
        $this->representativeService = $representativeService;
        $this->representativeInformationRepository = $representativeInformationRepo;
    }

    /**
     * Display a listing of the RepresentativeInformation.
     * GET|HEAD /representativeInformations
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->representativeService->getRepresentativeInformation();
    }

    /**
     * @return \App\Services\JsonResponse|\Illuminate\Http\Response
     */
    public function profileInfo(int $userId)
    {
        return $this->representativeService->getRepresentativeProfileInfo($userId);
    }

    /**
     * @param  int  $userId
     * @return \App\Services\JsonResponse|\Illuminate\Http\Response
     */
    public function representativeInfo()
    {
        return $this->representativeService->getRepresentativeInfo();
    }

    /**
     * Store a newly created RepresentativeInformation in storage.
     * POST /representativeInformations
     *
     *
     * @return Response
     */
    public function representativeScreenName(CreateRepresentativeInformationAPIRequest $request)
    {
        return $this->representativeService->storeScreenName($request->all());

    }

    /**
     * Store a newly created RepresentativeInformation in storage.
     * POST /representativeInformations
     *
     *
     * @return Response
     */
    public function contactInformation(ContactInformationRequest $request)
    {
        return $this->representativeService->storeEssentialInformation($request);

    }

    /**
     * Store a newly created RepresentativeInformation in storage.
     * POST /representativeInformations
     *
     *
     * @return Response
     */
    public function essentialInformation(EssentialInformationRequest $request)
    {
        return $this->representativeService->storeContactInformation($request->all());

    }

    /**
     * Display the specified RepresentativeInformation.
     * GET|HEAD /representativeInformations/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        /** @var RepresentativeInformation $representativeInformation */
        $representativeInformation = $this->representativeInformationRepository->find($id);

        if (empty($representativeInformation)) {
            return $this->sendError('Representative Information not found');
        }

        return $this->sendResponse($representativeInformation->toArray(), 'Representative Information retrieved successfully');
    }

    /**
     * Update the specified RepresentativeInformation in storage.
     * PUT/PATCH /representativeInformations/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, UpdateRepresentativeInformationAPIRequest $request)
    {
        $input = $request->all();

        /** @var RepresentativeInformation $representativeInformation */
        $representativeInformation = $this->representativeInformationRepository->find($id);

        if (empty($representativeInformation)) {
            return $this->sendError('Representative Information not found');
        }

        $representativeInformation = $this->representativeInformationRepository->update($input, $id);

        return $this->sendResponse($representativeInformation->toArray(), 'RepresentativeInformation updated successfully');
    }

    /**
     * Remove the specified RepresentativeInformation from storage.
     * DELETE /representativeInformations/{id}
     *
     * @param  int  $id
     * @return Response
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var RepresentativeInformation $representativeInformation */
        $representativeInformation = $this->representativeInformationRepository->find($id);

        if (empty($representativeInformation)) {
            return $this->sendError('Representative Information not found');
        }

        $representativeInformation->delete();

        return $this->sendSuccess('Representative Information deleted successfully');
    }

    public function verifyIdentity(VerifyIdentityRequest $request)
    {

        return $this->representativeService->storeVerifyIdentity($request);
    }

    public function imageUpload(ImageUploadRequest $request)
    {
        return $this->representativeService->imageUpload($request);
    }

    public function finalSubmit(Request $request)
    {
        return $this->representativeService->finalSubmit($request->all());
    }

    /**
     * Update Representative info status
     */
    public function updateRepresentativeInfoStatus(RepresentativeInfoStatusUpdateRequest $request): JsonResponse
    {
        return $this->representativeService->updateInfoStatus($request);
    }

    /**
     * Get Representative Status info
     *
     * @param  RepresentativeInfoStatusUpdateRequest  $request
     */
    public function representativeStatus(): JsonResponse
    {
        return $this->representativeService->representativeStatus();
    }

    public function deleteImage(int $imageType)
    {
        return $this->representativeService->removeImage($imageType);
    }
}
