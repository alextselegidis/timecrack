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
            $projects = Project::query()->orderBy('name')->get();
        } else {
            $projects = $user->projects()->orderBy('name')->get();
        }

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
        ]);
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
            'paused_at' => null,
            'paused_duration' => 0,
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

        $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $activeTracking = $user->activeTracking;

        // Calculate final paused duration if currently paused
        $pausedDuration = $activeTracking->paused_duration ?? 0;
        if ($activeTracking->isPaused()) {
            $pausedDuration += now()->getTimestamp() - $activeTracking->paused_at->getTimestamp();
        }

        // Create tracking record
        Tracking::create([
            'project_id' => $activeTracking->project_id,
            'user_id' => $user->id,
            'started_at' => $activeTracking->started_at,
            'ended_at' => now(),
            'paused_duration' => $pausedDuration,
            'message' => $request->input('message') ?: $activeTracking->message,
        ]);

        // Delete active tracking
        $activeTracking->delete();

        return redirect()->route('dashboard')->with('success', __('Timer stopped and tracking saved.'));
    }

    public function pause(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking');

        if (!$user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('No active timer to pause.'));
        }

        if ($user->isPaused()) {
            return redirect()->route('dashboard')->with('error', __('Timer is already paused.'));
        }

        $user->activeTracking->update([
            'paused_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', __('Timer paused.'));
    }

    public function resume(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking');

        if (!$user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('No active timer to resume.'));
        }

        if (!$user->isPaused()) {
            return redirect()->route('dashboard')->with('error', __('Timer is not paused.'));
        }

        $activeTracking = $user->activeTracking;

        // Add paused time to total paused duration
        $pausedDuration = ($activeTracking->paused_duration ?? 0) + (now()->getTimestamp() - $activeTracking->paused_at->getTimestamp());

        $activeTracking->update([
            'paused_at' => null,
            'paused_duration' => $pausedDuration,
        ]);

        return redirect()->route('dashboard')->with('success', __('Timer resumed.'));
    }

    public function updateMessage(Request $request)
    {
        $user = $request->user();
        $user->load('activeTracking');

        if (!$user->isTracking()) {
            return redirect()->route('dashboard')->with('error', __('No active timer.'));
        }

        $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->activeTracking->update([
            'message' => $request->input('message'),
        ]);

        return redirect()->route('dashboard')->with('success', __('Message updated.'));
    }
}
