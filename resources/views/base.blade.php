<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@3.x/css/materialdesignicons.min.css" rel="stylesheet">
    <title>FIDIASPRO</title>
    <link rel="shortcut icon" href="/favicon.png" />
</head>

<body>
<div id="app">
        <main-component></main-component>
    </div>
    <script src="{{ asset('js/enable-push.js') }}" defer></script>
    <script type="text/javascript" src="{{ url('js/moment.min.js') }}"></script>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/all.js"></script>

    <script type="text/javascript" src="{{ url(mix('app/main.js')) }}"></script>
</body>

</html>