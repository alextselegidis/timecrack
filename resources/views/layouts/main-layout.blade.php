{{--
/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('shared.head')
</head>
<body class="main-layout d-flex flex-column min-vh-100 bg-light">
<div class="flex-grow-1">

    @include('shared.header')

    <!-- Page Heading -->

    @hasSection('pageTitle')
        <header class="bg-body-secondary mb-3">
            <div class="container">
                <div class="row">

                    <nav class="navbar navbar-expand-lg">
                        <div class="container-fluid">
                            <div class="d-flex flex-column py-3">
                                @if(View::hasSection('breadcrumbs'))
                                    @yield('breadcrumbs')
                                @endif
                            </div>
                            @hasSection('navActions')
                                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#page-navbar-actions">
                                    <span class="navbar-toggler-icon"></span>
                                </button>

                                <div class="collapse navbar-collapse" id="page-navbar-actions">
                                    <nav class="navbar-nav ms-lg-auto mb-2 mb-lg-0">
                                        @yield('navActions')
                                    </nav>
                                </div>
                            @endif
                        </div>
                    </nav>

                </div>
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="container mb-4">
        <div class="row">
            <div class="col">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Toast Container (Bottom Right using Bootstrap classes) -->
    <div class="toast-container position-fixed bottom-0 end-0 mb-5 p-3">

        <!-- Success Toast -->
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert" aria-live="assertive"
                 aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        @endif

        <!-- Error Toast -->
        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive"
                 aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        @endif

        <!-- Validation Errors Toast -->
        @if ($errors->any())
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive"
                 aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ $errors->first() }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        @endif

    </div>

</div>

<footer class="mt-auto">
    @include('shared.footer')
</footer>

<script src="vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="vendor/pace-js/pace.min.js"></script>
<script src="scripts/timecrack.js?{{config('app.version')}}"></script>

@yield('scripts')
</body>
</html>
