<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Helpers\Notificationhelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

class SubscriptionService extends ApiBaseService
{
    const SUBSCRIPTION_SUCCESSFULLY = "Team Subscription Successfully complete";
    const INITIALIZATION_SUCCESSFULLY = "Payment Initialization Successfully complete";

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * TeamService constructor.
     *
     * @param TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;

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
            $intent = $user->createSetupIntent();
            $data = [
                'client_secret' => $intent->client_secret,
                'stripe_key' => env('STRIPE_KEY'),
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
            $packageID = Self::selectPlaneID($request['plane']);
            $packageName = Self::selectPlaneName($request['plane']) . date('YmdHis');

            $subscriptionInfo = $user->newSubscription("$packageName", "$packageID")->create($stripeToken, [
                'name' => $user->full_name,
                'email' => $user->email
            ]);
            $expireDate = Carbon::parse(self::expireDateCalculation($request['plane']))->format('Y-m-d');
            $suBInfo = Subscription::find($subscriptionInfo->id);
            $suBInfo->team_id = $request['team_id'];
            $suBInfo->plan_id = $request['plane'];
            $suBInfo->subscription_expire_at = $expireDate;
            $suBInfo->save();
            $teamExpireDateUpdate = Team::find($request['team_id']);
            $teamExpireDateUpdate->subscription_expire_at = $expireDate;
            $teamExpireDateUpdate->subscription_id = $subscriptionInfo->id;
            $teamExpireDateUpdate->save();

            Notificationhelpers::add(self::SUBSCRIPTION_SUCCESSFULLY, 'team', $request['team_id'], $userId);

            return $this->sendSuccessResponse($subscriptionInfo->toArray(), self::SUBSCRIPTION_SUCCESSFULLY);
        } catch (IncompletePayment $exception) {
            return $this->sendErrorResponse('Subscription payment has been filled');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
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

}
