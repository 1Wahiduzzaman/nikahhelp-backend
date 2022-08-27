<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketSubmissionRequest;
use App\Http\Requests\TicketSumbissionScreenshot;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmitTicketController extends Controller
{

    public $matrimonyUsers;

    public function __construct(UserService $matrimonyUsers)
    {
        $this->matrimonyUsers = $matrimonyUsers;
    }
    /**
     * Handle the incoming request.
     *
     * @param  TicketSubmissionRequest  $request
     */
    public function submitTicket(TicketSubmissionRequest $request)
    {
       return $this->matrimonyUsers->ticketSubmission($request);
    }

    public function screenShot(TicketSumbissionScreenshot $request)
    {
        return $this->matrimonyUsers->issueScreenShot($request);
    }

    public function getAllTicket(Request $request)
    {
        return $this->matrimonyUsers->allTickets($request);
    }
}
