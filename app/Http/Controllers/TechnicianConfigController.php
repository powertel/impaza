<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AutoAssignSetting;
use App\Models\User;

class TechnicianConfigController extends Controller
{
    // Combined view (legacy)
    public function index()
    {
        $settings = AutoAssignSetting::query()->first();
        if (!$settings) {
            $settings = new AutoAssignSetting([
                // Defaults requested: 16:30 start, 06:00 end
                'standby_start_time' => '16:30:00',
                'standby_end_time' => '06:00:00',
                'weekend_standby_enabled' => true,
                'consider_leave' => true,
                'consider_region' => true,
            ]);
        }

        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        $technicians = User::leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->orderBy('users.name','asc')
            ->get(['users.id','users.name','sections.section','users.region','users.weekly_standby','users.weekend_standby','user_statuses.status_name']);

        return view('technicians.settings', compact('settings','regions','technicians'));
    }

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
            ]);
        }
        return view('technicians.auto', compact('settings'));
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
            ]);
        }
        $regions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region');
        $technicians = User::leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->orderBy('users.name','asc')
            ->get(['users.id','users.name','sections.section','users.region','users.weekly_standby','users.weekend_standby','user_statuses.status_name']);
        return view('technicians.config', compact('settings','regions','technicians'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'standby_start_time' => 'required|date_format:H:i',
            'standby_end_time' => 'required|date_format:H:i',
            'weekend_standby_enabled' => 'nullable|boolean',
            'consider_leave' => 'nullable|boolean',
            'consider_region' => 'nullable|boolean',
        ]);

        // Normalize checkboxes
        $data['weekend_standby_enabled'] = (bool)($data['weekend_standby_enabled'] ?? false);
        $data['consider_leave'] = (bool)($data['consider_leave'] ?? false);
        $data['consider_region'] = (bool)($data['consider_region'] ?? false);

        $settings = AutoAssignSetting::query()->first();
        if ($settings) {
            $settings->update($data + ['updated_by' => auth()->id()]);
        } else {
            AutoAssignSetting::create($data + ['updated_by' => auth()->id()]);
        }

        return redirect()->route('technicians.settings')->with('success', 'Auto-assign settings updated');
    }

    public function updateRegions(Request $request)
    {
        $payload = $request->validate([
            'user_id' => 'required|array',
            'user_id.*' => 'integer|exists:users,id',
            'region' => 'required|array',
            'region.*' => 'nullable|string',
        ]);

        $userIds = $payload['user_id'];
        $regions = $payload['region'];
        foreach ($userIds as $idx => $uid) {
            $reg = $regions[$idx] ?? null;
            User::where('id', $uid)->update(['region' => $reg]);
        }

        return back()->with('success', 'Technician regions updated');
    }

    // Auto-save endpoint for updating a single global setting field
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
            ]);
            $settings->save();
        }

        $key = $field['field'];
        $value = $field['value'];

        // Coerce types for known boolean fields
        $booleanKeys = ['weekend_standby_enabled','consider_leave','consider_region'];
        if (in_array($key, $booleanKeys, true)) {
            $value = (bool)$value;
        }
        if ($key === 'standby_start_time' || $key === 'standby_end_time') {
            // Expect HH:MM
            if (!is_string($value)) {
                return response()->json(['error' => 'Invalid time format'], 422);
            }
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
                $statusId = DB::table('user_statuses')->where('status_name', $onLeave ? 'On Leave' : 'Assignable')->value('id');
                if ($statusId) {
                    $user->update(['user_status' => $statusId]);
                }
                break;
            default:
                return response()->json(['error' => 'Unknown field'], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}