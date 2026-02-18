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

use App\Models\Tracking;
use Illuminate\Database\Eloquent\Builder;
use Orion\Http\Controllers\Controller;

class TrackingsController extends Controller
{
    protected $model = Tracking::class;

    /**
     * Filter the query for non-admin users to only show their own trackings.
     */
    protected function buildIndexFetchQuery($request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $user = $request->user();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Filter the query for non-admin users.
     */
    protected function buildShowFetchQuery($request, array $requestedRelations): Builder
    {
        $query = parent::buildShowFetchQuery($request, $requestedRelations);

        $user = $request->user();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Only admins can create/update/delete trackings via API.
     */
    public function authorizeResource(string $ability, $model = null): void
    {
        $user = request()->user();

        if (in_array($ability, ['create', 'update', 'delete']) && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Include relations in the response.
     */
    protected function alwaysIncludes(): array
    {
        return ['project', 'user'];
    }
}
