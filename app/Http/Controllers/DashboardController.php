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

use App\Models\ActiveTracking;
use App\Models\Project;
use App\Models\Tracking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking.project');

        // Get projects accessible to the user
        if ($user->isAdmin()) {
            $projects = Project::query()->get();
        } else {
            $projects = $user->projects()->get();
        }

        // Sort projects: pinned first, then by last use (most recent tracking)
        $pinnedIds = $user->getPinnedProjectIds();
        $projectIds = $projects->pluck('id')->toArray();

        // Get last tracking time for each project
        $lastUsed = Tracking::query()
            ->whereIn('project_id', $projectIds)
            ->where('user_id', $user->id)
            ->selectRaw('project_id, MAX(ended_at) as last_used')
            ->groupBy('project_id')
            ->pluck('last_used', 'project_id')
            ->toArray();

        $projects = $projects->sortBy(function ($project) use ($pinnedIds, $lastUsed) {
            $isPinned = in_array($project->id, $pinnedIds) ? 0 : 1;
            $lastUsedTime = $lastUsed[$project->id] ?? '1970-01-01';
            return [$isPinned, $lastUsedTime];
        })->sortBy(function ($project) use ($pinnedIds, $lastUsed) {
            // Secondary sort: pinned first, then by most recent use (descending)
            $isPinned = in_array($project->id, $pinnedIds) ? 0 : 1;
            $lastUsedTime = $lastUsed[$project->id] ?? null;
            return $isPinned;
        })->values();

        // Sort pinned projects by last use, then non-pinned by last use
        $pinnedProjects = $projects->filter(fn($p) => in_array($p->id, $pinnedIds))
            ->sortByDesc(fn($p) => $lastUsed[$p->id] ?? '1970-01-01');
        $unpinnedProjects = $projects->filter(fn($p) => !in_array($p->id, $pinnedIds))
            ->sortByDesc(fn($p) => $lastUsed[$p->id] ?? '1970-01-01');

        $projects = $pinnedProjects->merge($unpinnedProjects);

        // Get recent trackings for the user
        $trackings = Tracking::query()
            ->where('user_id', $user->id)
            ->with('project')
            ->orderByDesc('ended_at')
            ->limit(10)
            ->get();

        return view('pages.dashboard', [
            'projects' => $projects,
            'trackings' => $trackings,
            'user' => $user,
            'pinnedIds' => $pinnedIds,
        ]);
    }

    public function togglePin(Request $request, Project $project)
    {
        $user = $request->user();
        $pinnedIds = $user->getPinnedProjectIds();

        if (in_array($project->id, $pinnedIds)) {
            $pinnedIds = array_values(array_diff($pinnedIds, [$project->id]));
        } else {
            $pinnedIds[] = $project->id;
        }

        $user->update(['pinned_project_ids' => $pinnedIds]);

        return redirect()->route('dashboard');
    }

    public function start(Request $request, Project $project)
    {
        $user = $request->user();

        // Check if user already has an active tracking
        if ($user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('You already have an active timer. Stop it first.'));
        }

        // Check if user has access to this project
        if (!$user->isAdmin() && !$user->projects->contains($project)) {
            return redirect()->route('dashboard')->with('error', __('You do not have access to this project.'));
        }

        // Start tracking
        ActiveTracking::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'started_at' => now(),
            'message' => null,
        ]);

        return redirect()->route('dashboard')->with('success', __('Timer started for :project.', ['project' => $project->name]));
    }

    public function stop(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking');

        if (!$user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('No active timer to stop.'));
        }

        $activeTracking = $user->activeTracking;
        $endedAt = now();

        $maxHours = round(max(0, $endedAt->getTimestamp() - $activeTracking->started_at->getTimestamp()) / 3600, 2);

        $request->validate([
            'message' => ['nullable', 'string'],
            'billable_hours' => ['nullable', 'numeric', 'min:0', 'max:' . $maxHours],
        ]);

        // Calculate billable hours: use provided value, or default to duration in hours
        $billableHours = $request->input('billable_hours');
        if ($billableHours === null || $billableHours === '') {
            $durationSeconds = max(0, $endedAt->getTimestamp() - $activeTracking->started_at->getTimestamp());
            $billableHours = round($durationSeconds / 3600, 2);
        }

        // Create tracking record
        Tracking::create([
            'project_id' => $activeTracking->project_id,
            'user_id' => $user->id,
            'started_at' => $activeTracking->started_at,
            'ended_at' => $endedAt,
            'billable_hours' => $billableHours,
            'message' => $request->input('message') ?: $activeTracking->message,
        ]);

        // Delete active tracking
        $activeTracking->delete();

        return redirect()->route('dashboard')->with('success', __('Timer stopped and tracking saved.'));
    }

    public function discard(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking');

        if (!$user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('No active timer to discard.'));
        }

        $user->activeTracking->delete();

        return redirect()->route('dashboard')->with('success', __('Tracking discarded.'));
    }

    public function updateMessage(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking');

        if (!$user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('No active timer.'));
        }

        $request->validate([
            'message' => ['nullable', 'string'],
        ]);

        $user->activeTracking->update([
            'message' => $request->input('message'),
        ]);

        return redirect()->route('dashboard')->with('success', __('Message updated.'));
    }
}
