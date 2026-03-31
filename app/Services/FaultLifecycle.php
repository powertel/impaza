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
use App\Models\FaultReferral;
use Illuminate\Support\Facades\Mail;
use App\Notifications\SystemNotification;
use App\Services\ExpoPushService;

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

        if ($toStatusId === self::techClearedId() || $toStatusId === self::nocClearedId()) {
            self::cascadeUpdateChildConfirmedRfo($fault, $actorUserId);
        }

        if ($toStatusId === self::nocClearedId()) {
            self::cascadeResolvePopOutageChildFaults($fault, $actorUserId);
        }
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
            self::notifyUsers(
                collect([$assigned]),
                'Fault assigned',
                "Fault {$fault->fault_ref_number} assigned to you",
                ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'event' => 'assigned']
            );
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
        return $when->between($start, $end, true);
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
        static $cachedId = null;
        if ($cachedId === null) {
            $cachedId = (int)(Status::where('status_code', 'ESC')->value('id') ?? 10);
        }
        return $cachedId;
    }

    public static function managerEscalatedId(): int
    {
        static $cachedId = null;
        if ($cachedId === null) {
            $cachedId = (int)(Status::where('status_code', 'MES')->value('id') ?? 11);
        }
        return $cachedId;
    }

    public static function referredId(): int
    {
        static $cachedId = null;
        if ($cachedId === null) {
            $cachedId = (int)(Status::where('status_code', 'REF')->value('id') ?? 7);
        }
        return $cachedId;
    }

    protected static function notifyStatusChange(Fault $fault, int $toStatusId): void
    {
        $desc = Status::find($toStatusId)->description ?? 'Status changed';
        $summary = self::faultSummary($fault);
        $customerText = self::customerMessage($fault, $toStatusId);

        if ($toStatusId === 1 && empty($fault->root_fault_id)) {
            $nocSectionId = 1;
            $recipientIds = User::query()
                ->where('section_id', $nocSectionId)
                ->leftJoin('user_statuses', 'users.user_status', '=', 'user_statuses.id')
                ->where('user_statuses.id', '=', 1)
                ->pluck('users.id')
                ->all();
            $recipients = User::whereIn('id', $recipientIds)->get();

            $phoneRecipients = $recipients->pluck('phonenumber')->filter()->values()->all();
            if (empty($phoneRecipients)) {
                $nocRaw = env('POWERTEL_SMS_NOC_RECIPIENTS');
                $phoneRecipients = array_values(array_filter(array_map('trim', explode(',', (string)$nocRaw)), fn($x) => $x !== ''));
            }
            if (!empty($phoneRecipients)) {
                $nocText = self::nocMessage($fault, 1);
                $ok = app(SmsService::class)->send($phoneRecipients, $nocText);
                Log::info($ok ? 'Notify: NOC notified (SMS) for status 1' : 'Notify: NOC SMS failed for status 1', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $phoneRecipients,
                ]);
            }

            if ($recipients->isNotEmpty()) {
                self::notifyUsers(
                    $recipients,
                    'New fault logged',
                    "Fault {$fault->fault_ref_number} logged. Pending assessment.",
                    ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'status_id' => $toStatusId, 'event' => 'status_changed']
                );
            }
        }

        // 2: Assessed -> notify Chief Technicians in the fault's region
        if ($toStatusId === 2) {
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            
            // Query for recipients: Chief Technicians in the section/region
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('positions.position', '=', 'Chief Technician')
                ->whereNotNull('users.phonenumber');

            if ($sectionId > 0) {
                $query->where('users.section_id', '=', $sectionId);
            }
            if (in_array($sectionId, [2, 3], true) && !empty($region)) {
                $query->where('users.region', '=', $region);
            }

            $recipientIds = (clone $query)->pluck('users.id')->all();
            $recipients = User::whereIn('id', $recipientIds)->get();
            $phones = $recipients->pluck('phonenumber')->filter()->values()->all();

            // Fallback: if no regional Chief Technician found, try all in that section
            if (empty($phones) && !empty($region)) {
                Log::info("Notify: No regional Chief Technician found for SMS assessed alert to {$region}, searching all in section {$sectionId}");
                $recipientIds = User::query()
                    ->join('positions','users.position_id','=','positions.id')
                    ->where('positions.position', '=', 'Chief Technician')
                    ->where('users.section_id', '=', $sectionId)
                    ->whereNotNull('users.phonenumber')
                    ->pluck('users.id')
                    ->all();
                $recipients = User::whereIn('id', $recipientIds)->get();
                $phones = $recipients->pluck('phonenumber')->filter()->values()->all();
            }

            if (empty($phones)) {
                $fallback = env('POWERTEL_SMS_CT_RECIPIENTS');
                $phones = array_values(array_filter(array_map('trim', explode(',', (string)$fallback)), fn($x) => $x !== ''));
            }
            if (!empty($phones)) {
                $text = "Assessment: Fault {$fault->fault_ref_number} has been assessed. Please review and proceed with rectification.";
                $ok = app(SmsService::class)->send($phones, $text);
                Log::info($ok ? 'Notify: Chief Technicians notified (SMS) for status 2' : 'Notify: Chief Technicians SMS failed for status 2', [
                    'ok' => $ok,
                    'fault' => $fault->fault_ref_number,
                    'recipients' => $phones,
                    'region' => $region,
                    'section_id' => $sectionId,
                ]);
            } else {
                Log::warning('Notify: No Chief Technicians found for assessed fault', [
                    'fault' => $fault->fault_ref_number,
                    'region' => $region,
                    'section_id' => $sectionId,
                ]);
            }

            if ($recipients->isNotEmpty()) {
                self::notifyUsers(
                    $recipients,
                    'Fault assessed',
                    "Fault {$fault->fault_ref_number} has been assessed. Please review.",
                    ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'status_id' => $toStatusId, 'event' => 'status_changed']
                );
            }
        }

        if ($toStatusId === 4) {
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $sectionName = $sectionId ? (Section::find($sectionId)->section ?? 'Section') : 'Section';
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            $ctQuery = User::query()
                ->join('positions', 'users.position_id', '=', 'positions.id');

            if ($sectionId === 1) {
                $ctQuery->where('positions.position', '=', 'Network Controller');
            } else {
                $ctQuery->where('positions.position', '=', 'Chief Technician');
            }

            if ($sectionId > 0) {
                $ctQuery->where('users.section_id', '=', $sectionId);
            }
            if (in_array($sectionId, [2, 3], true) && !empty($region)) {
                $ctQuery->where('users.region', '=', $region);
            }

            $ctIds = (clone $ctQuery)->pluck('users.id')->all();
            $ctUsers = User::whereIn('id', $ctIds)->get();

            if ($ctUsers->isEmpty() && !empty($region)) {
                $ctQuery = User::query()
                    ->join('positions', 'users.position_id', '=', 'positions.id');
                if ($sectionId === 1) {
                    $ctQuery->where('positions.position', '=', 'Network Controller');
                } else {
                    $ctQuery->where('positions.position', '=', 'Chief Technician');
                }
                if ($sectionId > 0) {
                    $ctQuery->where('users.section_id', '=', $sectionId);
                }
                $ctIds = (clone $ctQuery)->pluck('users.id')->all();
                $ctUsers = User::whereIn('id', $ctIds)->get();
            }

            $nocIds = User::query()
                ->where('section_id', 1)
                ->leftJoin('user_statuses', 'users.user_status', '=', 'user_statuses.id')
                ->where('user_statuses.id', '=', 1)
                ->pluck('users.id')
                ->all();
            $nocUsers = User::whereIn('id', $nocIds)->get();

            $ceUsers = User::query()
                ->where('section_id', 6)
                ->get();

            $recipients = $ctUsers->concat($nocUsers)->concat($ceUsers)->unique('id')->values();

            if ($recipients->isNotEmpty()) {
                self::notifyUsers(
                    $recipients,
                    'Fault rectified',
                    "Fault {$fault->fault_ref_number} was rectified by {$sectionName}.",
                    ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'status_id' => $toStatusId, 'event' => 'status_changed']
                );
            }
        }

        // Notify customer for key statuses (logged, assessed, resolved)
        self::notifyCustomerStatus($fault, $toStatusId, $customerText);

        // Cleared -> notify Power Call Centre
        if ($toStatusId === self::nocClearedId() && empty($fault->root_fault_id)) {
            self::sendClearedEmail($fault);
        }

        if ($toStatusId === self::referredId()) {
            self::sendReferralEmail($fault);
        }

        // Escalations -> notify appropriate recipients
        if ($toStatusId === self::escalatedId()) {
            self::sendEscalationEmail($fault, 'Chief Technician');
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id');

            if ($sectionId === 1) {
                $query->where('positions.position', '=', 'Network Controller');
            } else {
                $query->where('positions.position', '=', 'Chief Technician');
            }

            if ($sectionId > 0) {
                $query->where('users.section_id', '=', $sectionId);
            }
            if (in_array($sectionId, [2, 3], true) && !empty($region)) {
                $query->where('users.region', '=', $region);
            }
            $recipientIds = (clone $query)->pluck('users.id')->all();
            $recipients = User::whereIn('id', $recipientIds)->get();

            if ($recipients->isEmpty() && !empty($region)) {
                $query = User::query()
                    ->join('positions','users.position_id','=','positions.id');
                if ($sectionId === 1) {
                    $query->where('positions.position', '=', 'Network Controller');
                } else {
                    $query->where('positions.position', '=', 'Chief Technician');
                }
                if ($sectionId > 0) {
                    $query->where('users.section_id', '=', $sectionId);
                }
                $recipientIds = (clone $query)->pluck('users.id')->all();
                $recipients = User::whereIn('id', $recipientIds)->get();
            }

            if ($recipients->isEmpty()) {
                $query = User::query()
                    ->join('positions','users.position_id','=','positions.id')
                    ->where('positions.position', '=', 'Chief Technician')
                    ->whereIn('users.section_id', [2, 3]);

                if (!empty($region)) {
                    $query->where('users.region', '=', $region);
                }

                $recipientIds = (clone $query)->pluck('users.id')->all();
                $recipients = User::whereIn('id', $recipientIds)->get();

                if ($recipients->isEmpty() && !empty($region)) {
                    $recipientIds = User::query()
                        ->join('positions','users.position_id','=','positions.id')
                        ->where('positions.position', '=', 'Chief Technician')
                        ->whereIn('users.section_id', [2, 3])
                        ->pluck('users.id')
                        ->all();
                    $recipients = User::whereIn('id', $recipientIds)->get();
                }
            }

            if ($recipients->isNotEmpty()) {
                self::notifyUsers(
                    $recipients,
                    'Fault escalated',
                    "Fault {$fault->fault_ref_number} has been escalated by technician for review.",
                    ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'status_id' => $toStatusId, 'event' => 'status_changed']
                );
            }
        } elseif ($toStatusId === self::managerEscalatedId()) {
            self::sendEscalationEmail($fault, 'Manager');
            $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->whereIn('positions.position', ['Manager','Technical Manager']);
            if ($sectionId > 0) {
                $query->where('users.section_id', '=', $sectionId);
            }
            $recipientIds = (clone $query)->pluck('users.id')->all();
            $recipients = User::whereIn('id', $recipientIds)->get();

            if ($recipients->isNotEmpty()) {
                self::notifyUsers(
                    $recipients,
                    'Fault escalated to Manager',
                    "Fault {$fault->fault_ref_number} has been escalated to Manager for intervention.",
                    ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'status_id' => $toStatusId, 'event' => 'status_changed']
                );
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

    protected static function cascadeResolvePopOutageChildFaults(Fault $fault, ?int $actorUserId = null): void
    {
        if (!empty($fault->root_fault_id)) {
            return;
        }

        $isAggregator = (bool) (Customer::query()
            ->where('id', (int) $fault->customer_id)
            ->value('is_pop_aggregator') ?? false);
        if (!$isAggregator) {
            return;
        }

        $nocClearedId = self::nocClearedId();
        $childFaults = Fault::query()
            ->where('root_fault_id', $fault->id)
            ->where('status_id', '!=', $nocClearedId)
            ->get();

        if ($childFaults->isEmpty()) {
            return;
        }

        $remarkActivityId = (int) (\DB::table('remark_activities')
            ->where('activity', '=', 'ON NOC CLEAR')
            ->value('id') ?? 0);

        if ($remarkActivityId === 0) {
            $remarkActivityId = (int) (\DB::table('remark_activities')->orderBy('id')->value('id') ?? 0);
        }

        foreach ($childFaults as $child) {
            $childUpdate = ['status_id' => $nocClearedId];
            if (!empty($fault->confirmedRfo_id)) {
                $childUpdate['confirmedRfo_id'] = (int) $fault->confirmedRfo_id;
            }
            $child->update($childUpdate);
            self::recordStatusChange($child, $nocClearedId, $actorUserId);

            $remarkUserId = (int) ($actorUserId ?? 0);
            if ($remarkActivityId !== 0 && $remarkUserId > 0) {
                \DB::table('remarks')->insert([
                    'fault_id' => $child->id,
                    'user_id' => $remarkUserId,
                    'remark' => "Resolved automatically because POP fault {$fault->fault_ref_number} was cleared by NOC.",
                    'remarkActivity_id' => $remarkActivityId,
                    'file_path' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected static function cascadeUpdateChildConfirmedRfo(Fault $fault, ?int $actorUserId = null): void
    {
        if (!empty($fault->root_fault_id)) {
            return;
        }
        $isAggregator = (bool) (Customer::query()
            ->where('id', (int) $fault->customer_id)
            ->value('is_pop_aggregator') ?? false);
        if (!$isAggregator) {
            return;
        }
        $confirmed = (int) ($fault->confirmedRfo_id ?? 0);
        if ($confirmed <= 0) {
            return;
        }

        $childFaults = Fault::query()
            ->where('root_fault_id', $fault->id)
            ->get();

        if ($childFaults->isEmpty()) {
            return;
        }

        $remarkActivityId = (int) (\DB::table('remark_activities')
            ->where('activity', '=', 'ON NOC CLEAR')
            ->value('id') ?? 0);
        if ($remarkActivityId === 0) {
            $remarkActivityId = (int) (\DB::table('remark_activities')->orderBy('id')->value('id') ?? 0);
        }
        $remarkUserId = (int) ($actorUserId ?? 0);

        foreach ($childFaults as $child) {
            if ((int) ($child->confirmedRfo_id ?? 0) !== $confirmed) {
                $child->update(['confirmedRfo_id' => $confirmed]);
                if ($remarkActivityId !== 0 && $remarkUserId > 0) {
                    \DB::table('remarks')->insert([
                        'fault_id' => $child->id,
                        'user_id' => $remarkUserId,
                        'remark' => "Confirmed RFO updated to match POP fault {$fault->fault_ref_number}.",
                        'remarkActivity_id' => $remarkActivityId,
                        'file_path' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
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
        // Abbreviated labels to save space (SMS limit 160 chars)
        return trim("Cust: {$customer}\nLoc: {$city}/{$suburb}\nLnk: {$linkName}");
    }

    protected static function customerMessage(Fault $fault, int $toStatusId): string
    {
        if ($toStatusId === 1) {
            return "Good Day we have acknowledged the receipt of your fault {$fault->fault_ref_number}. We are working on it.";
        }

       /*  if ($toStatusId === 6) {
            return "Good news: Fault {$fault->fault_ref_number} was resolved by our team. If you still experience issues, please contact us.";
        } */
        return "";
    }

    protected static function nocMessage(Fault $fault, int $toStatusId): string
    {
        $summary = self::faultSummary($fault);
        if ($toStatusId === 1) {
            return "New fault {$fault->fault_ref_number} logged. Pending assessment.\n{$summary}";
        }
        return "";
    }

    protected static function techAssignmentMessage(Fault $fault, User $tech): string
    {
        $summary = self::faultSummary($fault);
        return "Assign: Fault {$fault->fault_ref_number} assigned to you.\n{$summary}";
    }

    protected static function techStatusMessage(Fault $fault, User $tech, int $toStatusId): string
    {
        $summary = self::faultSummary($fault);
        if ($toStatusId === 3) {
            return "Update: Fault {$fault->fault_ref_number} under rectification.\n{$summary}";
        }
        /* return "Fault {$fault->fault_ref_number} status updated.\n{$summary}"; */
        return "";
    }

    protected static function notifyUsers($users, string $title, string $body, array $payload = []): void
    {
        $collection = collect($users)->filter(function ($u) {
            return $u instanceof User;
        })->unique('id')->values();

        foreach ($collection as $u) {
            try {
                $u->notify(new SystemNotification($title, $body, $payload));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        try {
            $pushTitle = 'iMpazamon';
            $pushBody = trim($title . ': ' . $body);
            app(ExpoPushService::class)->sendToUsers($collection, $pushTitle, $pushBody, $payload);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected static function sendClearedEmail(Fault $fault): void
    {
        $to = 'powercallcentre@powertel.co.zw';
        $subject = "Fault Cleared: {$fault->fault_ref_number}";
        
        $customerModel = $fault->customer_id ? Customer::find($fault->customer_id) : null;
        $customerName = $customerModel ? ($customerModel->customer ?? 'N/A') : 'N/A';
        $rfo = $fault->confirmedrfo ? $fault->confirmedrfo->RFO : ($fault->suspectedrfo ? $fault->suspectedrfo->RFO : 'N/A');
        
        $data = [
            'fault_ref' => $fault->fault_ref_number,
            'customer' => $customerName,
            'service_type' => $fault->serviceType,
            'rfo' => $rfo,
            'cleared_at' => now()->toDateTimeString(),
        ];

        try {
            Mail::send('emails.fault_cleared', $data, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
            Log::info("Notify: Clearance email sent to Power Call Centre for fault {$fault->fault_ref_number}");
        } catch (\Exception $e) {
            Log::error("Notify: Error sending clearance email via SMTP: " . $e->getMessage());
        }

        $ceUsers = User::query()
            ->where('section_id', 6)
            ->get();
        if ($ceUsers->isNotEmpty()) {
            self::notifyUsers(
                $ceUsers,
                'Fault cleared',
                "Fault {$fault->fault_ref_number} cleared. Customer: {$customerName}.",
                ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'event' => 'cleared', 'section_id' => 6]
            );
        }
    }

    protected static function sendReferralEmail(Fault $fault): void
    {
        $referral = FaultReferral::where('fault_id', $fault->id)
            ->whereNull('completed_at')
            ->orderBy('id', 'desc')
            ->first();

        if (!$referral) {
            Log::warning("Notify: No active referral found for fault {$fault->fault_ref_number}");
            return;
        }

        $toSection = Section::find($referral->to_section_id);
        $fromSection = Section::find($referral->from_section_id);
        $referredBy = User::find($referral->referred_by);
        $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;

        $query = User::query()
            ->join('positions','users.position_id','=','positions.id')
            ->where('users.section_id', $referral->to_section_id)
            ->whereNotNull('users.email');

        if ((int)$referral->to_section_id === 1) {
            $query->where('positions.position', '=', 'Network Controller');
        } else {
            $query->where('positions.position', '=', 'Chief Technician');
        }

        if (in_array((int)$referral->to_section_id, [2, 3], true) && !empty($region)) {
            $query->where('users.region', '=', $region);
        }

        $recipientIds = (clone $query)->pluck('users.id')->all();
        $recipientUsers = User::whereIn('id', $recipientIds)->get();
        $recipients = $recipientUsers->pluck('email')->filter()->values()->all();

        // Fallback: if no regional supervisors found, try all supervisors in that section
        if (empty($recipients) && !empty($region)) {
            Log::info("Notify: No regional supervisors found for referral to {$region}, searching all in section {$referral->to_section_id}");
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('users.section_id', $referral->to_section_id)
                ->whereNotNull('users.email');

            if ((int)$referral->to_section_id === 1) {
                $query->where('positions.position', '=', 'Network Controller');
            } else {
                $query->where('positions.position', '=', 'Chief Technician');
            }
            $recipientIds = (clone $query)->pluck('users.id')->all();
            $recipientUsers = User::whereIn('id', $recipientIds)->get();
            $recipients = $recipientUsers->pluck('email')->filter()->values()->all();
        }

        if (empty($recipients)) {
            Log::warning("Notify: No email recipients found for referral to section {$referral->to_section_id}. Region: " . ($region ?? 'N/A'));
            return;
        }

        $customerModel = $fault->customer_id ? Customer::find($fault->customer_id) : null;
        $customerName = $customerModel ? ($customerModel->customer ?? 'N/A') : 'N/A';

        $data = [
            'fault_ref' => $fault->fault_ref_number,
            'customer' => $customerName,
            'service_type' => $fault->serviceType,
            'from_section' => $fromSection->section ?? 'Unknown',
            'to_section' => $toSection->section ?? 'Unknown',
            'referred_by' => $referredBy->name ?? 'Unknown',
            'referred_at' => $referral->started_at ?? now()->toDateTimeString(),
            'remark' => $referral->work_note,
        ];

        $subject = "Fault Referred to Your Section: {$fault->fault_ref_number}";

        try {
            Mail::send('emails.fault_referred', $data, function ($message) use ($recipients, $subject) {
                $message->to($recipients)
                        ->subject($subject);
            });
            Log::info("Notify: Referral email sent to section {$referral->to_section_id} for fault {$fault->fault_ref_number}");
        } catch (\Exception $e) {
            Log::error("Notify: Error sending referral email: " . $e->getMessage());
        }

        if (!empty($recipientUsers) && $recipientUsers->isNotEmpty()) {
            self::notifyUsers(
                $recipientUsers,
                'Fault referred',
                "Fault {$fault->fault_ref_number} referred to {$toSection->section}.",
                ['fault_id' => $fault->id, 'fault_ref' => $fault->fault_ref_number, 'event' => 'referred', 'to_section_id' => (int) $referral->to_section_id]
            );
        }
    }

    protected static function sendEscalationEmail(Fault $fault, string $type): void
    {
        $sectionId = (int) (FaultSection::where('fault_id', $fault->id)->value('section_id') ?? 0);
        $region = $fault->city_id ? (City::find($fault->city_id)->region ?? null) : null;

        $query = User::query()
            ->join('positions','users.position_id','=','positions.id')
            ->whereNotNull('users.email');

        if ($type === 'Chief Technician') {
            if ($sectionId === 1) {
                $query->where('positions.position', '=', 'Network Controller');
            } else {
                $query->where('positions.position', '=', 'Chief Technician');
            }
            
            if (in_array($sectionId, [2, 3], true) && !empty($region)) {
                $query->where('users.region', '=', $region);
            }
        } else {
            $query->whereIn('positions.position', ['Manager', 'Technical Manager']);
        }

        if ($sectionId > 0) {
            $query->where('users.section_id', '=', $sectionId);
        }

        $recipients = $query->pluck('users.email')->all();

        // If no recipients found for Chief Technician with region, try without region
        if (empty($recipients) && $type === 'Chief Technician' && in_array($sectionId, [2, 3], true) && !empty($region)) {
            Log::info("Notify: No regional Chief Technician found for {$region}, searching all Chief Technicians in section {$sectionId}");
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('positions.position', '=', 'Chief Technician')
                ->where('users.section_id', '=', $sectionId)
                ->whereNotNull('users.email');
            $recipients = $query->pluck('users.email')->all();
        }

        // Final fallback for Chief Technician: search across all technical sections (2, 3) if still empty
        if (empty($recipients) && $type === 'Chief Technician') {
            Log::info("Notify: No Chief Technician found for section {$sectionId}, searching across technical sections (NOC/Projects)");
            $query = User::query()
                ->join('positions','users.position_id','=','positions.id')
                ->where('positions.position', '=', 'Chief Technician')
                ->whereIn('users.section_id', [2, 3])
                ->whereNotNull('users.email');
            
            if (!empty($region)) {
                $query->where('users.region', '=', $region);
            }
            
            $recipients = $query->pluck('users.email')->all();
            
            // If still empty and we used region, try without region
            if (empty($recipients) && !empty($region)) {
                $recipients = User::query()
                    ->join('positions','users.position_id','=','positions.id')
                    ->where('positions.position', '=', 'Chief Technician')
                    ->whereIn('users.section_id', [2, 3])
                    ->whereNotNull('users.email')
                    ->pluck('users.email')
                    ->all();
            }
        }

        if (empty($recipients)) {
            Log::warning("Notify: No email recipients found for {$type} escalation of fault {$fault->fault_ref_number}. Section: {$sectionId}, Region: " . ($region ?? 'N/A'));
            return;
        }

        $customerModel = $fault->customer_id ? Customer::find($fault->customer_id) : null;
        $customerName = $customerModel ? ($customerModel->customer ?? 'N/A') : 'N/A';

        $data = [
            'fault_ref' => $fault->fault_ref_number,
            'customer' => $customerName,
            'service_type' => $fault->serviceType,
            'escalation_type' => $type,
            'escalated_at' => now()->toDateTimeString(),
        ];

        $subject = "Fault Escalated: {$fault->fault_ref_number} ({$type})";

        try {
            Mail::send('emails.fault_escalated', $data, function ($message) use ($recipients, $subject) {
                $message->to($recipients)
                        ->subject($subject);
            });
            Log::info("Notify: Escalation email sent to {$type}s for fault {$fault->fault_ref_number}");
        } catch (\Exception $e) {
            Log::error("Notify: Error sending escalation email: " . $e->getMessage());
        }
    }
}
