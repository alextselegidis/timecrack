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
        $isAdmin = $user->isAdmin();
        $query = Tracking::query()->with(['project', 'user']);

        // Non-admins can only see their own trackings
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $q = $request->query('q');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $userIds = $request->query('user_ids', []);

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

        // User filter (admin only)
        if ($isAdmin && !empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        $sort = $request->query('sort', 'started_at');
        $direction = $request->query('direction', 'desc');

        // Handle sorting with joins for related columns
        if ($sort === 'project') {
            $query->join('projects', 'trackings.project_id', '=', 'projects.id')
                ->orderBy('projects.name', $direction)
                ->select('trackings.*');
        } elseif ($sort === 'user') {
            $query->join('users', 'trackings.user_id', '=', 'users.id')
                ->orderBy('users.name', $direction)
                ->select('trackings.*');
        } elseif (in_array($sort, ['started_at', 'ended_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('started_at', $direction);
        }

        // Calculate total duration in seconds for all filtered results (not just current page)
        $totalDurationSeconds = (clone $query)->sum(\DB::raw('TIMESTAMPDIFF(SECOND, started_at, ended_at)'));

        // Calculate total billable hours for all filtered results
        $totalBillableHours = (clone $query)->sum('billable_hours');

        // Calculate total non-billable hours (duration - billable)
        $totalNonBillableHours = ($totalDurationSeconds / 3600) - $totalBillableHours;

        $trackings = $query->paginate(25);

        // Get all users for filter (admin only)
        $users = $isAdmin ? \App\Models\User::query()->where('is_active', true)->orderBy('name')->get() : collect();

        return view('pages.trackings', [
            'trackings' => $trackings,
            'q' => $q,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'userIds' => $userIds,
            'users' => $users,
            'isAdmin' => $isAdmin,
            'totalDurationSeconds' => $totalDurationSeconds,
            'totalBillableHours' => $totalBillableHours,
            'totalNonBillableHours' => $totalNonBillableHours,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();
        $query = Tracking::query()->with(['project', 'user']);

        // Non-admins can only see their own trackings
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $q = $request->query('q');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $userIds = $request->query('user_ids', []);

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

        // User filter (admin only)
        if ($isAdmin && !empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        $sort = $request->query('sort', 'started_at');
        $direction = $request->query('direction', 'desc');

        // Handle sorting with joins for related columns
        if ($sort === 'project') {
            $query->join('projects', 'trackings.project_id', '=', 'projects.id')
                ->orderBy('projects.name', $direction)
                ->select('trackings.*');
        } elseif ($sort === 'user') {
            $query->join('users', 'trackings.user_id', '=', 'users.id')
                ->orderBy('users.name', $direction)
                ->select('trackings.*');
        } elseif (in_array($sort, ['started_at', 'ended_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('started_at', $direction);
        }

        $trackings = $query->get();

        $filename = 'timecrack_trackings_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($trackings, $isAdmin) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            $headers = [__('project'), __('started'), __('ended'), __('duration'), __('billable_hours'), __('non_billable_hours'), __('message')];
            if ($isAdmin) {
                array_splice($headers, 1, 0, [__('user')]);
            }
            fputcsv($handle, $headers);

            // Data rows
            $totalDurationSeconds = 0;
            $totalBillableHours = 0;
            $totalNonBillableHours = 0;
            foreach ($trackings as $tracking) {
                $totalDurationSeconds += $tracking->duration_seconds;
                $totalBillableHours += $tracking->billable_hours ?? 0;
                $nonBillableHours = ($tracking->duration_seconds / 3600) - ($tracking->billable_hours ?? 0);
                $totalNonBillableHours += $nonBillableHours;
                $row = [
                    $tracking->project->name ?? __('unknown'),
                    $tracking->started_at->format('d/m/Y H:i'),
                    $tracking->ended_at->format('d/m/Y H:i'),
                    number_format($tracking->duration_seconds / 3600, 2, '.', ''),
                    $tracking->billable_hours !== null ? number_format($tracking->billable_hours, 2, '.', '') : '',
                    number_format($nonBillableHours, 2, '.', ''),
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
                number_format($totalBillableHours, 2, '.', ''),
                number_format($totalNonBillableHours, 2, '.', ''),
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

        // Only admins can create trackings manually
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $projects = Project::query()->orderBy('name')->get();
        $users = \App\Models\User::query()->where('is_active', true)->orderBy('name')->get();

        return view('pages.trackings-edit', [
            'tracking' => new Tracking(),
            'projects' => $projects,
            'users' => $users,
            'isAdmin' => true,
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
            'user_id' => ['required', 'exists:users,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after:started_at'],
            'billable_hours' => ['nullable', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $tracking = Tracking::create([
            'project_id' => $request->input('project_id'),
            'user_id' => $request->input('user_id'),
            'started_at' => $request->input('started_at'),
            'ended_at' => $request->input('ended_at'),
            'billable_hours' => $request->input('billable_hours'),
            'message' => $request->input('message'),
        ]);

        return redirect()->route('trackings')->with('success', __('record_saved_message'));
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
            'billable_hours' => ['nullable', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $tracking->update([
            'project_id' => $request->input('project_id'),
            'user_id' => $request->input('user_id'),
            'started_at' => $request->input('started_at'),
            'ended_at' => $request->input('ended_at'),
            'billable_hours' => $request->input('billable_hours'),
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

        return redirect()->back()->with('success', __('record_deleted_message'));
    }
}
