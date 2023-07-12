<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tracked_project_id',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_alt',
        'password',
        'email_verified_at',
        'tracked_started_at',
        'gender',
        'street',
        'street_additional',
        'city',
        'state',
        'postcode',
        'country',
        'birthdate',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'tracked_started_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        $parts = [];

        if ($this->street)
        {
            $parts[] = $this->street;
        }

        if ($this->street_additional)
        {
            $parts[] = $this->street_additional;
        }
        if ($this->city)
        {
            $parts[] = $this->city;
        }


        if ($this->postcode)
        {
            $parts[] = $this->postcode;
        }

        if ($this->country)
        {
            $parts[] = $this->country;
        }

        return implode(',', $parts);
    }

    public function tracked_project()
    {
        return $this->belongsTo(Project::class, 'tracked_project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public static function toOptions($where = NULL)
    {
        $query = self::query();

        if ($where)
        {
            $query->where($where);
        }

        return $query->selectRaw('CONCAT_WS(" ", first_name, last_name) AS label, id AS value')->get();
    }
}
