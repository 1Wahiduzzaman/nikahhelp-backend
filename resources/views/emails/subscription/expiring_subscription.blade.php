<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Email</title>
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
    <a><img src="{{ asset(config('chobi.chobi').'/logo/site/ma_logo_white.svg') }}" alt="logo" style="width: 170px; height: 110px;" /></a>
</div>

<div style="color: rgb(96 84 84 / 85%); margin: 0 auto; width: 500px;">
    @php
        $domain=env('WEB_DOMAIN');
    @endphp
    <div style="width: 100%; margin-top: 30px;">
        <h2 style="font-size: 28px; color: rgba(0,0,0,.5); text-align: center">Subscription expiring at {{ Carbon::parse($team->subscription_expire_at)->format('d M Y') }}</h2>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">Dear {{ @$user->full_name }},</p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Thank you for using MatrimonyAssist [{{ Carbon::parse($team->subscription_expire_at)->format('d M Y') }}]
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            This is a gentle reminder.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Your subscription for the - <span style="color: #522e8e;">{{ @$team->name }}</span> is expiring on <span style="color: #522e8e">{{ Carbon::parse($team->subscription_expire_at)->format('d M Y') }}</span>
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            All team members are receiving this email, Anyone from the team can take up subscription on
            behalf of their team.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            To continue using MatrimonyAssist please renew your subscription by clicking (tapping):
        </p>

        <div style="text-align: center; margin-top: 28px;">
            <a role="button" style="padding: 12px 30px;
            border-radius: 30px;
            background: #5eee5e;
            border: 3px solid #FFFFFF;
            cursor: pointer;
            color: #FFFFFF;
            font-size: 20px;
            text-align: center;
            display: inline-block;
            margin: 0 auto;
            text-decoration: none;
            font-weight: bold;" href="">Renew Subscription</a>
        </div>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px; text-align: center">
            <a style="color: #522e8e;" href="">Link</a>
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            If you no longer wish to continue please ignore this email and your subscription will end on
            {{ Carbon::parse($team->subscription_expire_at)->format('d M Y') }}. We are grateful to you for using MatrimonyAssist.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            If we have not been able to meet your expectations and you are cancelling because of our
            shortcomings, we would like to hear from you so that we can raise our game.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            We would love to hear from you if you have any suggestions for improving MatrionyAssist for
            others, to help them find a match for themselves and their loved ones more easily. You can give
            us your feedback by using the contact us link below.
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
            text-decoration: none;
            font-weight: bold;" href="">Contact Us</a>
        </div>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px; text-align: center">
            <a style="color: #522e8e; text-align: center" href="{{ $domain }}/subscription/{{ $team->team_id }}">{{ $domain }}/subscription/{{ $team->team_id }}</a>
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 28px;">
            Feedback tips. If you can please include the following details: what is the issue specifically, what
            happened, why is it a good or bad thing, what could change and how to improve things. You
            can attach screenshots of the matter you are concerned or happy about to illustrate your
            point.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px">
            Thanks, <br>
            Regards <br>
            Matrimony Assist Team
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 32px; text-align: center;">
            This email was sent to <span style="color: #522e8e;">{{ $user->email }}</span>, which is
            associated with a Matrimony Assist account.
        </p>

        <p style="font-size: 16px; color: rgba(0,0,0,.5); margin-top: 20px; text-align: center;">
            &copy; 2022 MatrimonyAssist. All Rights Reserved MatrimonyAssist.
        </p>
    </div>
</div>
</body>
</html>
