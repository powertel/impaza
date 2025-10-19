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
        'Waiting for assessment' => '#6b7280',          // gray-500
        'Fault has been assessed' => '#6366f1',         // indigo-500
        // In progress
        'Fault is under rectification' => '#f59e0b',    // amber-500
        // Cleared states
        'Fault has been cleared by Technician' => '#84cc16', // lime-500
        'Fault has been cleared by CT' => '#22c55e',    // emerald-500
        'Fault has been cleared by NOC' => '#16a34a',   // emerald-600
        // Additional lifecycle
        'Fault has been refered' => '#a855f7',          // purple-500 (referral)
        'Fault has been parked' => '#94a3b8',           // slate-400 (paused)
        'Fault has been revoked' => '#ef4444',          // red-500 (reversed)
        'Fault has been under escalated' => '#dc2626',  // red-600 (escalated)
    ];

}
