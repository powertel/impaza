<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Fault;
use App\Models\Remark;
use App\Models\ReasonsForOutage;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FaultLifecycle;

class FaultController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $faults = DB::table('faults')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->leftJoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftJoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('pops','faults.pop_id','=','pops.id')
            ->leftJoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
            ->leftJoin('fault_stage_logs as fsl', function($join) {
                $join->on('fsl.fault_id','=','faults.id');
                $join->on('fsl.status_id','=','faults.status_id');
                $join->whereNull('fsl.ended_at');
            })
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.assignedTo', '=', $userId)
            ->limit(50)
            ->get([
                'faults.id',
                'customers.customer',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.fault_ref_number',
                'faults.address',
                'account_manager_users.name as accountManager',
                'links.link',
                'statuses.id as status_id',
                'statuses.description as status',
                'assessed_users.name as assessedBy',
                'faults.serviceType',
                'faults.serviceAttribute',
                'faults.faultType',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city as city',
                'suburbs.suburb as suburb',
                'pops.pop as pop',
                'reasons_for_outages.RFO as RFO',
                'fsl.started_at as stage_started_at'
            ]);

        $faultIds = $faults->pluck('id');
        $remarksRecords = DB::table('remarks')
            ->leftJoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftJoin('users','remarks.user_id','=','users.id')
            ->whereIn('remarks.fault_id', $faultIds)
            ->orderBy('remarks.created_at', 'desc')
            ->get([
                'remarks.id',
                'remarks.fault_id',
                'remarks.created_at',
                'remarks.remark',
                'remarks.file_path',
                'users.name',
                'remark_activities.activity'
            ]);

        $remarksByFault = $remarksRecords->groupBy('fault_id');

        $faultAges = [];$faultAgeStart = [];$faultAgeEnd = [];
        $nocClearedId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
        $faultIdsList = $faults->pluck('id')->all();
        if (!empty($faultIdsList)) {
            $clearedLogs = DB::table('fault_stage_logs')
                ->whereIn('fault_id', $faultIdsList)
                ->where('status_id', $nocClearedId)
                ->select('fault_id','started_at')
                ->get()
                ->keyBy('fault_id');
            foreach ($faults as $f) {
                $start = \Carbon\Carbon::parse($f->created_at);
                $end = (isset($clearedLogs[$f->id])) ? \Carbon\Carbon::parse($clearedLogs[$f->id]->started_at) : \Carbon\Carbon::now();
                $days = $start->diffInDays($end);
                $hours = $start->copy()->addDays($days)->diffInHours($end) % 24;
                $minutes = $start->copy()->addDays($days)->addHours($hours)->diffInMinutes($end) % 60;
                $faultAges[$f->id] = ($days > 0 ? ($days.'d ') : '').($hours.'h ').($minutes.'m');
                $faultAgeStart[$f->id] = $start->format('c');
                $faultAgeEnd[$f->id] = isset($clearedLogs[$f->id]) ? \Carbon\Carbon::parse($clearedLogs[$f->id]->started_at)->format('c') : null;
            }
        }

        return response()->json([
            'faults' => $faults,
            'remarksByFault' => $remarksByFault,
            'faultAges' => $faultAges,
            'faultAgeStart' => $faultAgeStart,
            'faultAgeEnd' => $faultAgeEnd,
        ]);
    }

    public function show(Fault $fault)
    {
        // Join related tables to return enriched fault details
        $item = \DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('pops','faults.pop_id','=','pops.id')
            ->leftJoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
            ->leftJoin('fault_stage_logs as fsl', function($join) {
                $join->on('fsl.fault_id','=','faults.id');
                $join->on('fsl.status_id','=','faults.status_id');
                $join->whereNull('fsl.ended_at');
            })
            ->where('faults.id','=',$fault->id)
            ->first([
                'faults.id',
                'customers.customer',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.fault_ref_number',
                'faults.address',
                'links.link',
                'statuses.id as status_id',
                'statuses.description as status',
                'faults.serviceType',
                'faults.serviceAttribute',
                'faults.faultType',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city as city',
                'suburbs.suburb as suburb',
                'pops.pop as pop',
                'reasons_for_outages.RFO as RFO',
                'fsl.started_at as stage_started_at'
            ]);

        // Collect remarks for the fault
        $remarks = \DB::table('remarks')
            ->leftJoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftJoin('users','remarks.user_id','=','users.id')
            ->where('remarks.fault_id','=',$fault->id)
            ->orderBy('remarks.created_at','desc')
            ->get([
                'remarks.id',
                'remarks.created_at',
                'remarks.remark',
                'remarks.file_path',
                'users.name',
                'remark_activities.activity'
            ]);

        return response()->json(['fault' => $item, 'remarks' => $remarks]);
    }

    public function addRemark(Request $request, Fault $fault)
    {
        // Block remarks on resolved faults (status_id = 4)
        if ((string)$fault->status_id === '4') {
            return response()->json(['success' => false, 'message' => 'Fault is resolved; remarks are not allowed.'], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'remark' => 'required|string|min:2',
            'activity' => 'nullable|string',
            'attachment' => 'nullable|file',
        ]);

        $path = '';
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->storePublicly('attachments', 'public');
        }

        $activityName = $data['activity'] ?? null;
        // remarks.remarkActivity_id is NOT NULL in schema; use 0 when activity is omitted
        $remarkActivityId = 0;
        if ($activityName) {
            $remarkActivityId = (int) (\DB::table('remark_activities')
                ->where('activity', '=', $activityName)
                ->value('id') ?? 0);
        }

        Remark::create([
            'remark' => $data['remark'],
            'user_id' => $user->id,
            'fault_id' => $fault->id,
            'remarkActivity_id' => $remarkActivityId,
            'file_path' => $path,
        ]);

        return response()->json(['success' => true, 'message' => 'Remark added']);
    }

    public function rectify(Request $request, Fault $fault)
    {
        // Block if already resolved
        if ((string)$fault->status_id === '4') {
            return response()->json(['success' => false, 'message' => 'Fault already resolved'], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'notes' => 'required|string|min:2',
            'confirmedRfo_id' => 'required|exists:reasons_for_outages,id',
            'activity' => 'nullable|string',
            'attachment' => 'nullable|file',
        ]);

        $path = '';
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->storePublicly('attachments', 'public');
        }

        $activityName = $data['activity'] ?? null;
        // Default to 0 when no activity is provided to satisfy NOT NULL schema
        $remarkActivityId = 0;
        if ($activityName) {
            $remarkActivityId = (int) (DB::table('remark_activities')
                ->where('activity', '=', $activityName)
                ->value('id') ?? 0);
        }

        Remark::create([
            'remark' => $data['notes'],
            'user_id' => $user->id,
            'fault_id' => $fault->id,
            'remarkActivity_id' => $remarkActivityId,
            'file_path' => $path,
        ]);

        // Technician resolved: set status to 4, save confirmed RFO and log lifecycle
        $fault->update(['status_id' => 4, 'confirmedRfo_id' => $data['confirmedRfo_id']]);
        FaultLifecycle::recordStatusChange($fault, 4, $user->id);
        FaultLifecycle::resolveAssignment($fault);

        return response()->json(['success' => true, 'message' => 'Fault marked as technician resolved']);
    }

    public function rfos()
    {
        $rfos = ReasonsForOutage::orderBy('RFO')->get(['id','RFO']);
        return response()->json($rfos);
    }

    public function sections()
    {
        return response()->json(Section::orderBy('section')->get(['id','section']));
    }

    public function escalate(Request $request, Fault $fault)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'remark' => ['required','string']
        ]);

        \DB::beginTransaction();
        try {
            $fault->update(['status_id' => \App\Services\FaultLifecycle::escalatedId()]);
            FaultLifecycle::recordStatusChange($fault, \App\Services\FaultLifecycle::escalatedId(), $user->id);
            FaultLifecycle::resolveAssignment($fault);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $user->id,
                'remark' => 'Escalated: ' . $validated['remark'],
            ]);

            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Failed to escalate'], 500);
        }
    }
}
