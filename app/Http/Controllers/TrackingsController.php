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
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('message', 'like', '%' . $q . '%')
                    ->orWhereHas('project', fn($pq) => $pq->where('name', 'like', '%' . $q . '%'))
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $q . '%'));
            });
        }

        if ($dateFrom) {
            $query->whereDate('started_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('started_at', '<=', $dateTo);
        }

        $sort = $request->query('sort', 'started_at');
        $direction = $request->query('direction', 'desc');

        if ($sort && $direction) {
            $query->orderBy($sort, $direction);
        }

        // Calculate total duration in seconds for all filtered results (not just current page)
        $totalDurationSeconds = (clone $query)->sum(\DB::raw('TIMESTAMPDIFF(SECOND, started_at, ended_at) - COALESCE(paused_duration, 0)'));

        $trackings = $query->paginate(25);

        return view('pages.trackings', [
            'trackings' => $trackings,
            'q' => $q,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'isAdmin' => $user->isAdmin(),
            'totalDurationSeconds' => $totalDurationSeconds,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $query = Tracking::query()->with(['project', 'user']);

        // Non-admins can only see their own trackings
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $q = $request->query('q');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($q) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('message', 'like', '%' . $q . '%')
                    ->orWhereHas('project', fn($pq) => $pq->where('name', 'like', '%' . $q . '%'))
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $q . '%'));
            });
        }

        if ($dateFrom) {
            $query->whereDate('started_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('started_at', '<=', $dateTo);
        }

        $sort = $request->query('sort', 'started_at');
        $direction = $request->query('direction', 'desc');

        if ($sort && $direction) {
            $query->orderBy($sort, $direction);
        }

        $trackings = $query->get();
        $isAdmin = $user->isAdmin();

        $filename = 'timecrack_trackings_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($trackings, $isAdmin) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            $headers = [__('project'), __('started'), __('ended'), __('duration'), __('message')];
            if ($isAdmin) {
                array_splice($headers, 1, 0, [__('user')]);
            }
            fputcsv($handle, $headers);

            // Data rows
            $totalDurationSeconds = 0;
            foreach ($trackings as $tracking) {
                $totalDurationSeconds += $tracking->duration_seconds;
                $row = [
                    $tracking->project->name ?? __('unknown'),
                    $tracking->started_at->format('Y-m-d H:i'),
                    $tracking->ended_at->format('Y-m-d H:i'),
                    number_format($tracking->duration_seconds / 3600, 2, '.', ''),
                    $tracking->message ?? '',
                ];
                if ($isAdmin) {
                    array_splice($row, 1, 0, [$tracking->user->name ?? __('unknown')]);
                }
                fputcsv($handle, $row);
            }

            // Total row
            $totalRow = [
                __('total'),
                '',
                '',
                number_format($totalDurationSeconds / 3600, 2, '.', ''),
                '',
            ];
            if ($isAdmin) {
                array_splice($totalRow, 1, 0, ['']);
            }
            fputcsv($handle, $totalRow);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
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

        return redirect()->route('trackings.edit', $tracking->id)->with('success', __('record_saved_message'));
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

        return redirect()->route('trackings.edit', $tracking->id)->with('success', __('record_saved_message'));
    }

    public function destroy(Request $request, Tracking $tracking)
    {
        $user = $request->user();

        // Only admins can delete trackings
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $tracking->delete();

        return redirect()->route('trackings')->with('success', __('record_deleted_message'));
    }
}
