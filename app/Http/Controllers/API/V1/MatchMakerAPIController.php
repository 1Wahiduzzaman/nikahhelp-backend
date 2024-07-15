<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Matchmaker\BusinessInformationRequest;
use App\Http\Requests\Matchmaker\ContactInformationRequest;
use App\Http\Requests\Matchmaker\CreateMatchMakerAPIRequest;
use App\Http\Requests\Matchmaker\EssentialInformationRequest;
use App\Http\Requests\Matchmaker\ImageUploadRequest;
use App\Http\Requests\Matchmaker\VerifyIdentityRequest;
use App\Models\MatchMaker;
use App\Repositories\MatchMakerRepository;
use App\Services\MatchmakerService;
use Illuminate\Http\Request;
use Response;

/**
 * Class MatchMakerController
 */
class MatchMakerAPIController extends AppBaseController
{
    private \App\Repositories\MatchMakerRepository $matchMakerRepository;

    private \App\Services\MatchmakerService $matchmakerService;

    public function __construct(MatchMakerRepository $matchMakerRepo, MatchmakerService $matchmakerService)
    {
        $this->matchMakerRepository = $matchMakerRepo;
        $this->matchmakerService = $matchmakerService;
    }

    /**
     * Display a listing of the MatchMaker.
     * GET|HEAD /matchMakers
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->matchmakerService->getMatchMakerInformation();
    }

    /**
     * Store a newly created MatchMakerInformation in storage.
     * POST /MatchMakerInformation
     *
     *
     * @return Response
     */
    public function matchMakerScreenName(CreateMatchMakerAPIRequest $request)
    {
        return $this->matchmakerService->storeScreenName($request->all());
    }

    /**
     * Store a newly created MatchMaker business Information in storage.
     * POST /BusinessInformationRequest
     *
     *
     * @return Response
     */
    public function businessInformation(BusinessInformationRequest $request)
    {
        return $this->matchmakerService->businessInformation($request->all());
    }

    /**
     * Store a newly created MatchMaker in storage.
     * POST /representativeInformations
     *
     *
     * @return Response
     */
    public function contactInformation(ContactInformationRequest $request)
    {
        return $this->matchmakerService->storeContactInformation($request->all());
    }

    /**
     * Store a newly created MatchMakerEssentialInformationRequest in storage.
     * POST /representativeInformations
     *
     *
     * @return Response
     */
    public function essentialInformation(EssentialInformationRequest $request)
    {
        return $this->matchmakerService->storeEssentialInformation($request->all());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyIdentity(VerifyIdentityRequest $request)
    {
        return $this->matchmakerService->storeVerifyIdentity($request->all());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function imageUpload(ImageUploadRequest $request)
    {
        return $this->matchmakerService->imageUpload($request->all());
    }

    /**
     * Remove the specified MatchMaker from storage.
     * DELETE /matchMakers/{id}
     *
     * @param  int  $id
     * @return Response
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var MatchMaker $matchMaker */
        $matchMaker = $this->matchMakerRepository->find($id);

        if (empty($matchMaker)) {
            return $this->sendError('Match Maker not found');
        }

        $matchMaker->delete();

        return $this->sendSuccess('Match Maker deleted successfully');
    }

    /**
     * @return mixed
     */
    public function finalSubmit(Request $request)
    {
        return $this->matchmakerService->finalSubmit($request->all());
    }
}
