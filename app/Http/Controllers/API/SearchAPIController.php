<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Search\CreateSearchAPIRequest as APICreateSearchAPIRequest;
use App\Repositories\SearchRepository;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Response;

/**
 * Class SearchController
 */
class SearchAPIController extends AppBaseController
{
    private \App\Repositories\SearchRepository $searchRepository;

    private \App\Services\SearchService $searchService;

    public function __construct(SearchRepository $searchRepository, SearchService $searchService)
    {
        $this->searchRepository = $searchRepository;
        $this->searchService = $searchService;
    }

    /**
     * @param  Request  $request
     * @param  User  $user
     */
    public function filter(APICreateSearchAPIRequest $request)
    {
        return $this->searchService->filter($request);
    }

    /**
     * Display a listing of the Search.
     * GET|HEAD /searches
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $searches = $this->searchRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($searches->toArray(), 'Searches retrieved successfully');
    }
}
