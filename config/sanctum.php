<?php

/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */

use App\Http\Middleware\AppValidateCsrfToken;

return [
    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Stateful requests run through their own CSRF middleware, which must be the
    | one that names the "XSRF-TOKEN" cookie per install, just like the web group
    | in `bootstrap/app.php` does. Every other key keeps the package default.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => AppValidateCsrfToken::class,
    ],
];
