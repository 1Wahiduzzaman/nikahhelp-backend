<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Mail\ContactEmail;
use App\Models\User;
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

            $email = 'thesyed.london@gmail.com';
            $user = (object)[
                'email' => $email,
                'name' => substr($email, 0, strpos($email, '@')), // here we take the name form email (string before "@")
            ];

            Mail::to($user->email)->send(new ContactEmail($request->all()));

            return $this->sendSuccessResponse('Sent successfully', HttpStatusCode::SUCCESS);
        } catch (\Exception $exception)
        {
            $this->sendSuccessResponse($exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }

    }
}
