<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\BlockList\CreateBlockListAPIRequest;
use App\Http\Resources\BlockListResource;
use App\Repositories\BlockListRepository;
use App\Repositories\CandidateRepository;
use App\Services\BlockListService;
use App\Transformers\CandidateTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Response;
use Symfony\Component\HttpFoundation\Response as FResponse;

/**
 * Class block_listController
 * @package App\Http\Controllers\API
 */
class BlockListAPIController extends AppBaseController
{
    /**
     * @var  BlockListRepository
     */
    private $blockListRepository;

    /**
     * @var  BlockListService
     */
    private $blockListService;
    /**
     * @var CandidateRepository
     */
    private $candidateRepository;

    public function __construct(
        BlockListRepository $blockListRepo,
        BlockListService $blockListService,
        CandidateRepository $candidateRepository
    )
    {
        $this->blockListRepository = $blockListRepo;
        $this->blockListService = $blockListService;
        $this->candidateRepository = $candidateRepository;
    }

    /**
     * Display a listing of the block_list.
     * GET|HEAD /blockLists
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId();
        $blockLists = $this->blockListRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        )->where('block_by', '=', $userId);
        $formatted_data = BlockListResource::collection($blockLists);
        return $this->sendResponse($formatted_data, 'Block Lists retrieved successfully');
    }

    /**
     * Store a newly created block_list in storage.
     * POST /blockLists
     *
     * @param CreateBlockListAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBlockListAPIRequest $request)
    {
        return $blockList = $this->blockListService->store($request->all());
    }

    /**
     * Display the specified block_list.
     * GET|HEAD /blockLists/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var block_list $blockList */
        $blockList = $this->blockListRepository->find($id);

        if (empty($blockList)) {
            return $this->sendError('Block List not found');
        }

        return $this->sendResponse($blockList->toArray(), 'Block List retrieved successfully');
    }


    /**
     * Remove the specified block_list from storage.
     * DELETE /blockLists/{id}
     *
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function destroy($id)
    {
        /** @var block_list $blockList */
        $blockList = $this->blockListRepository->findOrFail($id);

        if (empty($blockList)) {
            return $this->sendError('Block List not found');
        }
        $blockList->delete();

        return $this->sendResponse([], 'Candidate Un-Block successful', FResponse::HTTP_OK);
    }

    public function destroyByCandidate(Request $request)
    {
        $userId = self::getUserId();

        try {
            $candidate = $this->candidateRepository->findOneByProperties([
                'user_id' => $userId
            ]);

            if (!$candidate) {
                throw (new ModelNotFoundException)->setModel(get_class($this->candidateRepository->getModel()), $userId);
            }



            $candidate->blockList()->detach($request->user_id);

            return $this->sendSuccessResponse([], 'Candidate Un-Block successfully!', [], HttpStatusCode::CREATED);

        }catch (\Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
