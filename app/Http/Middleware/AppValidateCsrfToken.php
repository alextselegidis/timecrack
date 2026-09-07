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

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Symfony\Component\HttpFoundation\Cookie;

class AppValidateCsrfToken extends ValidateCsrfToken
{
    /**
     * Create a new "XSRF-TOKEN" cookie that contains the CSRF token.
     *
     * Laravel hard codes the name "XSRF-TOKEN", which is identical for every Laravel install.
     * When two installs share a domain, the cookie set by one would clobber the other. We suffix
     * the cookie with a short hash of APP_KEY, which is unique per install by definition, the same
     * way `config/session.php` and `App\Auth\AppSessionGuard` do.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $config
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function newCookie($request, $config)
    {
        return new Cookie(
            'XSRF-TOKEN-'.substr(sha1((string) config('app.key')), 0, 8),
            $request->session()->token(),
            $this->availableAt(60 * $config['lifetime']),
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null,
            $config['partitioned'] ?? false
        );
    }
}
