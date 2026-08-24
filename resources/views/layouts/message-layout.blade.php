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
<body class="bg-light message-layout">

<div class="d-flex justify-content-center align-items-lg-center min-vh-100">

    <div class="bg-white w-100 text-center rounded-0 rounded-lg-3 shadow-lg-sm py-lg-4 min-vh-100 min-vh-lg-auto d-flex flex-column justify-content-center d-lg-block" style="max-width: 500px;">

         <div class="text-center mt-5 mb-3">
             <img src="images/logo.png" alt="logo" class="public-logo-image mb-3" style="width: 128px"/>
         </div>

        @include('shared.errors', ['class' => 'mx-2 mx-lg-5 text-center'])

        @yield('content')

         <div class="text-center small my-5">
             Powered By
             <a href="https://github.com/alextselegidis/timecrack" target="_blank">
                 Timecrack
             </a>
         </div>

    </div>

</div>

<script src="vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="vendor/pace-js/pace.min.js"></script>
<script src="scripts/timecrack.js?{{config('app.version')}}"></script>

@yield('scripts')
</body>
</html>
