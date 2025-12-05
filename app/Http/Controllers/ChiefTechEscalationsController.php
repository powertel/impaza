<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fault;
use App\Models\Remark;
use App\Models\Section;
use App\Models\FaultReferral;
use Illuminate\Support\Facades\DB;
use App\Services\FaultLifecycle;

class ChiefTechEscalationsController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:chief-tech-clear-faults-list|chief-tech-clear-faults-clear', ['only' => ['index','refer']]);
        $this->middleware('permission:chief-tech-return-to-technician', ['only' => ['returnToRectification']]);
        $this->middleware('permission:chief-tech-escalate', ['only' => ['escalateToManager']]);
        $this->middleware('permission:manager-return-to-chief-tech', ['only' => ['downgradeFromManager']]);
    }

    public function index()
    {
        $user = auth()->user();
        $escId = FaultLifecycle::escalatedId();
        $mgrEscId = FaultLifecycle::managerEscalatedId();
        $query = DB::table('faults')
            ->leftjoin('users','faults.assignedTo','=','users.id')
            ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
            ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
            ->leftjoin('fault_stage_logs as fsl', function($join) {
                $join->on('fsl.fault_id','=','faults.id');
                $join->on('fsl.status_id','=','faults.status_id');
                $join->whereNull('fsl.ended_at');
            })
            ->orderBy('faults.created_at', 'desc')
            ->whereIn('faults.status_id', [ $escId, $mgrEscId ]);

        if ((int)($user->section_id ?? 0) !== 1) {
            $query->where('users.section_id', '=', $user->section_id);
            if (!empty($user->region) && in_array((int)$user->section_id, [2, 3], true)) {
                $query->where('cities.region', '=', $user->region);
            }
        }

        $faults = $query->get([
            'faults.id',
            'faults.fault_ref_number',
            'customers.customer',
            'faults.contactName',
            'faults.phoneNumber',
            'faults.contactEmail',
            'faults.address',
            'account_manager_users.name as accountManager',
            'links.link',
            'statuses.description',
            'faults.status_id as status_id',
            'assessed_users.name as assessedBy',
            'faults.serviceType',
            'faults.serviceAttribute',
            'faults.faultType',
            'faults.priorityLevel',
            'faults.created_at',
            'cities.city',
            'cities.region',
            'suburbs.suburb',
            'pops.pop',
            'suspectedRFO.RFO as RFO',
            'confirmedRFO.RFO as confirmedRFO',
            'fsl.started_at as stage_started_at'
        ]);

        $faultIds = $faults->pluck('id');
        $remarksRecords = DB::table('remarks')
            ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
            ->leftjoin('users','remarks.user_id','=','users.id')
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

        return view('escalations.chief_tech_index', compact('faults','remarksByFault'))->with('i');
    }

    public function refer(Request $request, Fault $fault)
    {
        $validated = $request->validate([
            'section_id' => ['required','exists:sections,id'],
            'remark' => ['required','string']
        ]);

        DB::beginTransaction();
        try {
            $prev = (int)($fault->status_id ?? 0);
            $fault->update(['status_id' => 7]);
            FaultLifecycle::recordStatusChange($fault, 7, $request->user()->id);
            FaultLifecycle::resolveAssignment($fault);

            $section = Section::find((int)$validated['section_id']);
            $note = $section ? ('Referred to Section: ' . ($section->section ?? 'Section') . "\n" . $validated['remark']) : $validated['remark'];

            FaultReferral::create([
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
            return redirect()->back()->with('success', 'Escalation referred to section');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to refer escalation');
        }
    }

    public function escalateToManager(Request $request, Fault $fault)
    {
        $request->validate([
            'remark' => ['required','string']
        ]);

        DB::beginTransaction();
        try {
            $fault->update(['status_id' => FaultLifecycle::managerEscalatedId()]);
            FaultLifecycle::recordStatusChange($fault, FaultLifecycle::managerEscalatedId(), $request->user()->id);
            FaultLifecycle::resolveAssignment($fault);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => 'Escalated to Manager: ' . $request->input('remark'),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Escalated to Manager');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to escalate to Manager');
        }
    }

    public function downgradeFromManager(Request $request, Fault $fault)
    {
        $request->validate([
            'remark' => ['required','string']
        ]);

        DB::beginTransaction();
        try {
            $fault->update(['status_id' => FaultLifecycle::escalatedId()]);
            FaultLifecycle::recordStatusChange($fault, FaultLifecycle::escalatedId(), $request->user()->id);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => 'Manager returned: ' . $request->input('remark'),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Returned from Manager to Chief Tech');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to return from Manager');
        }
    }

    public function returnToRectification(Request $request, Fault $fault)
    {
        $request->validate([
            'remark' => ['required','string']
        ]);

        $fault->update(['status_id' => 3]);
        FaultLifecycle::reopenStageForStatus($fault, 3, $request->user()->id);
        FaultLifecycle::reopenAssignment($fault);

        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => 'Escalation returned: '.$request->input('remark'),
        ]);

        return redirect()->back()->with('success', 'Fault returned to rectification');
    }
}
