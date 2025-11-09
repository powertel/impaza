<?php

namespace App\Http\Controllers;

use App\Models\AutoAssignSetting;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicianConfigController extends Controller
{
    // Auto-assign settings page only
    public function auto()
    {
        $settings = AutoAssignSetting::query()->first();
        if (!$settings) {
            $settings = new AutoAssignSetting([
                'standby_start_time' => '16:30:00',
                'standby_end_time' => '06:00:00',
                'weekend_standby_enabled' => true,
                'consider_leave' => true,
                'consider_region' => true,
                'auto_assign_enabled' => false,
            ]);
        }
        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        $sections = Section::query()->orderBy('section')->get(['id','section']);
        return view('technicians.auto', compact('settings','regions','sections'));
    }

    // Technician configuration page only
    public function config()
    {
        $settings = AutoAssignSetting::query()->first();
        if (!$settings) {
            $settings = new AutoAssignSetting([
                'standby_start_time' => '16:30:00',
                'standby_end_time' => '06:00:00',
                'weekend_standby_enabled' => true,
                'consider_leave' => true,
                'consider_region' => true,
                'auto_assign_enabled' => false,
            ]);
        }
        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        $sections = Section::query()->orderBy('section')->get(['id','section']);
        $techniciansQuery = User::leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->orderBy('users.name','asc');

        // Limit technicians list to the logged-in user's section when available
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->section_id) {
            $techniciansQuery->where('users.section_id', $currentUser->section_id);
        }

        $technicians = $techniciansQuery->get(['users.id','users.name','sections.section','users.region','users.weekly_standby','users.weekend_standby','user_statuses.status_name']);
        return view('technicians.config', compact('settings','regions','sections','technicians'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'standby_start_time' => 'required|date_format:H:i',
            'standby_end_time' => 'required|date_format:H:i',
            'weekend_standby_enabled' => 'nullable|boolean',
            'consider_leave' => 'nullable|boolean',
            'consider_region' => 'nullable|boolean',
            'auto_assign_enabled' => 'nullable|boolean',
            'scope_section_id' => 'nullable|integer',
            'scope_region' => 'nullable|string',
        ]);

        // Normalize checkboxes
        $data['weekend_standby_enabled'] = (bool)($data['weekend_standby_enabled'] ?? false);
        $data['consider_leave'] = (bool)($data['consider_leave'] ?? false);
        $data['consider_region'] = (bool)($data['consider_region'] ?? false);
        $data['auto_assign_enabled'] = (bool)($data['auto_assign_enabled'] ?? false);

        // Default scope to the saving user's section/region if not explicitly provided
        $savingUser = $request->user();
        if (empty($data['scope_section_id'])) {
            $data['scope_section_id'] = optional($savingUser)->section_id;
        }
        if (empty($data['scope_region'])) {
            $data['scope_region'] = optional($savingUser)->region;
        }

        $settings = AutoAssignSetting::query()->first();
        if ($settings) {
            $settings->update($data + ['updated_by' => auth()->id()]);
        } else {
            AutoAssignSetting::create($data + ['updated_by' => auth()->id()]);
        }

        return redirect()->back()->with('success', 'Auto-assign settings saved successfully');
    }

    // Ajax save global setting
    public function updateSettingsAjax(Request $request)
    {
        $field = $request->validate([
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        $settings = AutoAssignSetting::query()->first();
        if (!$settings) {
            $settings = new AutoAssignSetting([
                'standby_start_time' => '16:30:00',
                'standby_end_time' => '06:00:00',
                'weekend_standby_enabled' => true,
                'consider_leave' => true,
                'consider_region' => true,
                'auto_assign_enabled' => false,
            ]);
            $settings->save();
        }

        $key = $field['field'];
        $value = $field['value'];

        // Coerce types for known boolean fields
        $booleanKeys = ['weekend_standby_enabled','consider_leave','consider_region','auto_assign_enabled'];
        if (in_array($key, $booleanKeys, true)) {
            $value = (bool)$value;
        }
        if ($key === 'standby_start_time' || $key === 'standby_end_time') {
            // Expect HH:MM
            if (!is_string($value)) {
                return response()->json(['error' => 'Invalid time format'], 422);
            }
        }
        if ($key === 'scope_section_id') {
            $value = $value ? (int)$value : null;
        }
        if ($key === 'scope_region') {
            $value = $value ?: null;
        }

        $settings->update([$key => $value, 'updated_by' => optional($request->user())->id]);

        return response()->json(['status' => 'ok']);
    }

    // Auto-save endpoint for updating per-user settings
    public function updateUserSetting(Request $request, User $user)
    {
        $payload = $request->validate([
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        $field = $payload['field'];
        $value = $payload['value'];

        switch ($field) {
            case 'region':
                $user->update(['region' => $value ?: null]);
                break;
            case 'weekly_standby':
                $user->update(['weekly_standby' => (bool)$value]);
                break;
            case 'weekend_standby':
                $user->update(['weekend_standby' => (bool)$value]);
                break;
            case 'user_status':
                // Accept values like 'Assignable' or 'Away' and store their IDs
                $name = is_string($value) ? $value : null;
                if ($name) {
                    $statusId = DB::table('user_statuses')->where('status_name', $name)->value('id');
                    if (!$statusId) {
                        // Create on-the-fly if missing
                        $statusId = DB::table('user_statuses')->insertGetId(['status_name' => $name]);
                    }
                    $user->update(['user_status' => $statusId]);
                }
                break;
            case 'on_leave':
                // Toggle user_status between On Leave and Assignable
                $onLeave = (bool)$value;
                $statusName = $onLeave ? 'On Leave' : 'Assignable';
                $statusId = DB::table('user_statuses')->where('status_name', $statusName)->value('id');
                if (!$statusId) {
                    $statusId = DB::table('user_statuses')->insertGetId(['status_name' => $statusName]);
                }
                $user->update(['user_status' => $statusId]);
                break;
        }

        return response()->json(['status' => 'ok']);
    }
}