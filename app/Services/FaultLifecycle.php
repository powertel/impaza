<?php

namespace App\Services;

use App\Models\Fault;
use App\Models\FaultStageLog;
use App\Models\FaultAssignment;
use App\Models\Status;
use App\Models\AutoAssignSetting;
use Illuminate\Support\Carbon;

class FaultLifecycle
{
    public static function recordStatusChange(Fault $fault, int $toStatusId, ?int $actorUserId = null): void
    {
        // End any open stage and start a new one for the new status
        FaultStageLog::startStage($fault->id, $toStatusId, $actorUserId);

        // If this is the terminal status (cleared by NOC), immediately end the stage and close any open assignment
        if ($toStatusId === self::nocClearedId()) {
            FaultStageLog::endStage($fault->id, $actorUserId);
            self::resolveAssignment($fault);
        }
    }

    public static function startAssignment(Fault $fault, int $userId, ?int $actorUserId = null, bool $isStandby = false, ?string $region = null): void
    {
        FaultAssignment::start($fault->id, $userId, $actorUserId, $isStandby, $region);
    }

    public static function resolveAssignment(Fault $fault): void
    {
        FaultAssignment::resolveForFault($fault->id);
    }

    public static function isOffHours(Carbon $when = null): bool
    {
        $when = $when ?: now();
        $settings = AutoAssignSetting::query()->first();

        // Fallback defaults requested: 16:30 start, 06:00 end
        $standbyStart = '16:30:00';
        $standbyEnd = '06:00:00';
        $weekendEnabled = true;
        if ($settings) {
            $standbyStart = $settings->standby_start_time ?? $standbyStart;
            $standbyEnd = $settings->standby_end_time ?? $standbyEnd;
            $weekendEnabled = (bool)$settings->weekend_standby_enabled;
        }

        // Weekend standby enabled? Weekend is treated as 24h off-hours
        if ($weekendEnabled && $when->isWeekend()) {
            return true;
        }

        // Weekday logic: off-hours outside 06:00–16:30 (or configured window)
        $start = Carbon::parse($standbyStart, $when->timezone);
        $end = Carbon::parse($standbyEnd, $when->timezone);
        // Normalize to today's date
        $start->setDate($when->year, $when->month, $when->day);
        $end->setDate($when->year, $when->month, $when->day);

        // If the window spans overnight (start > end), off-hours when time >= start OR < end
        if ($start->gt($end)) {
            return $when->greaterThanOrEqualTo($start) || $when->lessThan($end);
        }
        // Otherwise off-hours when between start and end same day
        return $when->betweenIncluded($start, $end);
    }

    protected static function nocClearedId(): int
    {
        static $cachedId = null;
        if ($cachedId === null) {
            $cachedId = (int)(Status::where('status_code', 'CLN')->value('id') ?? 6);
        }
        return $cachedId;
    }
}