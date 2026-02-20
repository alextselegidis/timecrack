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

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Controllers\Controller;

class UsersApiV1Controller extends Controller
{
    use DisableAuthorization;

    protected $model = User::class;

    /**
     * Only admins can access user management.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Hidden fields for API response.
     */
    protected function hiddenFromResponse(): array
    {
        return ['password', 'remember_token'];
    }
}
