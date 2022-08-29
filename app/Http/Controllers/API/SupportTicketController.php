<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupportTicketController extends AppBaseController
{
    public $matrimonyUsers;

    public function __construct(UserService $matrimonyUsers)
    {
        return $this->matrimonyUsers = $matrimonyUsers;
    }

    public  function getALlTicket(Request $request)
    {
        if (!Gate::allows('CAN_ACCESS_SUPPORT')) {
            return $this->sendUnauthorizedResponse();
        }
        return $this->matrimonyUsers->allTickets($request);
    }

    public function getUserTickets(Request $request, $id)
    {
        if (!Gate::allows('CAN_ACCESS_SUPPORT')) {
            return $this->sendUnauthorizedResponse();
        }
        return $this->matrimonyUsers->userTickets($request, $id);
    }

    public function getTicket($id)
    {
        if (!Gate::allows('CAN_ACCESS_SUPPORT')) {
            return $this->sendUnauthorizedResponse();
        }
        return $this->matrimonyUsers->singleTicket($id);
    }

    public function saveRequest(Request $request)
    {
        if (!Gate::allows('CAN_ACCESS_SUPPORT')) {
            return $this->sendUnauthorizedResponse();
        }

        return $this->matrimonyUsers->saveRequest($request);
    }

    public function getTicketMessages(Request $request, $id)
    {
        if (!Gate::allows('CAN_ACCESS_SUPPORT')) {
            return $this->sendUnauthorizedResponse();
        }

        return $this->matrimonyUsers->ticketMessages($request, $id);
    }
}
