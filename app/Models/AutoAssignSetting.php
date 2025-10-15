<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoAssignSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'standby_start_time',
        'standby_end_time',
        'weekend_standby_enabled',
        'consider_leave',
        'consider_region',
        'updated_by',
    ];
}