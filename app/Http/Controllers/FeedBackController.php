<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Mail\ContactEmail;
use App\Models\User;
use Exception;
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

            $email = 'khorramk.kbsk@gmail.com';
            $user = (object)[
                'email' => $email,
                'name' => substr($email, 0, strpos($email, '@')), // here we take the name form email (string before "@")
            ];

            $data = [
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'telephone' => $request->input('telephone'),
                'email' => $request->input('email'),
                'message' => $request->input('message'),
                'query' => $request->input('query')
            ];

            try {
                Mail::to($user->email)->send(new ContactEmail($data));
            } catch(Exception $exception) {
                return $this->sendSuccessResponse($exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
            }

            return $this->sendSuccessResponse('Sent successfully', HttpStatusCode::SUCCESS);
        } catch (\Exception $exception)
        {
            $this->sendSuccessResponse($exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }

    }
}
