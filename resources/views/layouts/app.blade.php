<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle ?? '' }} | {{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>
<body class="d-flex flex-column h-100">
<div class="flex-shrink-0">
     @include('layouts.navigation')

    <!-- Page Heading -->

    @if (isset($pageTitle))
        <header class="my-3">
            <div class="container">
                <div class="row">
                    <div class="col d-flex justify-content-between align-items-center border-bottom pb-3">
                        <div class="me-2">
                            <h2 class="mb-0">
                                {{ $pageTitle }}
                            </h2>
                        </div>
                        @if (isset($header))
                            <div class="header-actions">
                                {{ $header }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>
    @endif

    @if (session('success'))
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>


</div>
<div class="container mt-auto">
    <div class="row">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 px-0 mt-3 border-top small">
            <div class="col-md-4 ps-0 d-flex align-items-center">
                <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
                    <img src="{{ asset('img/logo.png') }}" alt="logo" class="footer-logo"/>
                </a>
                <span class="mb-3 mb-md-0 text-body-secondary">
                    <a href="https://timecrack.org" class="text-decoration-none me-2">Timecrack</a>

                    v{{config('app.version')}}
                </span>
            </div>

            <div class="col-md-4 pe-0 d-flex align-items-center justify-content-md-end">
                <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
                    <img src="{{ asset('img/alextselegidis-logo-16x16.png') }}" alt="logo"/>
                </a>
                <span class="mb-3 mb-md-0 text-body-secondary">
                <a href="https://alextselegidis.com" class="text-decoration-none">
                    Alex Tselegidis
                </a>
                © {{date('Y')}} - Software Services</span>
            </div>

        </footer>
    </div>
</div>

</body>
</html>
