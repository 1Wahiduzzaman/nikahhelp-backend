<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>
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
<body>
<div class="header">
    <a class="logo"><img src="{{ asset('public/images/ma_logo_white.svg') }}" alt="logo" class="mat-logo" /></a>
</div>

<div class="content-div mx-auto title-text">
    <div class="w-full">
        <h2>Verify your email address {{ $user['email'] }}</h2>

        <p>Dear [Mr./Ms.] {{ $user['full_name'] }},</p>

        <p>
            Welcome to Matrimony Assist [20-11-2021 10:40:30 +06]
        </p>

        <p>
            In order ot validate your account please click the link - <a href="{{ $domain }}/emailVerify/{{ $user->verifyUser->token }}" class="link-text">{{ $domain }}/emailVerify/{{ $user->verifyUser->token }}</a> or press the button below
        </p>

        <div class="center">
            <a role="button" class="verify-btn" href="{{ $domain }}/emailVerify/{{ $user->verifyUser->token }}">Verify my email</a>
        </div>

        <p>
            Verify email is the first step of the account validate process. For new account, after your email is verified we
            wil proceed to complete your <a class="primary-text">Candidate</a> registration form.
        </p>

        <p>
            If you do not verify your email address by sent link within <span class="font-italic font-weight-bold text-black">30 minutes</span> then the sent
            verification link & your signup informations will automatically unenrolled from Matrimony Assist platform, you can rejoin Matrimony Assist at any time by once again
            completing the Matrimony Assist signup process.
        </p>

        <p>
            You're receiving this email because you recently created a new Matrimony Assist account or added a new email address. If this wasn't you,
            please ignore this email.
        </p>

        <p class="mt-8">
            Thanks, <br>
            Regards <br>
            Matrimony Assist Team
        </p>

        <p class="center mt-8">
            This email was sent to <span class="primary-text">{{ $user['email'] }}</span>, which is
            associated with a Matrimony Assist account.
        </p>

        <p class="center mt-5">
            &copy; 2022 Matrimony Assist Inc., All Rights Reserved Matrimony Assist Inc., 55 2nd Street London, UK 94105
        </p>
    </div>
</div>

@php
    $domain=env('WEB_DOMAIN');
@endphp
</body>
</html>
