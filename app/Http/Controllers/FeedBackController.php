<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedBackController extends Controller
{
    public function feedBack(Request $request)
    {
        $request->validate([
            'query' => 'string',
            'message' => 'string',
            'firstname' => 'string',
            'lastname' => 'string',
            'telephone' => 'string',
            'email' => 'email',
        ]);

        Mail::send('contact', $request->all(), function ($mail) use ($request){
            $mail->from($request->input('email'), 'help')
                ->to('contact@matrimonyassist.com');
        });
    }
}
