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

use Illuminate\Database\Eloquent\Model;

class ActiveTracking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'started_at',
        'paused_at',
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
        'paused_at' => 'datetime',
    ];

    /**
     * Get the total paused seconds, including a pause that is still running.
     */
    public function getPausedSecondsAttribute(): int
    {
        $seconds = (int) $this->paused_duration;

        if ($this->paused_at) {
            $seconds += max(0, now()->getTimestamp() - $this->paused_at->getTimestamp());
        }

        return $seconds;
    }

    public function isPaused(): bool
    {
        return $this->paused_at !== null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
