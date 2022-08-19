<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketSubmissionRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class SubmitTicketController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  TicketSubmissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UserService $matrimonyUsers, TicketSubmissionRequest $request)
    {
       return $matrimonyUsers->ticketSubmission($request);
    }
}
