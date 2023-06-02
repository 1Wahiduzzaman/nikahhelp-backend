<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plans;
use App\Models\Team;
use App\Repositories\TeamRepository;
use Stripe;
use Session;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;
use App\Http\Resources\SubscriptionReportResource;
use App\Mail\SubscriptionMail;
use Illuminate\Support\Facades\Mail;
use \App\Domain;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\SubscriptionExpiringMail;
use App\Mail\SubscriptionNewMail;

class SubscriptionService extends ApiBaseService
{
    const SUBSCRIPTION_SUCCESSFULLY = "Team Subscription Successfully complete";
    const INITIALIZATION_SUCCESSFULLY = "Payment Initialization Successfully complete";

    protected \App\Repositories\TeamRepository $teamRepository;
    protected \App\Domain $domain;

    /**
     * TeamService constructor.
     *
     * @param TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository, Domain $domain)
    {
        $this->teamRepository = $teamRepository;
        $this->domain = $domain;
    }

    /**
     * @return JsonResponse
     */
    public function paymentInitialization()
    {
        try {
            $userId = self::getUserId();
            $user = User::find($userId);
            if (!$user) {
                return $this->sendErrorResponse('User not found', [], HttpStatusCode::BAD_REQUEST);
            }
            $user->createAsStripeCustomer();

            $intent = $user->createSetupIntent();
            $data = [
                'client_secret' => $intent->client_secret
            ];
            return $this->sendSuccessResponse($data, self::INITIALIZATION_SUCCESSFULLY, [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function newSubscription($request)
    {
        try {
            $userId = self::getUserId();
            $user = User::find($userId);
            if (!$user) {
                return $this->sendErrorResponse('User not found', [], HttpStatusCode::BAD_REQUEST);
            }
            $stripeToken = $request['stripeToken'];
            $packageID = Self::selectPlaneID(5);
            $packageName = Self::selectPlaneName(5).date('YmdHis');
            $user->addPaymentMethod($stripeToken);

            $paymentMethod = $user->findPaymentMethod($stripeToken);
            $subscriptionInfo = $user->newSubscription("$packageName", "$packageID")->create($paymentMethod->id, [
                'name' => $user->full_name,
                'email' => $user->email
            ]);
            $expireDate = Carbon::parse(self::expireDateCalculation(4))->format('Y-m-d');
            $suBInfo = Subscription::find($subscriptionInfo->id);
            $suBInfo->team_id = $request['team_id'];  //pk
            $suBInfo->plan_id = $request['plane'];
            $suBInfo->subscription_expire_at = $expireDate;
            $suBInfo->save();
            $teamExpireDateUpdate = Team::with(['created_by'])->find($request['team_id']);   //pk team_id
            $current_expire_date  = $teamExpireDateUpdate->subscription_expire_at;
            if($current_expire_date) {
                $exp_date = Carbon::parse($this->reNewExpiryDate(4, $current_expire_date))->format('Y-m-d');
            } else {
                $exp_date = $expireDate;
            }
            $teamExpireDateUpdate->subscription_expire_at = $exp_date;
            $teamExpireDateUpdate->subscription_id = $subscriptionInfo->id;
            $teamExpireDateUpdate->save();

            // Send Mail to subscribed user
            try{
                if($user->email) {
                    $subscription = $teamExpireDateUpdate->subscription;
                    Mail::to($user->email)->send(new SubscriptionNewMail($teamExpireDateUpdate, $subscription, $this->domain->domain));
                }
            } catch (IncompletePayment $exception) {
                return $this->sendErrorResponse('Subscription mail has been filled');
            }
            //
            Notificationhelpers::add(self::SUBSCRIPTION_SUCCESSFULLY, 'team', $request['team_id'], $userId);

            return $this->sendSuccessResponse($subscriptionInfo->toArray(), self::SUBSCRIPTION_SUCCESSFULLY);
        } catch (IncompletePayment $exception) {
            return $this->sendErrorResponse('Subscription payment has been filled');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }

    }

    private function reNewExpiryDate($plan = null, $date = null) {
        $date = Carbon::createFromFormat('Y-m-d', $date);
        switch ($plan) {
            case 0:
                return $date->addDays(1);
                break;
            case 1:
                return $date->addDays(30);
                break;
            case 2:
                return $date->addDays(90);
                break;
            case 3:
                return $date->addDays(180);
                break;
            case 4:
                return $date->addDays(365);
                break;
            default:
                return $date->addDays(1);
                break;

        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function oneDaySubscription($request)
    {
        $userId = self::getUserId();
        try {
            $teamExpireDateUpdate = Team::find($request['team_id']);
            if (!$teamExpireDateUpdate) {
                return $this->sendErrorResponse('Team not found', [], HttpStatusCode::BAD_REQUEST);
            }

//            $userId = self::getUserId();
//            $user = User::find($userId);
//            $stripeToken = $request['stripeToken'];
//            $packageID = Self::selectPlaneID($request['plane']);
//            $packageName = Self::selectPlaneName($request['plane']);
//            $user->createOrGetStripeCustomer();
//            $user->updateDefaultPaymentMethod($stripeToken);
//            $subscriptionInfo = $user->charge(1 * 100, $stripeToken);


            $teamExpireDateUpdate->subscription_expire_at = Carbon::parse(self::expireDateCalculation($request['plane']))->format('Y-m-d');;
            $teamExpireDateUpdate->save();
            Notificationhelpers::add(self::SUBSCRIPTION_SUCCESSFULLY, 'team', $request['team_id'], $userId);
//            if ($subscriptionInfo->status == "succeeded"):
            return $this->sendSuccessResponse($teamExpireDateUpdate->toArray(), self::SUBSCRIPTION_SUCCESSFULLY);
//            else:
//                return $this->sendErrorResponse('something went wrong please try again', [], HttpStatusCode::BAD_REQUEST);
//            endif;
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $type
     * @return mixed
     */
    public function selectPlaneID($type)
    {
        switch ($type) {
            case 0:
                return env('PLANE_ONE_DAY', 'PLANE_ONE_MONTH');
                break;
            case 1:
                return env('PLANE_ONE_MONTH', 'PLANE_ONE_MONTH');
                break;
            case 2:
                return env('PLANE_THREE_MONTH', 'PLANE_ONE_MONTH');
                break;
            case 3:
                return env('PLANE_SIX_MONTH', 'PLANE_ONE_MONTH');
                break;
            case 4:
                return env('PLANE_FIX');
                break;
            case 5:
                return env('PLANE_PROMO_OFFER');
                break;
            default:
                return env('PLANE_ONE_DAY', 'PLANE_ONE_DAY');
                break;

        }

    }

    /**
     * @param $type
     * @return string
     */
    public function selectPlaneName($type)
    {
        switch ($type) {
            case 0:
                return 'PLANE_ONE_DAY';
                break;
            case 1:
                return 'PLANE_ONE_MONTH';
                break;
            case 2:
                return 'PLANE_THREE_MONTH';
                break;
            case 3:
                return 'PLANE_SIX_MONTH';
                break;
            case 4:
                return 'PLANE_ONE_YEAR';
                break;
            case 5:
                return 'PLANE_PROMO_OFFER';
                break;
            default:
                return 'PLANE_ONE_DAY_FREE';
                break;

        }

    }

    /**
     * @param $type
     * @return Carbon
     */
    public function expireDateCalculation($type)
    {

        switch ($type) {
            case 0:
                return Carbon::now()->addDays(1);
                break;
            case 1:
                return Carbon::now()->addDays(30);
                break;
            case 2:
                return Carbon::now()->addDays(90);
                break;
            case 3:
                return Carbon::now()->addDays(180);
                break;
            case 4:
                return Carbon::now()->addDays(365);
                break;
            default:
                return Carbon::now()->addDays(1);
                break;

        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function cancelSubscription($request)
    {
        try {
            $findSubscription = Subscription::find($request['subscription_id']);
            if (!$findSubscription) {
                return $this->sendErrorResponse('Team subscription not found', [], HttpStatusCode::BAD_REQUEST);
            }
            $userId = self::getUserId();
            $user = User::find($userId);
            if (!$user) {
                return $this->sendErrorResponse('User not found', [], HttpStatusCode::BAD_REQUEST);
            }
            $subscriptionCancelInfo = $user->subscription($findSubscription->name)->cancelNowAndInvoice();
//            $subscriptionCancelInfo = $user->subscription($findSubscription->name)->cancelNow();
//            $subscriptionCancelInfo = $user->subscription($findSubscription->name)->cancel();
            $teamExpireDateUpdate = Team::find($findSubscription->team_id);
            $teamExpireDateUpdate->subscription_expire_at = Carbon::now()->format('Y-m-d');;
            $teamExpireDateUpdate->status = 2;
            $teamExpireDateUpdate->save();
            if ($subscriptionCancelInfo):
                Notificationhelpers::add("Subscription has been canceled", 'team', $findSubscription->team_id, $userId);
                return $this->sendSuccessResponse($subscriptionCancelInfo->toArray(), self::SUBSCRIPTION_SUCCESSFULLY);
            else:
                return $this->sendErrorResponse('Something went wrong please try again', [], HttpStatusCode::BAD_REQUEST);
            endif;
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function subscriptionReport($request)
    {
        if(!Gate::allows('GET_TEAM_SUBSCRIPTION_DATA')){
            return $this->sendUnauthorizedResponse();
        }

        $page = $request['page'] ?: 1;
        $parpage = $request['parpage'] ?: 10;
        $teamInformation = $this->teamRepository->getModel()->newQuery();
        if ($page) {
            $skip = $parpage * ($page - 1);
            $queryData = $teamInformation->limit($parpage)->offset($skip)->get();
        } else {
            $queryData = $teamInformation->limit($parpage)->offset(0)->get();
        }

        $PaginationCalculation = $teamInformation->paginate($parpage);
        $team_info = SubscriptionReportResource::collection($queryData);
        $result['result'] = $team_info;
        $result['pagination'] = self::pagination($PaginationCalculation);
        return $this->sendSuccessResponse($result, 'Team subscription information fetched Successfully');

    }


    /**
     * @param $queryData
     * @return array
     */
    protected function pagination($queryData)
    {
        $data = [
            'total_items' => $queryData->total(),
            'current_items' => $queryData->count(),
            'first_item' => $queryData->firstItem(),
            'last_item' => $queryData->lastItem(),
            'current_page' => $queryData->currentPage(),
            'last_page' => $queryData->lastPage(),
            'has_more_pages' => $queryData->hasMorePages(),
        ];
        return $data;
    }


    //Subscription Cron Job
    public function subscriptionExpiring($teams) {
        foreach($teams as $team){
            if(!$team->team_members->isEmpty()) {
                foreach($team->team_members as $member) {
                    $user = $member->user;
                    if($user->email) {
                        Mail::to($user->email)->send(new SubscriptionExpiringMail($team, $user, $this->domain->domain));
                    }
                }
            }
        }
        echo 'Mail Sent';
    }

    public function subscriptionExpired($teams) {
        foreach($teams as $team){
            if(!$team->team_members->isEmpty()) {
                foreach($team->team_members as $member) {
                    //$user = $member->user;
                    if($member->user->email) {
                        Mail::to($member->user->email)->send(new SubscriptionExpiredMail($team, $member, $this->domain->domain));
                    }
                }
            }
        }
        echo 'Mail Sent';
    }

}
