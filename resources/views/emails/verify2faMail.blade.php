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
    @php
        $main_domain=env('MAIN_DOMAIN');
        $domain=env('WEB_DOMAIN');
        $chobi= $main_domain.'/logo';
    @endphp
    <a href="{{ $domain }}"><img src="{{ $chobi }}" alt="logo" style="text-align: center; margin: auto" /></a>
</div>

<div style="color: rgb(96 84 84 / 85%); margin: 0 auto; width: 500px;">
    <div style="width: 100%; margin-top: 30px;">
        <h2 style="font-size: 20px; color: rgba(0,0,0,.5);">MatrimonyAssist Verification code</h2>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">Dear {{ $user_name }},</p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px">We received a request to access your MatrimonyAssist Account {{ $user['email'] }} through your email address. Your verification code is:</p>

        <h2 style="text-align: center; font-size:20px;"><b>{{ $user->two_factor_code }}</b></h2>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px">This code is valid for 10 minutes. If you did not request this code, it is possible that someone else is trying to access the MatrimonyAssist Account {{ $user['email'] }}. Do not forward or give this code to anyone.</p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px">
            Thank you <br>
            MatrimonyAssist Team
        </p>


        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px; text-align: center;">
            This email was sent to <span style="color: #522e8e;">{{ $user['email'] }}</span>, which is
            associated with a MatrimonyAssist account.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 20px; text-align: center;">
            &copy;{{date('Y')}} MatrimonyAssist. All Rights Reserved MatrimonyAssist.
        </p>
    </div>
</div>

@php
    $domain=env('WEB_DOMAIN');
@endphp
</body>
</html>
