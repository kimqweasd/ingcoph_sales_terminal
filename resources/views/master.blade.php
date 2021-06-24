<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ env('APP_NAME') }}</title>

        <link href="{{ mix('compiled/css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/adminlte.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/vuetify/vuetify.min.css') }}" rel="stylesheet">

        <script src="{{ mix('compiled/js/app.js') }}" defer></script>
        <script src="{{ asset('js/jquery.min.js') }}" defer></script>
        <script src="{{ asset('js/sweetalert.min.js') }}" defer></script>
        <script src="{{ asset('js/adminlte.min.js') }}" defer></script>
        @javascript('api', $api)
    </head>
    <body  style="padding: 0; margin: 0; overflow: hidden !important;">
        <div id="app" style="background-color: white; height:100%; width:100%;">
            @yield('content')
        </div>
    </body>
</html>
