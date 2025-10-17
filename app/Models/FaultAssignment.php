<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FaultAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fault_id',
        'user_id',
        'assigned_at',
        'resolved_at',
        'assigned_by',
        'is_standby',
        'region',
        'duration_seconds',
    ];

    public $timestamps = false;

    public static function start(int $faultId, int $userId, ?int $assignedBy = null, bool $isStandby = false, ?string $region = null): self
    {
        // If there is an open assignment record, close it
        $open = self::where('fault_id', $faultId)->whereNull('resolved_at')->first();
        if ($open) {
            $open->resolved_at = now();
            $open->duration_seconds = Carbon::parse($open->assigned_at)->diffInSeconds($open->resolved_at);
            $open->save();
        }
        return self::create([
            'fault_id' => $faultId,
            'user_id' => $userId,
            'assigned_at' => now(),
            'assigned_by' => $assignedBy,
            'is_standby' => $isStandby,
            'region' => $region,
        ]);
    }

    public static function resolveForFault(int $faultId): void
    {
        $open = self::where('fault_id', $faultId)->whereNull('resolved_at')->first();
        if ($open) {
            $open->resolved_at = now();
            $open->duration_seconds = Carbon::parse($open->assigned_at)->diffInSeconds($open->resolved_at);
            $open->save();
        }
    }

    /**
     * Reopen the most recent assignment for a fault by clearing resolved_at and duration.
     * This continues the original assignment timing without creating a new record.
     */
    public static function reopenForFault(int $faultId): void
    {
        $latest = self::where('fault_id', $faultId)
            ->orderByDesc('assigned_at')
            ->first();
        if ($latest && $latest->resolved_at) {
            $latest->resolved_at = null;
            $latest->duration_seconds = null;
            $latest->save();
        }
    }
}