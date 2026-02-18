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

namespace App\Http\Controllers\Api;

use App\Models\User;
use Orion\Http\Controllers\Controller;

class UsersController extends Controller
{
    protected $model = User::class;

    /**
     * Only admins can access user management.
     */
    public function authorizeResource(string $ability, $model = null): void
    {
        $user = request()->user();

        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Hidden fields for API response.
     */
    protected function hiddenFromResponse(): array
    {
        return ['password', 'remember_token'];
    }
}
