<!DOCTYPE html>
<html>
<head>
    <title>Forget Password Mail</title>
</head>
<body>
<h2>Welcome to the site {{$user['name']}}</h2>
<br/>
Your registered email-id is {{$user['email']}} , Please click on the below link to reset account password
<br/>
@php
    $domain=env('WEB_DOMAIN');
@endphp
<a href="{{$domain}}/v1/password-reset-token-verification/{{$token}}">reset password</a>
</body>
</html>
