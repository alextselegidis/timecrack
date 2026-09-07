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

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'started_at',
        'ended_at',
        'billable_hours',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'billable_hours' => 'decimal:2',
        'is_overlapping' => 'boolean',
    ];

    /**
     * Get the trackings of the same user whose time range overlaps the given one.
     */
    public static function overlapping(int $userId, $startedAt, $endedAt, ?int $ignoreId = null)
    {
        return static::query()
            ->with('project')
            ->where('user_id', $userId)
            ->where('started_at', '<', $endedAt)
            ->where('ended_at', '>', $startedAt)
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Flag every row of the result that overlaps another tracking of the same user, so that
     * the history and the export can mark it without querying each row on its own.
     */
    public function scopeWithOverlapFlag($query)
    {
        return $query->select('trackings.*')->selectRaw(
            'EXISTS (SELECT 1 FROM trackings AS overlapping'
            . ' WHERE overlapping.user_id = trackings.user_id AND overlapping.id <> trackings.id'
            . ' AND overlapping.started_at < trackings.ended_at AND overlapping.ended_at > trackings.started_at)'
            . ' AS is_overlapping'
        );
    }

    /**
     * Every duration in the application is rounded to whole minutes exactly once, here.
     *
     * The times are stored to the second but shown to the minute, so a value that is rounded
     * again at each display site drifts: a page could floor 25 rows of stray seconds away and
     * still print a total that was summed from the raw seconds. Rounding once, per row, and
     * summing only rounded values keeps the history, the dashboard, the export and the totals
     * on the same numbers. `scopeSelectTotals()` mirrors this arithmetic in SQL.
     */
    public function getDurationMinutesAttribute(): int
    {
        return max(0, (int) round($this->duration_seconds / 60));
    }

    /**
     * Billable minutes come from the stored `billable_hours`, capped at the duration so that a
     * value entered before an edit shortened the tracking can never exceed it.
     */
    public function getBillableMinutesAttribute(): int
    {
        return min($this->duration_minutes, max(0, (int) round((float) ($this->billable_hours ?? 0) * 60)));
    }

    /**
     * Non billable minutes are the remainder, so billable + non billable is always the duration.
     */
    public function getNonBillableMinutesAttribute(): int
    {
        return $this->duration_minutes - $this->billable_minutes;
    }

    public function getDurationSecondsAttribute(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return 0;
        }

        return max(0, $this->ended_at->getTimestamp() - $this->started_at->getTimestamp());
    }

    public function getDurationAttribute(): string
    {
        return !$this->started_at || !$this->ended_at ? '' : duration_label($this->duration_minutes);
    }

    public function getDurationDecimalAttribute(): string
    {
        return !$this->started_at || !$this->ended_at ? '' : duration_hours($this->duration_minutes) . 'h';
    }

    /**
     * Sum the duration and the billable minutes of a whole query, using the same rounding as the
     * accessors above, so that a total always equals the sum of the rows it covers.
     */
    public function scopeSelectTotals($query): array
    {
        $duration = 'GREATEST(0, ROUND(TIMESTAMPDIFF(SECOND, trackings.started_at, trackings.ended_at) / 60))';
        $billable = 'LEAST(' . $duration . ', GREATEST(0, ROUND(COALESCE(trackings.billable_hours, 0) * 60)))';

        $totals = $query->reorder()
            ->select(\DB::raw(
                'COALESCE(SUM(' . $duration . '), 0) AS duration_minutes,'
                . ' COALESCE(SUM(' . $billable . '), 0) AS billable_minutes'
            ))
            ->toBase()
            ->first();

        $durationMinutes = (int) ($totals->duration_minutes ?? 0);
        $billableMinutes = (int) ($totals->billable_minutes ?? 0);

        return [
            'duration' => $durationMinutes,
            'billable' => $billableMinutes,
            'non_billable' => $durationMinutes - $billableMinutes,
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
