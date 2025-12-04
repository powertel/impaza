<?php

namespace App\Services;

use App\Models\Fault;
use App\Models\FaultStageLog;
use App\Models\FaultAssignment;
use App\Models\Status;
use App\Models\AutoAssignSetting;
use Illuminate\Support\Carbon;
use App\Services\SmsService;
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
            Log::info("Notify: Fault {$fault->fault_ref_number} assigned to technician", [
                'technician_id' => $userId,
                'technician_name' => $assigned->name ?? 'Unknown',
                'phone' => $assigned->phonenumber,
                'is_standby' => $isStandby
            ]);
            $text = self::techAssignmentMessage($fault, $assigned);
            $ok = app(SmsService::class)->send([$assigned->phonenumber], $text);
            Log::info($ok ? 'Notify: SMS sent to assigned technician' : 'Notify: SMS failed to assigned technician', [
                'ok' => $ok,
                'phone' => $assigned->phonenumber,
            ]);
        } else {
            Log::warning("Notify: Cannot notify assigned technician - no phone number", [
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
        $priorSeconds = (int) \DB::table('fault_stage_logs')
            ->where('fault_id', $fault->id)
            ->where('status_id', $statusId)
            ->whereNotNull('ended_at')
            ->sum('duration_seconds');

        // Attempt to reopen the last stage for the target status
        FaultStageLog::reopenLastForStatus($fault->id, $statusId);

        // If there is no previous stage of that status, start a fresh one
        $open = FaultStageLog::where('fault_id', $fault->id)
            ->where('status_id', $statusId)
            ->whereNull('ended_at')
            ->orderByDesc('started_at')
            ->first();
        if (!$open) {
            FaultStageLog::startStage($fault->id, $statusId, $actorUserId);
        } else {
            if ($priorSeconds > 0) {
                $open->started_at = now()->subSeconds($priorSeconds);
                $open->save();
            }
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

    public static function escalatedId(): int
    {
        return 10;
    }

    public static function managerEscalatedId(): int
    {
        return 11;
    }

    protected static function notifyStatusChange(Fault $fault, int $toStatusId): void
    {
        $desc = Status::find($toStatusId)->description ?? 'Status changed';
        $summary = self::faultSummary($fault);
        $customerText = self::customerMessage($fault, $toStatusId);

        if ($toStatusId === 1) {
            $nocSectionId = 1;
            $recipients = User::query()
                ->where('section_id', $nocSectionId)
                ->leftJoin('user_statuses', 'users.user_status', '=', 'user_statuses.id')
                ->where('user_statuses.id', '=', 1)
                ->whereNotNull('users.phonenumber')
                ->pluck('users.phonenumber')
                ->all();
            if (empty($recipients)) {
                $nocRaw = env('POWERTEL_SMS_NOC_RECIPIENTS');
                $recipients = array_values(array_filter(array_map('trim', explode(',', (string)$nocRaw)), fn($x) => $x !== ''));
            }
            if (!empty($recipients)) {
                $nocText = self::nocMessage($fault, 1);
                $ok = app(SmsService::class)->send($recipients, $nocText);
                Log::info($ok ? 'Notify: NOC notified (SMS) for status 1' : 'Notify: NOC SMS failed for status 1', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $recipients,
                ]);
            }
        }

        // 2: Assessed -> notify Chief Technicians in the fault's region
        if ($toStatusId === 2) {
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('positions.position', '=', 'Chief Technician')
                ->whereNotNull('users.phonenumber');
            if (!empty($region)) {
                $query->where('users.region', '=', $region);
            }
            $recipients = $query->pluck('users.phonenumber')->all();
            if (empty($recipients)) {
                $fallback = env('POWERTEL_SMS_CT_RECIPIENTS');
                $recipients = array_values(array_filter(array_map('trim', explode(',', (string)$fallback)), fn($x) => $x !== ''));
            }
            if (!empty($recipients)) {
                $text = "Assessment: Fault {$fault->fault_ref_number} has been assessed. Please review and proceed with rectification.";
                $ok = app(SmsService::class)->send($recipients, $text);
                Log::info($ok ? 'Notify: Chief Technicians notified (SMS) for status 2' : 'Notify: Chief Technicians SMS failed for status 2', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $recipients,
                    'region' => $region,
                ]);
            } else {
                Log::warning('Notify: No Chief Technicians found for assessed fault', [
                    'fault' => $fault->fault_ref_number,
                    'region' => $region,
                ]);
            }
        }

        if ($toStatusId === 4) {
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $sectionName = $sectionId ? (Section::find($sectionId)->section ?? 'Section') : 'Section';
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('positions.position', '=', 'Chief Technician')
                ->whereNotNull('users.phonenumber');
            if ($sectionId > 0) {
                $query->where('users.section_id', '=', $sectionId);
            }
            if (in_array($sectionId, [2,3], true) && !empty($region)) {
                $query->where('users.region', '=', $region);
            }
            $recipients = $query->pluck('users.phonenumber')->all();
            if (empty($recipients)) {
                $fallback = env('POWERTEL_SMS_CT_RECIPIENTS');
                $recipients = array_values(array_filter(array_map('trim', explode(',', (string)$fallback)), fn($x) => $x !== ''));
            }
            if (!empty($recipients)) {
                $text = "Rectification: Fault {$fault->fault_ref_number} was rectified for {$sectionName}.";
                $ok = app(SmsService::class)->send($recipients, $text);
                Log::info($ok ? 'Notify: Chief Technicians notified (SMS) for status 4' : 'Notify: Chief Technicians SMS failed for status 4', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $recipients,
                    'section_id' => $sectionId,
                    'region' => $region,
                ]);
            } else {
                Log::warning('Notify: No Chief Technicians found for rectified fault', [
                    'fault' => $fault->fault_ref_number,
                    'section_id' => $sectionId,
                    'region' => $region,
                ]);
            }
        }

        // Notify customer for key statuses (logged, assessed, resolved)
        self::notifyCustomerStatus($fault, $toStatusId, $customerText);

        // Escalations -> notify appropriate recipients
        if ($toStatusId === self::escalatedId()) {
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('positions.position', '=', 'Chief Technician')
                ->whereNotNull('users.phonenumber');
            if ($sectionId > 0) {
                $query->where('users.section_id', '=', $sectionId);
            }
            if (in_array($sectionId, [2,3], true) && !empty($region)) {
                $query->where('users.region', '=', $region);
            }
            $recipients = $query->pluck('users.phonenumber')->all();
            if (!empty($recipients)) {
                $text = "Escalation: Fault {$fault->fault_ref_number} has been escalated by technician for review.";
                $ok = app(SmsService::class)->send($recipients, $text);
                Log::info($ok ? 'Notify: Chief Technicians notified (SMS) for escalation' : 'Notify: Chief Technicians SMS failed for escalation', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $recipients,
                    'section_id' => $sectionId,
                    'region' => $region,
                ]);
            }
        } elseif ($toStatusId === self::managerEscalatedId()) {
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->whereIn('positions.position', ['Manager','Technical Manager'])
                ->whereNotNull('users.phonenumber');
            if ($sectionId > 0) {
                $query->where('users.section_id', '=', $sectionId);
            }
            if (in_array($sectionId, [2,3], true) && !empty($region)) {
                $query->where('users.region', '=', $region);
            }
            $recipients = $query->pluck('users.phonenumber')->all();
            if (!empty($recipients)) {
                $text = "Escalation: Fault {$fault->fault_ref_number} has been escalated to Manager for intervention.";
                $ok = app(SmsService::class)->send($recipients, $text);
                Log::info($ok ? 'Notify: Managers notified (SMS) for escalation' : 'Notify: Managers SMS failed for escalation', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $recipients,
                    'section_id' => $sectionId,
                    'region' => $region,
                ]);
            }
        }

        // 3+ progression updates -> notify currently assigned technician if present
        /* if ($toStatusId === 3) {
            Log::info("Notify: Fault {$fault->fault_ref_number} status updated to {$toStatusId}, notifying assigned technician");
            $assigned = $fault->assignedTo ? User::find($fault->assignedTo) : null;
            $techText = $assigned ? self::techStatusMessage($fault, $assigned, $toStatusId) : "Fault {$fault->fault_ref_number}: {$desc}\n{$summary}";
            self::notifyAssignedTech($fault, $techText);
        } */
    }

    protected static function notifyAssignedTech(Fault $fault, string $text): void
    {
        if ($fault->assignedTo) {
            $assigned = User::find($fault->assignedTo);
            if ($assigned && $assigned->phonenumber) {
                Log::info("Notify: Dispatching SMS to assigned technician", [
                    'fault_ref' => $fault->fault_ref_number,
                    'technician_id' => $assigned->id,
                    'technician_name' => $assigned->name ?? 'Unknown',
                    'phone' => $assigned->phonenumber
                ]);
                $okTech = app(SmsService::class)->send([$assigned->phonenumber], $text);
                Log::info($okTech ? 'Notify: SMS sent to assigned technician' : 'Notify: SMS failed to assigned technician', [
                    'ok' => $okTech,
                    'phone' => $assigned->phonenumber,
                ]);
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

               /*  if (!empty($customerPhones)) {
                    $custText = self::customerMessage($fault, 3);
                    $okCust = app(SmsService::class)->send($customerPhones, $custText);
                    Log::info($okCust ? 'Notify: Customer notified (SMS) about assignment' : 'Notify: Customer SMS failed for assignment', [
                        'ok' => $okCust,
                        'fault' => $fault->fault_ref_number,
                        'assigned_to' => $assigned->name ?? 'Unknown',
                    ]);
                } */
            } else {
                Log::warning("Notify: Assigned technician has no phone number for fault {$fault->fault_ref_number}", [
                    'technician_id' => $fault->assignedTo,
                    'technician_name' => $assigned->name ?? 'Unknown'
                ]);
            }
        } else {
            Log::info("Notify: No technician assigned to fault {$fault->fault_ref_number}");
        }
    }

    protected static function notifyCustomerStatus(Fault $fault, int $toStatusId, string $text): void
    {
        // Only send for: 1 (logged/waiting assessment), 2 (assessed), 3 (under rectification), 4 (cleared by technician)
        $shouldSend = in_array($toStatusId, [1], true);
        if (!$shouldSend) {
            return;
        }
        if (trim($text) === '') {
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

        $ok = app(SmsService::class)->send($customerPhones, $text);
        Log::info($ok ? 'Notify: Customer notified (SMS) for status' : 'Notify: Customer SMS failed for status', [
            'ok' => $ok,
            'fault' => $fault->fault_ref_number,
            'status' => $desc,
            'recipients' => $customerPhones,
        ]);
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

    protected static function customerMessage(Fault $fault, int $toStatusId): string
    {
        if ($toStatusId === 1) {
            return "Good Day we have acknowledged the receipt of your fault {$fault->fault_ref_number}. We are on it.";
        }
        /*         if ($toStatusId === 2) {
            return "Update: Fault {$fault->fault_ref_number} has been assessed. We are preparing rectification.";
        }
        if ($toStatusId === 3) {
            return "Good news: Rectification is underway for fault {$fault->fault_ref_number}. We will keep you updated.";
        } */
       /*  if ($toStatusId === 6) {
            return "Good news: Fault {$fault->fault_ref_number} was resolved by our team. If you still experience issues, please contact us.";
        } */
        return "";
    }

    protected static function nocMessage(Fault $fault, int $toStatusId): string
    {
        $summary = self::faultSummary($fault);
        if ($toStatusId === 1) {
            return "New fault logged {$fault->fault_ref_number}. Awaiting assessment.\n{$summary}";
        }
        return "";
    }

    protected static function techAssignmentMessage(Fault $fault, User $tech): string
    {
        $summary = self::faultSummary($fault);
        return "Assignment: You are assigned to fault {$fault->fault_ref_number}.\n{$summary}";
    }

    protected static function techStatusMessage(Fault $fault, User $tech, int $toStatusId): string
    {
        $summary = self::faultSummary($fault);
        if ($toStatusId === 3) {
            return "Update: Fault {$fault->fault_ref_number} is under rectification.\n{$summary}";
        }
        /* return "Fault {$fault->fault_ref_number} status updated.\n{$summary}"; */
    }
}
