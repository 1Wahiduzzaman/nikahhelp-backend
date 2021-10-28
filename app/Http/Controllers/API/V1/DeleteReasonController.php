<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\DeleteReason;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\DeleteReasonService;
use App\Http\Requests\DeleteReasonSubmitRequest;

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
     * @param DeleteReasonSubmitRequest $request
     * @return JsonResponse
     */
    public function store(DeleteReasonSubmitRequest $request)
    {
        return $this->deleteReasonService->save($request);
    }

}
