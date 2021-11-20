<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>
<body>
<h2>Verify your email address {{$user['full_name']}}</h2>
<br/>
Your registered email-id is {{$user['email']}} , Please click on the below link to verify your email account
<br/>
@php
    $domain=env('WEB_DOMAIN');
@endphp
<a href="{{$domain}}/emailVerify/{{$user->verifyUser->token}}">Verify Email</a>
<!-- <a href="{{$domain}}/emailVerify/{{$user->verifyUser->token}}">Verify Email</a> -->
</body>
</html>
