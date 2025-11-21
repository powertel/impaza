<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Fault;
use App\Models\AutoAssignSetting;
use App\Services\FaultLifecycle;

class AutoAssignFaults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faults:auto-assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-assign assessed faults to available technicians by section (round-robin)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Gather all enabled scopes (supports multiple section/region rows)
        $settingsList = AutoAssignSetting::query()->where('auto_assign_enabled', true)->get();
        if ($settingsList->isEmpty()) {
            $this->info('Auto-assign disabled by Chief Tech.');
            return Command::SUCCESS;
        }

        $assignedCount = 0;
        foreach ($settingsList as $settings) {
            // Explicit scope fields take precedence; fallback to updater
            $scopeSectionId = $settings->scope_section_id ?? null;
            $scopeRegion = $settings->scope_region ?? null;
            if (empty($scopeSectionId) || (in_array((int)$scopeSectionId, [2,3], true) && empty($scopeRegion))) {
                $scopeUser = null;
                if (!empty($settings->updated_by)) {
                    $scopeUser = \App\Models\User::find((int)$settings->updated_by);
                }
                $scopeSectionId = $scopeSectionId ?: ($scopeUser->section_id ?? null);
                if (in_array((int)$scopeSectionId, [2,3], true)) {
                    $scopeRegion = $scopeRegion ?: ($scopeUser->region ?? null);
                } else {
                    $scopeRegion = null;
                }
            }

            // Find assessed faults with no technician yet assigned (for this scope)
            $faultsQuery = DB::table('faults')
                ->leftJoin('fault_section', 'faults.id', '=', 'fault_section.fault_id')
                ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                ->where('faults.status_id', '=', 2) // Fault has been assessed
                ->whereNull('faults.assignedTo');

            // Apply scoping if updater has section/region
            if (!empty($scopeSectionId)) {
                $faultsQuery->where('fault_section.section_id', '=', $scopeSectionId);
            }
            if (!empty($scopeRegion)) {
                $faultsQuery->where(function($q) use ($scopeRegion) {
                    $q->whereIn('fault_section.section_id', [2, 3])
                      ->where('cities.region', '=', $scopeRegion)
                      ->orWhereNotIn('fault_section.section_id', [2, 3]);
                });
            }

            $faults = $faultsQuery
                ->select(['faults.id', 'faults.city_id', 'fault_section.section_id'])
                ->get();

            if ($faults->isEmpty()) {
                continue;
            }

            // Fetch configurable settings once for this scope
            $considerRegion = (bool)($settings->consider_region ?? true);
            $considerLeave = (bool)($settings->consider_leave ?? true);
            $isOffHours = FaultLifecycle::isOffHours();
            $isWeekendOff = (bool)($settings->weekend_standby_enabled ?? true) && now()->isWeekend();

            foreach ($faults as $row) {
            if (!$row->section_id) { continue; }

            // Build candidate technician list for this section, preferring Standby during off-hours, otherwise Assignable.
            $acceptable = $isOffHours ? ['Standby', 'Assignable'] : ['Assignable'];
            $excluded = ['Unassignable', 'Away']; // Exclude business trip
            if ($considerLeave) {
                $excluded[] = 'On Leave';
            }

            // Determine fault region
            $faultRegion = DB::table('cities')->where('id', $row->city_id)->value('region');

            $query = DB::table('users')
                ->leftJoin('user_statuses', 'users.user_status', '=', 'user_statuses.id')
                ->where('users.section_id', '=', $row->section_id)
                ->whereIn('user_statuses.status_name', $acceptable)
                ->whereNotIn('user_statuses.status_name', $excluded)
                ->when($isOffHours, function($q) use ($isWeekendOff) {
                    if ($isWeekendOff) {
                        $q->where('users.weekend_standby', '=', true);
                    } else {
                        $q->where('users.weekly_standby', '=', true);
                    }
                });

            $enforceRegion = in_array((int)$row->section_id, [2, 3], true);
            if ($enforceRegion) {
                if (!empty($scopeRegion)) {
                    $query->where('users.region', '=', $scopeRegion);
                } elseif ($considerRegion && $faultRegion) {
                    $query->where('users.region', '=', $faultRegion);
                }
            }

            $userIds = $query->pluck('users.id')->toArray();

            if (empty($userIds)) { continue; }

            // Round-robin pointer per section
            $rrKey = 'auto_assign_rr_' . $row->section_id;
            $idx = Cache::get($rrKey, 0);
            if ($idx >= count($userIds)) { $idx = 0; }
            $selectedUserId = (int)$userIds[$idx];

            // Perform assignment and lifecycle updates
            $fault = Fault::find((int)$row->id);
            if (!$fault) { continue; }

            $fault->assignedTo = $selectedUserId;
            $fault->status_id = 3; // Under rectification
            $fault->save();

            FaultLifecycle::recordStatusChange($fault, 3, null);
            $region = $faultRegion;
            FaultLifecycle::startAssignment($fault, $selectedUserId, null, $isOffHours, $region);

                $assignedCount++;

            // Advance pointer
            $idx++;
            if ($idx >= count($userIds)) { $idx = 0; }
            Cache::put($rrKey, $idx);
            }
        }

        $this->info("Auto-assigned {$assignedCount} fault(s).");
        return Command::SUCCESS;
    }
}