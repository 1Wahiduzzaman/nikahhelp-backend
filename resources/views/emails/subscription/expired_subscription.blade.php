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
        use Carbon\Carbon;
    @endphp
    <a href="{{ $domain }}"><img src="{{ $chobi }}" alt="logo" style="text-align: center; margin: auto" /></a>
</div>

<div style="color: rgb(96 84 84 / 85%); margin: 0 auto; width: 500px;">
    <div style="width: 100%; margin-top: 30px;">
        <h2 style="font-size: 28px; color: rgba(0,0,0,.5); text-align: center">Subscription expired {{ Carbon::parse(@$team->subscription_expire_at)->format('d M Y') }}</h2>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">Dear {{ $user->user->full_name }},</p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Thank you for using MatrimonyAssist
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Further to our previous reminders, please note that your subscription for the team- <span style="color: #522e8e;">{{ @$team->name }}</span> has now
			<span style="color: #522e8e;">{{ Carbon::parse($team->subscription_expire_at)->format('d M Y') }}</span>
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            The owner of this team is <span style="color: #522e8e;">{{ $team->created_by()->first()->full_name }}</span>. All team members will receive a notification of this
			renewal in MatrimonyAssist
        </p>

        <div style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Help and guidance on how to subscribe and subscription detail. <a href="{{ $domain.'/help' }}">{{ $domain.'/help' }}</a>
        </div>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Please note this is an automated email. Please do not reply as the email will not reach MatrimonyAssist Team
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px">
            Thanks, <br>
            Regards <br>
            MatrimonyAssist Team
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px; text-align: center;">
            This email was sent to <span style="color: #522e8e;">{{ $user->email }}</span>, which is
            associated with a MatrimonyAssist account.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 20px; text-align: center;">
            &copy;{{ date("Y") }} MatrimonyAssist. All Rights Reserved MatrimonyAssist.
        </p>
    </div>
</div>
</body>
</html>
