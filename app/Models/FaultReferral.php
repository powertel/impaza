<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultReferral extends Model
{
    use HasFactory;

    protected $table = 'fault_referrals';

    protected $fillable = [
        'fault_id',
        'from_section_id',
        'to_section_id',
        'referred_by',
        'previous_status_id',
        'work_note',
        'started_at',
        'completed_at',
    ];

    public $timestamps = false;
}