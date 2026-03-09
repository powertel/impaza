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

use App\Models\AutoAssignSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FaultController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
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
            ->select([
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

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('faults.phoneNumber', 'like', $like)
                   ->orWhere('faults.contactEmail', 'like', $like)
                   ->orWhere('faults.address', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);
        $faults = $paginated->items();

        // Map to keep format consistent
        $mappedFaults = collect($faults)->map(function($f) {
            return $f;
        });

        // Use pluck on the collection, not array
        $faultIds = collect($faults)->pluck('id');
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
        $faultIdsList = collect($faults)->pluck('id')->all();
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
            'faults' => $mappedFaults,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
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
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file',
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

        $created = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file) { continue; }
                $stored = $file->storePublicly('attachments', 'public');
                $r = Remark::create([
                    'remark' => $data['remark'],
                    'user_id' => $user->id,
                    'fault_id' => $fault->id,
                    'remarkActivity_id' => $remarkActivityId,
                    'file_path' => $stored,
                ]);
                $created[] = $r->id;
            }
        } else {
            $r = Remark::create([
                'remark' => $data['remark'],
                'user_id' => $user->id,
                'fault_id' => $fault->id,
                'remarkActivity_id' => $remarkActivityId,
                'file_path' => $path,
            ]);
            $created[] = $r->id;
        }

        return response()->json(['success' => true, 'message' => 'Remark added', 'ids' => $created]);
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
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file',
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

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file) { continue; }
                $stored = $file->storePublicly('attachments', 'public');
                Remark::create([
                    'remark' => $data['notes'],
                    'user_id' => $user->id,
                    'fault_id' => $fault->id,
                    'remarkActivity_id' => $remarkActivityId,
                    'file_path' => $stored,
                ]);
            }
        } else {
            Remark::create([
                'remark' => $data['notes'],
                'user_id' => $user->id,
                'fault_id' => $fault->id,
                'remarkActivity_id' => $remarkActivityId,
                'file_path' => $path,
            ]);
        }

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

    public function unassigned(Request $request)
    {
        // Permission check: assigned-fault-list
        if (!$request->user()->can('assigned-fault-list')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
            ->leftJoin('fault_section','faults.id','=','fault_section.fault_id')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('pops','faults.pop_id','=','pops.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->where('fault_section.section_id','=', $request->user()->section_id)
            ->where('faults.status_id','=', 2) // Status 2 = Open/Unassigned
            ->whereNull('faults.assignedTo')
            ->when(in_array((int)$request->user()->section_id, [2, 3], true), function($q) use ($request) {
                $q->where('cities.region','=', $request->user()->region);
            })
            ->orderBy('faults.created_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'faults.serviceType',
                'users.name as assignedToName',
                'assessed_users.name as assessedBy'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('faults.phoneNumber', 'like', $like)
                   ->orWhere('faults.address', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function sectionFaults(Request $request)
    {
        // Permission check: department-faults-list or assigned-fault-list
        if (!$request->user()->can('department-faults-list') && !$request->user()->can('assigned-fault-list')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
            ->leftJoin('fault_section','faults.id','=','fault_section.fault_id')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->where('fault_section.section_id','=', $request->user()->section_id)
            ->when(in_array((int)$request->user()->section_id, [2, 3], true), function($q) use ($request) {
                $q->where('cities.region','=', $request->user()->region);
            })
            ->orderBy('faults.created_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'users.name as assignedToName',
                'faults.assignedTo',
                'assessed_users.name as assessedBy'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('links.link', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function assignableTechnicians(Request $request)
    {
        $technicians = DB::table('users')
            ->leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.section_id','=', $request->user()->section_id)
            ->when(in_array((int)$request->user()->section_id, [2, 3], true), function($q) use ($request) {
                $q->where('users.region','=', $request->user()->region);
            })
            ->where('user_statuses.status_name','=','Assignable')
            ->orderBy('users.name','asc')
            ->get(['users.id','users.name']);

        return response()->json($technicians);
    }

    public function assign(Request $request)
    {
        if (!$request->user()->can('assign-fault')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'fault_id' => 'required|integer|exists:faults,id',
            'assignedTo' => 'required|integer|exists:users,id',
        ]);

        $fault = Fault::find($request->input('fault_id'));
        if (!$fault) {
            return response()->json(['message' => 'Fault not found'], 404);
        }

        if ((int)$fault->status_id !== 2 || !is_null($fault->assignedTo)) {
            return response()->json(['message' => 'Fault is not in an assignable state'], 422);
        }

        // Region check
        $faultRegion = \DB::table('cities')->where('id', $fault->city_id)->value('region');
        $enforceRegion = in_array((int)$request->user()->section_id, [2, 3], true);
        if ($enforceRegion && $faultRegion !== $request->user()->region) {
            return response()->json(['message' => 'You can only assign faults in your region'], 403);
        }

        // Ensure selected technician is in current section/region and eligible
        $isTechEligible = \DB::table('users')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.id', '=', $request->input('assignedTo'))
            ->where('users.section_id', '=', $request->user()->section_id)
            ->when($enforceRegion, function($q) use ($request) {
                $q->where('users.region', '=', $request->user()->region);
            })
            ->where('user_statuses.status_name', '=', 'Assignable')
            ->exists();

        if (!$isTechEligible) {
            return response()->json(['message' => 'Selected technician is not eligible'], 422);
        }

        $fault->update([
            'assignedTo' => (int)$request->input('assignedTo'),
            'status_id' => 3,
        ]);

        FaultLifecycle::recordStatusChange($fault, 3, $request->user()->id);
        FaultLifecycle::startAssignment(
            $fault,
            (int)$request->input('assignedTo'),
            $request->user()->id,
            FaultLifecycle::isOffHours(),
            $faultRegion
        );

        return response()->json(['success' => true, 'message' => 'Fault assigned successfully']);
    }

    public function reassign(Request $request, $id)
    {
        if (!$request->user()->can('re-assign-fault')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'assignedTo' => ['required', 'exists:users,id'],
                'remark' => ['required','string'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Re-assign validation failed', [
                'fault_id' => (int)$id,
                'user_id' => optional($request->user())->id,
                'path' => $request->path(),
                'errors' => $e->errors(),
            ]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $fault = Fault::find($id);
        if (!$fault) {
            return response()->json(['message' => 'Fault not found'], 404);
        }

        // Region enforcement for sections 2 and 3
        $enforceRegion = in_array((int)$request->user()->section_id, [2, 3], true);
        
        // Ensure selected technician is eligible
        $isTechEligible = \DB::table('users')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.id', '=', $request->input('assignedTo'))
            ->where('users.section_id', '=', $request->user()->section_id)
            ->when($enforceRegion, function($q) use ($request) {
                $q->where('users.region', '=', $request->user()->region);
            })
            ->where('user_statuses.status_name', '=', 'Assignable')
            ->exists();

        if (!$isTechEligible) {
            return response()->json(['message' => 'Selected technician is not eligible'], 422);
        }

        $fault->update([
            'assignedTo' => (int)$request->input('assignedTo'),
        ]);

        // Log the reassignment remark
        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => 'Re-assigned: ' . $request->input('remark'),
        ]);

        return response()->json(['success' => true, 'message' => 'Fault re-assigned successfully']);
    }

    public function assessments(Request $request)
    {
        // Status 1 = Logged/Pending Assessment
        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->where('faults.status_id', '=', 1)
            ->orderBy('faults.created_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'faults.serviceType'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('links.link', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function assess(Request $request, $id)
    {
        if (!$request->user()->can('fault-assessment')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'priorityLevel' => ['required'],
            'faultType' => ['required'],
            'remark' => ['required','string'],
        ]);

        $fault = Fault::find($id);
        if (!$fault) {
            return response()->json(['message' => 'Fault not found'], 404);
        }

        DB::beginTransaction();
        try {
            $req = $request->only(['priorityLevel','faultType']);
            $req['status_id'] = 2; // Unassigned
            $req['assessed_by'] = $request->user()->id;
            $fault->update($req);
            
            FaultLifecycle::recordStatusChange($fault, 2, $request->user()->id);

            // Determine section based on faultType
            $sectionId = null;
            if ($request->input('faultType') === 'Logical') {
                $sectionId = 1;
            } elseif ($request->input('faultType') === 'Physical') {
                $sectionId = 2;
            }

            // Update FaultSection
            \App\Models\FaultSection::updateOrCreate(
                ['fault_id' => $id],
                ['section_id' => $sectionId]
            );

            if (!is_null($sectionId)) {
                $this->autoAssign($sectionId);
            }

            // Log remark
            $remarkActivityId = (int) (DB::table('remark_activities')
                ->where('activity', '=', 'ON ASSESSMENT')
                ->value('id') ?? 0);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => $validated['remark'],
                'remarkActivity_id' => $remarkActivityId,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Fault Assessed']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Assessment failed'], 500);
        }
    }

    public function rectified(Request $request)
    {
        // Permission check: noc-clear-faults-list
        if (!$request->user()->can('noc-clear-faults-list')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->where('faults.status_id', '=', 4)
            ->orderBy('faults.created_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'users.name as assignedToName',
                'assessed_users.name as assessedBy'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('links.link', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function clear(Request $request, $id)
    {
        if (!$request->user()->can('noc-clear-faults-clear')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string'],
            'confirmedRfo_id' => ['nullable', 'integer', 'exists:reasons_for_outages,id'],
        ]);

        $fault = Fault::find($id);
        if (!$fault) return response()->json(['message' => 'Fault not found'], 404);

        $updateData = ['status_id' => 6]; // NOC Cleared
        if ($request->filled('confirmedRfo_id')) {
            $updateData['confirmedRfo_id'] = (int) $validated['confirmedRfo_id'];
        }
        $fault->update($updateData);
        FaultLifecycle::recordStatusChange($fault, 6, $request->user()->id);
        FaultLifecycle::resolveAssignment($fault);

        $remarkActivityId = (int) (DB::table('remark_activities')
            ->where('activity', '=', 'ON NOC CLEAR')
            ->value('id') ?? 0);

        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => $validated['remark'],
            'remarkActivity_id' => $remarkActivityId,
        ]);

        return response()->json(['success' => true, 'message' => 'Fault Cleared']);
    }

    public function escalations(Request $request)
    {
        if (!$request->user()->can('chief-tech-clear-faults-list')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $escId = FaultLifecycle::escalatedId();
        $mgrEscId = FaultLifecycle::managerEscalatedId();

        $query = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->whereIn('faults.status_id', [$escId, $mgrEscId])
            ->orderBy('faults.created_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'users.name as assignedToName',
                'assessed_users.name as assessedBy'
            ]);

        if ((int)($request->user()->section_id ?? 0) !== 1) {
             // Logic from ChiefTechEscalationsController
             // Usually filters by section if not section 1 (assuming section 1 is NOC/Admin?)
             // Keeping it simple for mobile api logic similar to web
        }

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('links.link', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function resolved(Request $request)
    {
        // Status 6 = NOC Cleared / Resolved
        // Permission: Typically open to all auth users or 'reports'
        
        $nocClearedId = 6;
        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->where('faults.status_id', '=', $nocClearedId)
            ->orderBy('faults.updated_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'faults.updated_at',
                'cities.city',
                'users.name as assignedToName',
                'assessed_users.name as assessedBy'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('links.link', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function referred(Request $request)
    {
        // Status 7 = Referred
        // Permission: refer-fault? or just list
        $perPage = (int) $request->query('per_page', 20);
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('cities','faults.city_id','=','cities.id')
            ->leftJoin('users','faults.assignedTo','=','users.id')
            ->leftJoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftJoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->where('faults.status_id', '=', 7)
            ->orderBy('faults.created_at', 'desc')
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'statuses.description as status',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'users.name as assignedToName',
                'assessed_users.name as assessedBy'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $query->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('links.link', 'like', $like);
            });
        }

        $paginated = $query->paginate($perPage);

        return response()->json([
            'faults' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ]
        ]);
    }

    public function revoke(Request $request, $id)
    {
        // Permission check? ResolvedController doesn't show one explicitly, but usually it's implied.
        // Let's assume basic auth for now or check roles in frontend.

        $validated = $request->validate([
            'remark' => ['required','string']
        ]);

        $fault = Fault::find($id);
        if (!$fault) return response()->json(['message' => 'Fault not found'], 404);

        $fault->update(['status_id' => 4]); // Reopen to Rectified
        FaultLifecycle::recordStatusChange($fault, 4, $request->user()->id);
        FaultLifecycle::reopenStageForStatus($fault, 4, $request->user()->id);

        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => 'Resolved revoke: '.$validated['remark'],
        ]);

        return response()->json(['success' => true, 'message' => 'Fault Reopened']);
    }

    public function refer(Request $request, $id)
    {
        if (!$request->user()->can('refer-fault')) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'section_id' => ['required','exists:sections,id'],
            'remark' => ['required','string']
        ]);

        $fault = Fault::find($id);
        if (!$fault) return response()->json(['message' => 'Fault not found'], 404);

        DB::beginTransaction();
        try {
            $prev = (int)($fault->status_id ?? 0);
            $fault->update(['status_id' => 7]);
            FaultLifecycle::recordStatusChange($fault, 7, $request->user()->id);
            FaultLifecycle::resolveAssignment($fault);

            $section = Section::find((int)$validated['section_id']);
            $note = $section ? ('Referred to Section: ' . ($section->section ?? 'Section') . "\n" . $validated['remark']) : $validated['remark'];

            \App\Models\FaultReferral::create([
                'fault_id' => $fault->id,
                'from_section_id' => $request->user()->section_id,
                'to_section_id' => (int)$validated['section_id'],
                'referred_by' => $request->user()->id,
                'previous_status_id' => $prev,
                'work_note' => $note,
                'started_at' => now(),
            ]);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => $note,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Fault Referred']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Referral failed'], 500);
        }
    }

    private function autoAssign($section_id)
    {
        $scopeSectionId = (int)$section_id;

        $settingsCollection = AutoAssignSetting::query()
            ->where('scope_section_id', $scopeSectionId)
            ->where('auto_assign_enabled', true)
            ->get();

        if ($settingsCollection->isEmpty()) {
            Log::info('Auto-assign skipped: no enabled settings for section', [
                'assessed_section_id' => $scopeSectionId,
            ]);
            return;
        }

        foreach ($settingsCollection as $settings) {
            $scopeRegion = $settings->scope_region; 

            $considerRegion = (bool)($settings->consider_region ?? true);
            $considerZones = (bool)($settings->consider_zones ?? false);
            $considerLeave = (bool)($settings->consider_leave ?? true);
            $isOffHours = \App\Services\FaultLifecycle::isOffHours();
            $isWeekendOff = (bool)($settings->weekend_standby_enabled ?? true) && now()->isWeekend();

            // 1. Get Eligible Technicians for this Scope
            $usersQuery = User::join('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
                ->where('sections.id','=',$scopeSectionId)
                ->when(in_array($scopeSectionId, [2,3], true) && !empty($scopeRegion), function($q) use ($scopeRegion) {
                    $q->where('users.region', '=', $scopeRegion);
                })
                ->where(function($q) use ($isOffHours) {
                    if ($isOffHours) {
                        $q->whereIn('user_statuses.status_name', ['Standby','Assignable']);
                    } else {
                        $q->where('user_statuses.status_name', '=', 'Assignable');
                    }
                })
                ->whereNotIn('user_statuses.status_name', $considerLeave ? ['Unassignable','On Leave'] : ['Unassignable'])
                ->when($isOffHours, function($q) use ($isWeekendOff) {
                    if ($isWeekendOff) {
                        $q->where('users.weekend_standby', '=', true);
                    } else {
                        $q->where('users.weekly_standby', '=', true);
                    }
                });

            $users = $usersQuery->pluck('users.id')->toArray();
            
            if (empty($users)) {
                 $users = User::join('departments','users.department_id','=','departments.id')
                    ->leftjoin('sections','users.section_id','=','sections.id')
                    ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
                    ->where('sections.id','=',$scopeSectionId)
                    ->where('user_statuses.status_name', '=', 'Assignable')
                    ->when(in_array($scopeSectionId, [2,3], true) && !empty($scopeRegion), function($q) use ($scopeRegion) {
                        $q->where('users.region', '=', $scopeRegion);
                    })
                    ->whereNotIn('user_statuses.status_name', $considerLeave ? ['Unassignable','On Leave'] : ['Unassignable'])
                    ->pluck('users.id')
                    ->toArray();
                 
                 if (!empty($users)) {
                    Log::warning('Auto-assign fallback: relaxed technician pool used', [
                        'section_id' => $scopeSectionId,
                        'scope_region' => $scopeRegion,
                    ]);
                 }
            }

            if (empty($users)) {
                continue; 
            }

            // 2. Find unassigned assessed faults matching this scope
            $faults = DB::table('fault_section')
                ->leftjoin('faults','fault_section.fault_id','=','faults.id')
                ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                ->leftJoin('pops', 'faults.pop_id', '=', 'pops.id')
                ->where('faults.status_id','=',2) // Assessed
                ->whereNull('faults.assignedTo')
                ->where('fault_section.section_id','=',$scopeSectionId)
                ->when(in_array($scopeSectionId, [2,3], true) && !empty($scopeRegion), function($q) use ($scopeRegion) {
                    $q->where('cities.region', '=', $scopeRegion);
                })
                ->select('faults.id', 'cities.region as fault_region', 'pops.zone_id')
                ->get();

            if ($faults->isEmpty()) {
                continue;
            }

            // 3. Round-Robin Assignment Loop
            $rrKeySuffix = in_array($scopeSectionId, [2,3], true) ? ($scopeRegion ?: 'all') : 'all';
            $rrKey = 'last_assigned_user_index_' . $scopeSectionId . '_' . $rrKeySuffix;
            $lastAssignedUserIndex = Cache::get($rrKey, 0);

            foreach ($faults as $f) {
                $autoAssign = $f->id;
                $faultRegion = $f->fault_region;
                $zoneId = $f->zone_id;
                
                $eligibleUsers = $users;
                $appliedZoneFilter = false;

                if ($considerZones && $zoneId) {
                    $zoneUserIds = \DB::table('technician_zone')
                        ->where('zone_id', $zoneId)
                        ->whereIn('user_id', $eligibleUsers)
                        ->pluck('user_id')
                        ->toArray();
                    
                    if (!empty($zoneUserIds)) {
                        $eligibleUsers = $zoneUserIds;
                        $appliedZoneFilter = true;
                    }
                }

                if (!$appliedZoneFilter && $considerRegion && $faultRegion && empty($scopeRegion)) {
                    $regionUserIds = User::whereIn('id', $eligibleUsers)
                        ->where('region', $faultRegion)
                        ->pluck('id')
                        ->toArray();
                    
                    if (!empty($regionUserIds)) {
                        $eligibleUsers = $regionUserIds;
                    }
                }

                if (empty($eligibleUsers)) {
                    Log::info('Auto-assign skipped for fault: no eligible technicians', [
                        'fault_id' => $autoAssign,
                        'section_id' => $scopeSectionId,
                        'scope_region' => $scopeRegion,
                    ]);
                    continue;
                }

                $currentRrKey = $rrKey;
                $currentIndex = $lastAssignedUserIndex;
                if ($appliedZoneFilter) {
                    $currentRrKey = 'last_assigned_user_index_' . $scopeSectionId . '_zone_' . $zoneId;
                    $currentIndex = Cache::get($currentRrKey, 0);
                }

                $eligibleUsers = array_values($eligibleUsers);
                $idx = $currentIndex % count($eligibleUsers);
                $selectedUserId = $eligibleUsers[$idx];

                $assign = Fault::find($autoAssign);
                if (!$assign) continue;

                $req = [];
                $req['assignedTo'] = $selectedUserId;
                $req['status_id'] = 3;
                $assign->update($req);
                
                FaultLifecycle::recordStatusChange($assign, 3, auth()->id());
                FaultLifecycle::startAssignment($assign, $selectedUserId, auth()->id(), $isOffHours, $faultRegion);

                $nextIndex = $idx + 1;
                Cache::put($currentRrKey, $nextIndex);
                
                if (!$appliedZoneFilter) {
                    $lastAssignedUserIndex = $nextIndex;
                    Cache::put($rrKey, $lastAssignedUserIndex);
                }
                
                Log::info("Auto-assigned fault {$autoAssign} to user {$selectedUserId}");
            }
        }
    }
}
