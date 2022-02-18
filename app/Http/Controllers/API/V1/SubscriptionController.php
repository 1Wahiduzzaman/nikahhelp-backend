<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\AppBaseController;

use Session;
use Token;
use App\Services\SubscriptionService;
use App\Http\Requests\Subscription\NewSubscriptionRequest;
use App\Http\Requests\Subscription\OneDaySubscriptionRequest;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;

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
    public function createNewSubscription(NewSubscriptionRequest $request) {        
        
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

    //Cron job
    public function subscriptionExpire() {
        $date = Carbon::today()->addDay(7);
        // dd($date);
        $users = Team::with(['team_members' => function($q) {
            $q->with(['user' => function($u){
                $u->select('id', 'full_name', 'email', 'status');
            }]);
            // $q->with('user');
            $q->where('status', 1);
        }])
        ->where('subscription_expire_at', $date)->first();
        dd($users->team_members[0]->user);
        // $users = User::where('email', 'raz.doict@gmail.com')->get();        
        return $this->subscriptionService->subscriptionExpire($users);
    }
}
