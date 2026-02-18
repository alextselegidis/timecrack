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

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'is_active', 'role', 'pinned_project_ids'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'pinned_project_ids' => 'array',
        ];
    }

    public function getPinnedProjectIds(): array
    {
        return $this->pinned_project_ids ?? [];
    }

    public function hasProjectPinned(int $projectId): bool
    {
        return in_array($projectId, $this->getPinnedProjectIds());
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function trackings()
    {
        return $this->hasMany(Tracking::class);
    }

    public function activeTracking()
    {
        return $this->hasOne(ActiveTracking::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::ADMIN->value;
    }

    public function isTracking(): bool
    {
        return $this->activeTracking !== null;
    }
}
