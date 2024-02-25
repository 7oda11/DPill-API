<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <p>{{ session('login') }}</p>
    <form action="/login" method="post">
        @csrf
        <label for="name">name</label>
        <input type="email" name="name" id="name" value="{{ old('name') }}"><br>
        <label for="password">password</label>
        <input type="password" name="password" id="password"><br>
        <p style="color: red">{{ session('message') }}</p>
        <input type="submit" value="Login">
    </form>
</body>

</html>
