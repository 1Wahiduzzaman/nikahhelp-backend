<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Matchmaker\CreateMatchMakerAPIRequest;
use App\Http\Requests\Matchmaker\UpdateMatchMakerAPIRequest;
use App\Http\Requests\Matchmaker\EssentialInformationRequest;
use App\Http\Requests\Matchmaker\ContactInformationRequest;
use App\Http\Requests\Matchmaker\BusinessInformationRequest;
use App\Http\Requests\Matchmaker\VerifyIdentityRequest;
use App\Http\Requests\Matchmaker\ImageUploadRequest;
use App\Models\MatchMaker;
use App\Repositories\MatchMakerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Services\MatchmakerService;

/**
 * Class MatchMakerController
 * @package App\Http\Controllers\API
 */
class MatchMakerAPIController extends AppBaseController
{

    /**
     * @var  MatchMakerRepository
     */
    private $matchMakerRepository;
    /**
     * @var  MatchmakerService
     */
    private $matchmakerService;

    public function __construct(MatchMakerRepository $matchMakerRepo, MatchmakerService $matchmakerService)
    {
        $this->matchMakerRepository = $matchMakerRepo;
        $this->matchmakerService = $matchmakerService;
    }

    /**
     * Display a listing of the MatchMaker.
     * GET|HEAD /matchMakers
     *
     * @param Request $request
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
     * @param CreateMatchMakerAPIRequest $request
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
     * @param BusinessInformationRequest $request
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
     * @param ContactInformationRequest $request
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
     * @param EssentialInformationRequest $request
     *
     * @return Response
     */
    public function essentialInformation(EssentialInformationRequest $request)
    {
        return $this->matchmakerService->storeEssentialInformation($request->all());
    }

    /**
     * @param VerifyIdentityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyIdentity(VerifyIdentityRequest $request)
    {
        return $this->matchmakerService->storeVerifyIdentity($request->all());
    }

    /**
     * @param ImageUploadRequest $request
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
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
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
     * @param Request $request
     * @return mixed
     */

    public function finalSubmit(Request $request)
    {
        return $this->matchmakerService->finalSubmit($request->all());
    }
}
