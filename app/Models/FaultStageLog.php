<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FaultStageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'fault_id',
        'status_id',
        'started_at',
        'ended_at',
        'started_by',
        'ended_by',
        'duration_seconds',
    ];

    public $timestamps = false;

    public static function startStage(int $faultId, int $statusId, ?int $userId = null): self
    {
        // End any currently open stage for this fault
        $open = self::where('fault_id', $faultId)->whereNull('ended_at')->first();
        if ($open) {
            $open->ended_at = now();
            $open->ended_by = $userId;
            $open->duration_seconds = Carbon::parse($open->started_at)->diffInSeconds($open->ended_at);
            $open->save();
        }

        return self::create([
            'fault_id' => $faultId,
            'status_id' => $statusId,
            'started_at' => now(),
            'started_by' => $userId,
        ]);
    }

    public static function endStage(int $faultId, ?int $userId = null): void
    {
        $open = self::where('fault_id', $faultId)->whereNull('ended_at')->first();
        if ($open) {
            $open->ended_at = now();
            $open->ended_by = $userId;
            $open->duration_seconds = Carbon::parse($open->started_at)->diffInSeconds($open->ended_at);
            $open->save();
        }
    }
}