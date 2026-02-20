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

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Controllers\Controller;

class ProjectsApiV1Controller extends Controller
{
    use DisableAuthorization;

    protected $model = Project::class;

    /**
     * Filter the query for non-admin users to only show their assigned projects.
     */
    protected function buildIndexFetchQuery($request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $user = $request->user();

        if (!$user->isAdmin()) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
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
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query;
    }

    /**
     * Only admins can create projects.
     */
    protected function beforeStore($request, $model)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function beforeUpdate($request, $model)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    protected function beforeDestroy($request, $model)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
