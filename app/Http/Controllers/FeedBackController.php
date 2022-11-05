<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedBackController extends Controller
{
    public function feedBack(Request $request)
    {
        try {
            $request->validate([
                'query' => 'string',
                'message' => 'string',
                'firstname' => 'string',
                'lastname' => 'string',
                'telephone' => 'string',
                'email' => 'email',
            ]);

            Mail::send('emails.contact', $request->all(), function ($mail) use ($request){
                $mail->from($request->input('email'), 'help')
                    ->to('thesyed.london@gmail.com');
            });

            return $this->sendSuccessResponse('Sent successfully', HttpStatusCode::SUCCESS);
        } catch (\Exception $exception)
        {
            $this->sendSuccessResponse($exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }

    }
}
