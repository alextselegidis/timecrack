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

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tracking;
use Illuminate\Http\Request;

class TrackingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Tracking::query()->with(['project', 'user']);

        // Non-admins can only see their own trackings
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $q = $request->query('q');

        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('message', 'like', '%' . $q . '%')
                    ->orWhereHas('project', fn($pq) => $pq->where('name', 'like', '%' . $q . '%'))
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $q . '%'));
            });
        }

        $sort = $request->query('sort', 'started_at');
        $direction = $request->query('direction', 'desc');

        if ($sort && $direction) {
            $query->orderBy($sort, $direction);
        }

        $trackings = $query->paginate(25);

        return view('pages.trackings', [
            'trackings' => $trackings,
            'q' => $q,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $projects = Project::query()->orderBy('name')->get();
        } else {
            $projects = $user->projects()->orderBy('name')->get();
        }

        return view('pages.trackings-edit', [
            'tracking' => new Tracking(),
            'projects' => $projects,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Only admins can create trackings manually
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        Tracking::create([
            'project_id' => $request->input('project_id'),
            'user_id' => $user->id,
            'started_at' => $request->input('started_at'),
            'ended_at' => $request->input('ended_at'),
            'paused_duration' => 0,
            'message' => $request->input('message'),
        ]);

        return redirect()->route('trackings')->with('success', __('Tracking created successfully.'));
    }

    public function edit(Request $request, Tracking $tracking)
    {
        $user = $request->user();

        // Only admins can edit trackings
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $projects = Project::query()->orderBy('name')->get();
        $users = \App\Models\User::query()->where('is_active', true)->orderBy('name')->get();

        session(['trackings_list_url' => url()->previous()]);

        return view('pages.trackings-edit', [
            'tracking' => $tracking,
            'projects' => $projects,
            'users' => $users,
            'isAdmin' => true,
        ]);
    }

    public function update(Request $request, Tracking $tracking)
    {
        $user = $request->user();

        // Only admins can edit trackings
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'user_id' => ['required', 'exists:users,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'paused_duration' => ['nullable', 'integer', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $tracking->update([
            'project_id' => $request->input('project_id'),
            'user_id' => $request->input('user_id'),
            'started_at' => $request->input('started_at'),
            'ended_at' => $request->input('ended_at'),
            'paused_duration' => $request->input('paused_duration', 0),
            'message' => $request->input('message'),
        ]);

        return redirect()->route('trackings')->with('success', __('Tracking updated successfully.'));
    }

    public function destroy(Request $request, Tracking $tracking)
    {
        $user = $request->user();

        // Only admins can delete trackings
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $tracking->delete();

        return redirect()->route('trackings')->with('success', __('Tracking deleted successfully.'));
    }
}
