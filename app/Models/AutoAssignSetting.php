<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoAssignSetting extends Model
{
    use HasFactory;

    protected $table = 'auto_assign_settings';

    protected $fillable = [
        'standby_start_time',
        'standby_end_time',
        'weekend_standby_enabled',
        'consider_leave',
        'consider_region',
        'auto_assign_enabled',
        'updated_by',
    ];
}