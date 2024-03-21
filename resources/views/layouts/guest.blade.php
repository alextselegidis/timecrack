<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Timecrack') }}</title>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <div class="min-vh-100 d-flex flex-column justify-content-sm-center align-items-center">
                <div class="text-center">
                    <a href="/">
                        <img src="{{ asset('img/logo.png') }}" alt="logo" class="guest-logo-img"/>
                    </a>
                </div>

                <div class="p-4 bg-white shadow-md overflow-hidden w-100">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
