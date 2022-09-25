<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            margin: 0;
        }
        h2 {
            font-size: 15px;
        }
        p {
            font-size: 13px;
        }
        .center {
            text-align: center;
        }
        .mx-auto {
            margin: 0 auto;
        }
        .w-full {
            width: 100%;
        }
        .primary-text {
            color: #522e8e;
        }
        .font-italic {
            font-style: italic;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-black {
            color: #000000;
            opacity: 0.75;
        }
        .mt-5 {
            margin-top: 20px;
        }
        .mt-8 {
            margin-top: 32px;
        }
        .content-div {
            width: 300px;
        }
        .header {
            background: #522e8e;
            display: flex;
            justify-content: center;
        }
        .mat-logo {
            width: 170px;
            height: 110px;
        }
        .title-text {
            color: rgb(96 84 84 / 85%);
        }
        .link-text {
            color: #1d68a7;
        }
        .verify-btn {
            padding: 12px 30px;
            border-radius: 30px;
            background: #522e8e;
            border: 3px solid #FFFFFF;
            cursor: pointer;
            color: #FFFFFF;
            font-size: 20px;
            text-align: center;
            display: inline-block;
            margin: 0 auto;
            font-weight: bold;
            -webkit-transition: all 0.2s ease-in-out;
            -moz-transition: all 0.2s ease-in-out;
            -ms-transition: all 0.2s ease-in-out;
            -o-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
        }
        .verify-btn:hover {
            background: #FFFFFF;
            border: 3px solid #522e8e;
            color: #522e8e;
        }
        @media (min-width: 520px) {
            .content-div {
                width: 500px;
            }
            h2 {
                font-size: 24px;
            }
            p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body style="margin: 0;">
<div style="background: #522e8e; display: flex; justify-content: center;">
    {{-- @php
        $chobi= config('chobi.chobi').'/logo/site/ma_logo_white.svg';
    @endphp --}}
    <a><img src="{{ public_path('images/email/ma_logo_white.svg') }}" alt="logo" style="width: 170px; height: 110px;" /></a>
</div>

<div style="color: rgb(96 84 84 / 85%); margin: 0 auto; width: 500px;">
    <div style="width: 100%; margin-top: 30px;">
        <h2 style="font-size: 20px; color: rgba(0,0,0,.5);">Verify your email address</h2>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">Dear {{ $user_name }},</p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Welcome to MatrimonyAssist
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
        We are happy you signed up for MatrimonyAssist.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
        You are almost ready to start exploring the MatriomnyAssist. Before enjoiyng various features of the application, simply verify that this is your email address by clicking the link:<a href="{{ $domain }}/emailVerify/{{ $user->verifyUser->token }}" style="color: #1d68a7;">{{ $domain }}/emailVerify/{{ $user->verifyUser->token }}</a> or press purple the button below
        </p>

        <div style="text-align: center; margin-top: 28px;">
            <a role="button" style="padding: 12px 30px;
            border-radius: 30px;
            background: #522e8e;
            border: 3px solid #FFFFFF;
            cursor: pointer;
            color: #FFFFFF;
            font-size: 20px;
            text-align: center;
            display: inline-block;
            margin: 0 auto;
            font-weight: bold;" href="{{ $domain }}/emailVerify/{{ $user->verifyUser->token }}">Verify my email</a>
        </div>

        <!-- <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            If you do not verify your email address by sent link within <span style="font-style: italic; font-weight: bold; color: #000000; opacity: 0.75;">30 minutes</span> then the sent verification link & your signup
            information will automatically unenrolled from Matrimony Assist platform, you can rejoin Matrimony Assist
            at any time by once again completing the Matrimony Assist signup process.
        </p> -->

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            This link is valid for <span style="font-style: italic; font-weight: bold; color: #000000; opacity: 0.75;">15 minutes.</span> After the email verification, you will have the access to complete the full registration form. </p>
        <!-- <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Verify email is the first step of the account validate process. After your email is verified we will
            proceed to complete your <a style="color: #522e8e;">Candidate</a> registration form.
        </p> -->

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
        You are receiving this email because you have signed for a new MatrimonyAssist account or added a new email address to your account.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
        If you did not initiate this request, please contact us immediately at support@matrimonyassist.com.
        </p>
        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px">
        Thank you <br>
        MatrimonyAssist Team
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px; text-align: center;">
            This email was sent to <span style="color: #522e8e;">{{ $user['email'] }}</span>, which is
            associated with a Matrimony Assist account.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 20px; text-align: center;">
            &copy; 2022 Matrimony Assist. All Rights Reserved Matrimony Assist.
        </p>
    </div>
</div>

@php
    $domain=env('WEB_DOMAIN');
@endphp
</body>
</html>
