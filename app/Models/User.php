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
        'region',
        'weekly_standby',
        'weekend_standby'
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
        'weekly_standby' => 'boolean',
        'weekend_standby' => 'boolean',
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
}
