<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketSubmissionRequest;
use App\Http\Requests\TicketSumbissionScreenshot;
use App\Models\CandidateInformation;
use App\Models\RepresentativeInformation;
use App\Services\CandidateService;
use App\Services\RepresentativeService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmitTicketController extends Controller
{
    public $representative;

    public $candidate;

    public $matrimonyUsers;

    public function __construct(UserService $matrimonyUsers, CandidateService $candidate, RepresentativeService $representative)
    {
        $this->candidate = $candidate;
        $this->representative = $representative;
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

    public function allTicket(Request $request, $id)
    {
        if ($this->candidate->getUserId() == $id) {
            return $this->candidate->allTickets($request, $id);
        }

        return $this->representative->allTickets($request, $id);
    }

    public function resolveTicket(Request $request)
    {
        return $this->matrimonyUsers->resolveTicket($request);
    }

    public function sendTicketMessage(Request $request)
    {
        return $this->matrimonyUsers->sendMessage($request);
    }
}
