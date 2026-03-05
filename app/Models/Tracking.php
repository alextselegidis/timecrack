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
    ];

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

        return number_format(floor($this->duration_seconds * 100 / 3600) / 100, 2) . 'h';
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
     * 0.01 h precision (centihours) so that rounding artifacts are eliminated.
     */
    public function getNonBillableSecondsAttribute(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return 0;
        }

        $billableCentihours = (int) round(($this->billable_hours ?? 0) * 100);
        $durationCentihours = (int) round($this->duration_seconds / 36);

        if ($billableCentihours >= $durationCentihours) {
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
