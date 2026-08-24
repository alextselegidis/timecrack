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

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

<base href="{{url('')}}/">

<title>@yield('pageTitle') | Timecrack</title>
<meta name="description" content="Timecrack is a time tracking application designed to help users easily track their work hours.">
<meta name="theme-color" content="#d97638">

<link rel="icon" href="favicon.ico" type="image/x-icon"/>
<link rel="apple-touch-icon" href="images/apple-touch-icon.png"/>

<link rel="manifest" href="manifest.json">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Timecrack">

<link rel="stylesheet" href="vendor/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="vendor/bootstrap-icons/bootstrap-icons.min.css">
<link rel="stylesheet" href="vendor/pace-js/pace-theme-default.min.css">
<link rel="stylesheet" href="vendor/pace-js/pace-theme-flat-top.tmpl.css">
<link rel="stylesheet" href="styles/timecrack.css?{{config('app.version')}}">

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('{{ url('sw.js') }}?v={{ config('app.version') }}');
        });
    }
</script>

@yield('styles')
