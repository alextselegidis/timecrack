<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'external_id',
        'created_at',
        'updated_at',
        'started_at',
        'ended_at',
        'summary',
        'is_billable',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
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
        // if ( ! $this->started_at || ! $this->ended_at)
        // {
        //     return '';
        // }
        //
        // return abs($this->ended_at->getTimestamp() - $this->started_at->getTimestamp()) / 60; // Minutes

        return $this->ended_at->diffForHumans($this->started_at, CarbonInterface::DIFF_ABSOLUTE, TRUE, 4);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function toOptions($where = NULL)
    {
        $query = self::query();

        if ($where)
        {
            $query->where($where);
        }

        return $query->selectRaw('summary AS label, id AS value')->get();
    }
}
