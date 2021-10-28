<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\AppBaseController;

use Session;
use Token;
use App\Services\SubscriptionService;
use App\Http\Requests\Subscription\NewSubscriptionRequest;
use App\Http\Requests\Subscription\OneDaySubscriptionRequest;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;


class SubscriptionController extends AppBaseController
{
    /**
     * @var  SubscriptionService
     */
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function initializationPayment()
    {
        return $this->subscriptionService->paymentInitialization();

    }

    /**
     * @param NewSubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewSubscription(NewSubscriptionRequest $request)
    {
        return $this->subscriptionService->newSubscription($request->all());

    }

    /**
     * @param CancelSubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSubscription(CancelSubscriptionRequest $request)
    {
        return $this->subscriptionService->cancelSubscription($request->all());
    }

    /**
     * @param OneDaySubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneDaySubscription(OneDaySubscriptionRequest $request)
    {
        return $this->subscriptionService->oneDaySubscription($request->all());
    }
}
