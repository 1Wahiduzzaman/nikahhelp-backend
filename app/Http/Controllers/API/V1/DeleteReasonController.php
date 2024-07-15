<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteReasonSubmitRequest;
use App\Services\DeleteReasonService;
use Illuminate\Http\JsonResponse;

class DeleteReasonController extends Controller
{
    /**
     * @var DeleteReasonService
     */
    public $deleteReasonService;

    public function __construct(DeleteReasonService $deleteReasonService)
    {
        $this->deleteReasonService = $deleteReasonService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(DeleteReasonSubmitRequest $request)
    {
        return $this->deleteReasonService->save($request);
    }
}
