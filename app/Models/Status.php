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
        'Waiting for Assessment' => '#DC2626',
        'Fault has been Assessed' => '#CA8A04',
        // In progress
        'Fault is under Rectification' => '#D97706',
        // Cleared states
        'Fault has been Rectified' => '#64748B',
        'Fault has been cleared by CT' => '#16A34A',
        'Fault has been Restored' => '#15803D',
        // Additional lifecycle
        'Fault has been Refered' => '#9333EA',
        'Fault has been Parked' => '#64748B',
        'Fault has been Revoked' => '#DC2626',
        'Fault  escalated to Chief Technician' => '#2563EB',
        'Impacted by POP outage' => '#0284C7',
    ];

}
