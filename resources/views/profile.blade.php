<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>this is profile for {{ session('name')->pname }}</h1>
    <h3>id {{ session('id') }}</h3>
    {{-- <p>login {{ session('login') }}</p> --}}
    <a href="/logout">Logout</a>
</body>
</html>
