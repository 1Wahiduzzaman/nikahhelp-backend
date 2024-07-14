<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;
use App\Http\Requests\Subscription\NewSubscriptionRequest;
use App\Http\Requests\Subscription\OneDaySubscriptionRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;

class SubscriptionController extends AppBaseController
{
    private \App\Services\SubscriptionService $subscriptionService;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewSubscription(NewSubscriptionRequest $request)
    {

        return $this->subscriptionService->newSubscription($request->all());

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSubscription(CancelSubscriptionRequest $request)
    {
        return $this->subscriptionService->cancelSubscription($request->all());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneDaySubscription(OneDaySubscriptionRequest $request)
    {
        return $this->subscriptionService->oneDaySubscription($request->all());
    }

    //Cron job
    public function subscriptionExpiring($days = 7)
    {
        $date = Carbon::today()->addDay($days);
        $teams = Team::with(['team_members' => function ($q) {
            $q->with(['user' => function ($u) {
                $u->select('id', 'full_name', 'email', 'status');
            }]);
            $q->where('status', 1);
        }])
            ->where('subscription_expire_at', $date)->get();

        // $users = User::where('email', 'raz.doict@gmail.com')->get();
        return $this->subscriptionService->subscriptionExpiring($teams);
    }

    public function subscriptionExpired($days = 1)
    {
        $date = Carbon::today()->subDay($days);
        $teams = Team::with(['team_members' => function ($q) {
            $q->with(['user' => function ($u) {
                $u->select('id', 'full_name', 'email', 'status');
            }]);
            $q->where('status', 1);
        }])
            ->with(['created_by' => function ($u) {
                $u->select('id', 'full_name', 'email', 'status');
            }])
            ->where('subscription_expire_at', $date)->get();

        return $this->subscriptionService->subscriptionExpired($teams);
    }
}
