<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <div class="min-vh-100 d-flex flex-column justify-content-sm-center align-items-center">
                <div class="text-center">
                    <a href="/">
                        <x-application-logo class="w-25 h-25"/>
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
