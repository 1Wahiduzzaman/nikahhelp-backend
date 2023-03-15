<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationAPIRequest;
use App\Http\Requests\API\UpdateNotificationAPIRequest;
use App\Models\Notification;
use App\Models\TeamMember;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use App\Http\Controllers\AppBaseController;
use App\Helpers\Notificationhelpers;
use Response;
use Symfony\Component\Console\Input\Input;

/**
 * Class NotificationController
 * @package App\Http\Controllers\API
 */
class NotificationAPIController extends AppBaseController
{
    private \App\Repositories\NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Notification.
     * GET|HEAD /notifications
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId();
        $parpage = 10;
        $shortBy = isset($request->short_by) ? $request->short_by : 'All';

        if (!empty($request->input('parpage'))) {
            $parpage = $request->input('parpage');
        }
        $teamList = TeamMember::where(TeamMember::USER_ID, '=', $userId)->groupBy(TeamMember::TEAM_ID)->pluck(TeamMember::TEAM_ID);
        if (!empty($teamList) && count($teamList) > 0 && ($shortBy == 'All' or $shortBy == 'team')):
            $userNotification = Notification::Where(TeamMember::USER_ID, '=', $userId)->orWhereIn('team_id', [$teamList])->orderBy('created_at', 'desc')->paginate($parpage);
//          $userNotification = Notification::whereIn('team_id', [$teamList])->OrWhere(TeamMember::USER_ID, '=', $userId)->paginate($parpage);
        else:
            $userNotification = Notification::Where('user_id', '=', $userId)->orderBy('created_at', 'desc')->paginate($parpage);
        endif;
        $formatted_data['data'] = NotificationResource::collection($userNotification);
        $formatted_data['pagination'] = $this->paginationResponse($userNotification);

        return $this->sendResponse($formatted_data, 'Notifications retrieved successfully');

    }


    /**
     * Store a newly created Notification in storage.
     * POST /notifications
     *
     * @param CreateNotificationAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateNotificationAPIRequest $request)
    {
        $input = $request->all();

        $notification = $this->notificationRepository->create($input);

        return $this->sendResponse($notification->toArray(), 'Notification saved successfully');
    }

    /**
     * Display the specified Notification.
     * GET|HEAD /notifications/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError('Notification not found');
        }

        return $this->sendResponse($notification->toArray(), 'Notification retrieved successfully');
    }


    /**
     * Remove the specified Notification from storage.
     * DELETE /notifications/{id}
     *
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function destroy($id)
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError('Notification not found');
        }

        $notification->delete();

        return $this->sendSuccess('Notification deleted successfully');
    }
}
