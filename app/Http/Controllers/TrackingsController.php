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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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
            $query->where('started_at', '>=', Carbon::parse($dateFrom, user_timezone())->startOfDay()->utc());
        }

        if ($dateTo) {
            $query->where('started_at', '<=', Carbon::parse($dateTo, user_timezone())->endOfDay()->utc());
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

        // Totals cover every filtered row, not just the current page, and use the same per row
        // rounding as the accessors, so the visible rows always add up to them.
        $totals = (clone $query)->selectTotals();

        $trackings = $query->withOverlapFlag()->paginate(25);

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
            'totals' => $totals,
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
            $query->where('started_at', '>=', Carbon::parse($dateFrom, user_timezone())->startOfDay()->utc());
        }

        if ($dateTo) {
            $query->where('started_at', '<=', Carbon::parse($dateTo, user_timezone())->endOfDay()->utc());
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

        $trackings = $query->withOverlapFlag()->get();

        $filename = 'timecrack_trackings_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($trackings, $isAdmin) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            $headers = [__('project'), __('started'), __('ended'), __('duration'), __('billable_hours'), __('non_billable_hours'), __('message'), __('overlap')];
            if ($isAdmin) {
                array_splice($headers, 1, 0, [__('user')]);
            }
            fputcsv($handle, $headers);

            // Data rows. The totals sum the very values the rows print, so every hours column of
            // the export adds up to its total exactly.
            $totals = ['duration' => 0.0, 'billable' => 0.0, 'non_billable' => 0.0];
            foreach ($trackings as $tracking) {
                $hours = [
                    'duration' => duration_hours($tracking->duration_minutes, ''),
                    'billable' => duration_hours((int) $tracking->billable_minutes, ''),
                    'non_billable' => duration_hours($tracking->non_billable_minutes, ''),
                ];

                foreach ($hours as $column => $value) {
                    $totals[$column] += (float) $value;
                }

                $row = [
                    $tracking->project->name ?? __('unknown'),
                    tz($tracking->started_at)->format('d/m/Y H:i'),
                    tz($tracking->ended_at)->format('d/m/Y H:i'),
                    $hours['duration'],
                    $hours['billable'],
                    $hours['non_billable'],
                    $tracking->message ?? '',
                    $tracking->is_overlapping ? __('yes') : __('no'),
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
                number_format($totals['duration'], 2, '.', ''),
                number_format($totals['billable'], 2, '.', ''),
                number_format($totals['non_billable'], 2, '.', ''),
                '',
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

        $tracking = new Tracking();
        $tracking->started_at = now()->subMinutes(60);
        $tracking->ended_at = now();
        $tracking->billable_minutes = 60;

        return view('pages.trackings-edit', [
            'tracking' => $tracking,
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
            'billable_hours' => ['nullable', 'numeric', 'min:0', $this->billableHoursCap($request)],
            'message' => ['nullable', 'string'],
        ]);

        // The form fields carry local time, the database keeps everything in UTC.
        $startedAt = Carbon::parse($request->input('started_at'), user_timezone())->utc();
        $endedAt = Carbon::parse($request->input('ended_at'), user_timezone())->utc();

        if ($response = $this->overlapWarning($request, $startedAt, $endedAt)) {
            return $response;
        }

        // The form asks for decimal hours, the database keeps whole minutes.
        // Sub minute trackings are treated as accidental and not billed.
        $durationSeconds = $endedAt->getTimestamp() - $startedAt->getTimestamp();
        $billableMinutes = $durationSeconds < 60 ? 0 : billable_minutes($request->input('billable_hours'));

        $tracking = Tracking::create([
            'project_id' => $request->input('project_id'),
            'user_id' => $request->input('user_id'),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'billable_minutes' => $billableMinutes,
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
            'billable_hours' => ['nullable', 'numeric', 'min:0', $this->billableHoursCap($request)],
            'message' => ['nullable', 'string'],
        ]);

        // The form fields carry local time, the database keeps everything in UTC.
        $startedAt = Carbon::parse($request->input('started_at'), user_timezone())->utc();
        $endedAt = Carbon::parse($request->input('ended_at'), user_timezone())->utc();

        if ($response = $this->overlapWarning($request, $startedAt, $endedAt, $tracking->id)) {
            return $response;
        }

        // The form asks for decimal hours, the database keeps whole minutes.
        // Sub minute trackings are treated as accidental and not billed.
        $durationSeconds = $endedAt->getTimestamp() - $startedAt->getTimestamp();
        $billableMinutes = $durationSeconds < 60 ? 0 : billable_minutes($request->input('billable_hours'));

        $tracking->update([
            'project_id' => $request->input('project_id'),
            'user_id' => $request->input('user_id'),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'billable_minutes' => $billableMinutes,
            'message' => $request->input('message'),
        ]);

        return redirect()->route('trackings.edit', $tracking->id)->with('success', __('record_saved_message'));
    }

    /**
     * Reject a billable value above the duration of the tracking, measured in the whole minutes
     * the application displays, so that billing the full duration is always accepted.
     */
    private function billableHoursCap(Request $request): callable
    {
        return function ($attribute, $value, $fail) use ($request) {
            if ($value === null || !$request->input('started_at') || !$request->input('ended_at')) {
                return;
            }

            $seconds = Carbon::parse($request->input('started_at'), user_timezone())
                ->diffInSeconds(Carbon::parse($request->input('ended_at'), user_timezone()), true);

            if ($value > (float) duration_hours((int) round($seconds / 60), '')) {
                $fail(__('Billable hours cannot exceed the duration between start and end times.'));
            }
        };
    }

    /**
     * Send the user back to the form once, with the overlapping trackings listed, unless the
     * overlap was already confirmed. Confirmed records are saved and flagged in the history.
     */
    private function overlapWarning(Request $request, $startedAt, $endedAt, ?int $ignoreId = null): ?RedirectResponse
    {
        if ($request->boolean('overlap_confirmed')) {
            return null;
        }

        $overlapping = Tracking::overlapping((int) $request->input('user_id'), $startedAt, $endedAt, $ignoreId);

        if ($overlapping->isEmpty()) {
            return null;
        }

        return redirect()->back()->withInput()->with('overlapping_trackings', $overlapping->map(fn($tracking) => sprintf(
            '%s: %s - %s',
            $tracking->project->name ?? __('unknown'),
            tz($tracking->started_at)->format('d/m/Y H:i'),
            tz($tracking->ended_at)->format('d/m/Y H:i')
        ))->all());
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
