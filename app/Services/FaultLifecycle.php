<?php

namespace App\Services;

use App\Models\Fault;
use App\Models\FaultStageLog;
use App\Models\FaultAssignment;
use App\Models\Status;
use App\Models\AutoAssignSetting;
use Illuminate\Support\Carbon;
use App\Jobs\SendInfobipMessage;
use App\Models\User;
use App\Models\Section;
use App\Models\FaultSection;
use App\Models\City;
use App\Models\Position;
use App\Models\Link;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendInfobipTemplateMessage;

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

        // Dispatch Infobip notifications for lifecycle changes
        self::notifyStatusChange($fault, $toStatusId);
    }

    public static function startAssignment(Fault $fault, int $userId, ?int $actorUserId = null, bool $isStandby = false, ?string $region = null): void
    {
        FaultAssignment::start($fault->id, $userId, $actorUserId, $isStandby, $region);

        // Notify assigned technician
        $assigned = User::find($userId);
        if ($assigned && $assigned->phonenumber) {
            Log::info("Infobip: Fault {$fault->fault_ref_number} assigned to technician", [
                'technician_id' => $userId,
                'technician_name' => $assigned->name ?? 'Unknown',
                'phone' => $assigned->phonenumber,
                'is_standby' => $isStandby
            ]);
            $summary = self::faultSummary($fault);
            $text = "Fault {$fault->fault_ref_number}: Technician assigned\n{$summary}";
            SendInfobipMessage::dispatch([$assigned->phonenumber], $text);
        } else {
            Log::warning("Infobip: Cannot notify assigned technician - no phone number", [
                'fault_ref' => $fault->fault_ref_number,
                'technician_id' => $userId,
                'technician_name' => $assigned->name ?? 'Unknown'
            ]);
        }
    }

    public static function resolveAssignment(Fault $fault): void
    {
        FaultAssignment::resolveForFault($fault->id);
    }

    /**
     * Reopen the most recent assignment window for the given fault so timing continues.
     */
    public static function reopenAssignment(Fault $fault): void
    {
        FaultAssignment::reopenForFault($fault->id);
    }

    /**
     * End the current stage and reopen the previous stage record for the given status.
     * If there is no previous stage, starts a new one.
     */
    public static function reopenStageForStatus(Fault $fault, int $statusId, ?int $actorUserId = null): void
    {
        // Close any currently open stage (e.g., Technician Cleared)
        FaultStageLog::endStage($fault->id, $actorUserId);
        // Attempt to reopen the last stage for the target status
        FaultStageLog::reopenLastForStatus($fault->id, $statusId);
        // If there is no previous stage of that status, start a fresh one
        $exists = FaultStageLog::where('fault_id', $fault->id)
            ->where('status_id', $statusId)
            ->whereNull('ended_at')
            ->exists();
        if (!$exists) {
            FaultStageLog::startStage($fault->id, $statusId, $actorUserId);
        }
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

    protected static function techClearedId(): int
    {
        static $cachedId = null;
        if ($cachedId === null) {
            $cachedId = (int)(Status::where('status_code', 'CLT')->value('id') ?? 4);
        }
        return $cachedId;
    }

    protected static function notifyStatusChange(Fault $fault, int $toStatusId): void
    {
        $desc = Status::find($toStatusId)->description ?? 'Status changed';
        $summary = self::faultSummary($fault);
        $text = "Fault {$fault->fault_ref_number}: {$desc}\n{$summary}";

        // 1: Waiting for assessment -> customer notification only
        if ($toStatusId === 1) {
            Log::info("Infobip: Skipping NOC notifications; customer will be notified");
        }

        // 2: Assessed -> customer notification only
        if ($toStatusId === 2) {
            Log::info("Infobip: Skipping chief technician notifications; customer will be notified");
        }

        // Notify customer for key statuses (logged, assessed, resolved)
        self::notifyCustomerStatus($fault, $toStatusId, $text);

        // 3+ progression updates -> notify currently assigned technician if present
        if ($toStatusId === 3) {
            Log::info("Infobip: Fault {$fault->fault_ref_number} status updated to {$toStatusId}, notifying assigned technician");
            self::notifyAssignedTech($fault, $text);
        }
    }

    protected static function notifyAssignedTech(Fault $fault, string $text): void
    {
        if ($fault->assignedTo) {
            $assigned = User::find($fault->assignedTo);
            if ($assigned && $assigned->phonenumber) {
                Log::info("Infobip: Dispatching message to assigned technician", [
                    'fault_ref' => $fault->fault_ref_number,
                    'technician_id' => $assigned->id,
                    'technician_name' => $assigned->name ?? 'Unknown',
                    'phone' => $assigned->phonenumber
                ]);
                SendInfobipMessage::dispatch([$assigned->phonenumber], $text);
                // Customer notification: assignment update
                $customerPhones = [];
                if (!empty($fault->phoneNumber)) {
                    $customerPhones[] = $fault->phoneNumber;
                } elseif (!empty($fault->customer_id)) {
                    $customer = Customer::find($fault->customer_id);
                    if ($customer && !empty($customer->contact_number)) {
                        $customerPhones[] = $customer->contact_number;
                    }
                }

                if (!empty($customerPhones)) {
                    $templateName = env('INFOBIP_STATUS_TEMPLATE');
                    if (!empty($templateName)) {
                        SendInfobipTemplateMessage::dispatch(
                            $customerPhones,
                            $templateName,
                            'en',
                            [
                                $fault->fault_ref_number ?? '',
                                'Technician assigned: ' . ($assigned->name ?? ''),
                            ]
                        );
                        Log::info('Infobip: Customer notified (template) about assignment', [
                            'fault' => $fault->fault_ref_number,
                            'assigned_to' => $assigned->name ?? 'Unknown',
                        ]);
                    } else {
                        SendInfobipMessage::dispatch($customerPhones, $text);
                        Log::info('Infobip: Customer notified (text) about assignment', [
                            'fault' => $fault->fault_ref_number,
                            'assigned_to' => $assigned->name ?? 'Unknown',
                        ]);
                    }
                }
            } else {
                Log::warning("Infobip: Assigned technician has no phone number for fault {$fault->fault_ref_number}", [
                    'technician_id' => $fault->assignedTo,
                    'technician_name' => $assigned->name ?? 'Unknown'
                ]);
            }
        } else {
            Log::info("Infobip: No technician assigned to fault {$fault->fault_ref_number}");
        }
    }

    protected static function notifyCustomerStatus(Fault $fault, int $toStatusId, string $text): void
    {
        // Only send for: 1 (logged/waiting assessment), 2 (assessed), 3 (under rectification), 4 (cleared by technician)
        $shouldSend = in_array($toStatusId, [1, 2, 3, 4], true);
        if (!$shouldSend) {
            return;
        }

        $desc = Status::where('id', $toStatusId)->value('description') ?? 'Status updated';

        $customerPhones = [];
        if (!empty($fault->phoneNumber)) {
            $customerPhones[] = $fault->phoneNumber;
        } elseif (!empty($fault->customer_id)) {
            $customer = Customer::find($fault->customer_id);
            if ($customer && !empty($customer->contact_number)) {
                $customerPhones[] = $customer->contact_number;
            }
        }

        if (empty($customerPhones)) {
            Log::warning('Infobip: No customer phone found for status update', [
                'fault' => $fault->fault_ref_number,
                'toStatusId' => $toStatusId,
            ]);
            return;
        }

        $templateName = env('INFOBIP_STATUS_TEMPLATE');
        if (!empty($templateName)) {
            SendInfobipTemplateMessage::dispatch(
                $customerPhones,
                $templateName,
                'en',
                [
                    $fault->fault_ref_number ?? '',
                    $desc,
                ]
            );
            Log::info('Infobip: Customer notified (template) for status', [
                'fault' => $fault->fault_ref_number,
                'status' => $desc,
                'recipients' => $customerPhones,
            ]);
        } else {
            SendInfobipMessage::dispatch($customerPhones, $text);
            Log::info('Infobip: Customer notified (text) for status', [
                'fault' => $fault->fault_ref_number,
                'status' => $desc,
                'recipients' => $customerPhones,
            ]);
        }
    }

    protected static function faultSummary(Fault $fault): string
    {
        $customerModel = $fault->customer_id ? Customer::find($fault->customer_id) : null;
        $customer = $customerModel ? ($customerModel->customer ?? '') : '';
        $city = optional($fault->city)->city ?? '';
        $suburb = optional($fault->suburb)->suburb ?? '';
        $link = $fault->link_id ? Link::find($fault->link_id) : null;
        $linkName = $link ? ($link->link ?? '') : '';
        return trim("Customer: {$customer}\nCity/Suburb: {$city} / {$suburb}\nLink: {$linkName}");
    }
}