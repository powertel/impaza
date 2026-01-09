<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_code',
        'description',
    ];
    const STATUS_COLOR = [
        // Pending / triage
        'Waiting for assessment' => '#fd0404ff',          // gray-500
        'Fault has been assessed' =>'yellow',         // yellow-400
        // In progress
        'Fault is under rectification' => '#f99f04ff',    // amber-500
        // Cleared states
        'Fault has been Rectified' => '#94a3b8', // lime-500
        'Fault has been cleared by CT' => '#22c55e',    // emerald-500
        'Fault has been Restored' => '#16a34a',  // emerald-600
        // Additional lifecycle
        'Fault has been refered' => '#a855f7',          // purple-500 (referral)
        'Fault has been parked' => '#94a3b8',           // slate-400 (paused)
        'Fault has been revoked' => '#ef4444',          // red-500 (reversed)
        'Fault  escalated to Chief Technician' => '#548ff3ff',  // red-600 (escalated)
    ];

}
