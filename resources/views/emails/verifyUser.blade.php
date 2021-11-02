<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>
<body>
<h2>Welcome to the site {{$user['name']}}</h2>
<br/>
Your registered email-id is {{$user['email']}} , Please click on the below link to verify your email account
<br/>
@php
    $domain=env('WEB_DOMAIN');
@endphp
<a href="{{$domain}}/v1/emailVerify/{{$user->verifyUser->token}}">Verify Email</a>
<!-- <a href="{{$domain}}/emailVerify/{{$user->verifyUser->token}}">Verify Email</a> -->
</body>
</html>
