<html>
<head>

</head>
<body>
    <h1>{{ $data->firstname }} has requested help</h1>
    <p>please read the message to help:</p>
    <p>
        {{ $data->message }}
    </p>

    <p>{{ $data->email }}</p>
    <p>{{ $data->telephone }}</p>
</body>
</html>
