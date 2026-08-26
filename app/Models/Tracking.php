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

    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->ended_at) {
            return '';
        }

        $totalMinutes = (int) floor($this->duration_seconds / 60);
        $hours = (int) floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return "{$hours}h {$minutes}m";
    }

    public function getDurationDecimalAttribute()
    {
        if (!$this->started_at || !$this->ended_at) {
            return '';
        }

        return number_format(round($this->duration_seconds / 3600, 2), 2) . 'h';
    }

    public function getDurationSecondsAttribute(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return 0;
        }

        return max(0, $this->ended_at->getTimestamp() - $this->started_at->getTimestamp());
    }

    /**
     * Get non-billable seconds, comparing billable and duration at the same
     * 0.01 h precision so that rounding artifacts are eliminated.
     */
    public function getNonBillableSecondsAttribute(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return 0;
        }

        // Compare both values rounded to 2 decimal places using the same PHP
        // arithmetic, so that a billable_hours stored via either PHP round() or
        // MySQL ROUND() is treated as equal to the duration when it matches.
        $billableHours = round($this->billable_hours ?? 0, 2);
        $durationHours = round($this->duration_seconds / 3600, 2);

        if ($billableHours >= $durationHours) {
            return 0;
        }

        return max(0, $this->duration_seconds - (int) round(($this->billable_hours ?? 0) * 3600));
    }

    public function getNonBillableHoursAttribute(): float
    {
        return round($this->non_billable_seconds / 3600, 2);
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
