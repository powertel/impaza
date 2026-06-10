<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'section_id',
        'position_id',
        'phonenumber',
        'user_status',
        'is_access',
        'dashboard_auto_refresh_enabled',
        'dashboard_refresh_seconds',
        'region',
        'weekly_standby',
        'weekend_standby'
    ];

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'technician_zone', 'user_id', 'zone_id');
    }

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
        'weekly_standby' => 'boolean',
        'weekend_standby' => 'boolean',
        'is_access' => 'boolean',
        'dashboard_auto_refresh_enabled' => 'boolean',
        'dashboard_refresh_seconds' => 'integer',
    ];

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

        //relationship of section and faults
        public function sections()
        {
            return $this->belongsToMany(Section::class);
        }

        public function faults()
        {
            return $this->hasMany(Fault::class);
        }

        public function assignedFaults()
        {
            return $this->hasMany(Fault::class, 'assignedTo');
        }

    public function pushTokens()
    {
        return $this->hasMany(UserPushToken::class);
    }
}
