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

use Carbon\CarbonInterface;
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
        'paused_duration',
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
    ];

    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->ended_at) {
            return '';
        }

        $totalSeconds = $this->ended_at->getTimestamp() - $this->started_at->getTimestamp() - ($this->paused_duration ?? 0);

        return $this->ended_at->copy()->subSeconds($this->paused_duration ?? 0)->diffForHumans(
            $this->started_at,
            CarbonInterface::DIFF_ABSOLUTE,
            true,
            4
        );
    }

    public function getDurationSecondsAttribute(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return 0;
        }

        return max(0, $this->ended_at->getTimestamp() - $this->started_at->getTimestamp() - ($this->paused_duration ?? 0));
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
