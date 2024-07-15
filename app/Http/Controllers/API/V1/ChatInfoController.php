<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\ChatInfoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatInfoController extends Controller
{
    /**
     * @var ChatInfoService
     */
    public $chatInfoService;

    public function __construct(ChatInfoService $chatInfoService)
    {
        $this->chatInfoService = $chatInfoService;
    }

    /**
     * Return information for chat integration.
     *
     * @return JsonResponse
     */
    public function getInfo(Request $request)
    {
        return $this->chatInfoService->getInfo($request);
    }

    /**
     * Return information for chat integration.
     *
     * @return JsonResponse
     */
    public function getUserInfoList(Request $request)
    {
        return $this->chatInfoService->getUserInfoList($request);
    }
}
