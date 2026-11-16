<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Staff sign in') &middot; {{ $schoolConfig['name'] }}</title>
    <link rel="icon" href="{{ asset($schoolConfig['images']['logo']) }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset($schoolConfig['images']['logo']) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @yield('body')
</body>
</html>
