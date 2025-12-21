<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fault;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use App\Models\Remark;
use App\Models\AccountManager;
use App\Models\Section;
use App\Models\FaultReferral;
use App\Services\FaultLifecycle;
use DB;

class MyFaultController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:my-fault-list|my-fault-create|my-fault-edit|my-fault-delete', ['only' => ['index','store']]);
         $this->middleware('permission:my-fault-create', ['only' => ['create','store']]);
         $this->middleware('permission:my-fault-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:my-fault-delete', ['only' => ['destroy']]);
         $this->middleware('permission:refer-fault', ['only' => ['refer']]);
         $this->middleware('permission:rectify-fault', ['only' => ['escalate']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $faults = DB::table('faults')
                 ->leftjoin('users','faults.assignedTo','=','users.id')
                ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
                ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
				->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
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
                // Join open stage for current status to get start time
                ->leftjoin('fault_stage_logs as fsl', function($join) {
                    $join->on('fsl.fault_id','=','faults.id');
                    $join->on('fsl.status_id','=','faults.status_id');
                    $join->whereNull('fsl.ended_at');
                })
                ->orderBy('faults.created_at', 'desc')
                ->where('faults.assignedTo','=',auth()->user()->id)
                ->get([
                    'faults.id',
                    'customers.customer',
                    'faults.contactName',
                    'faults.phoneNumber',
                    'faults.contactEmail',
                    'faults.address',
                    'account_manager_users.name as accountManager',
                    'links.link',
                    'statuses.description',
                    'assigned_users.name as assignedTo',
                    'reported_users.name as reportedBy',
                    'assessed_users.name as assessedBy',
                    'faults.serviceType',
                    'faults.serviceAttribute',
                    'faults.faultType',
                    'faults.priorityLevel',
                    'faults.created_at',
                    'cities.city as city',
                    'suburbs.suburb as suburb',
                    'pops.pop as pop',
                    'suspectedRFO.RFO as RFO',
                    'faults.confirmedRfo_id',
                    'confirmedRFO.RFO as confirmedRFO',
                    'fsl.started_at as stage_started_at'
                ]);
        // Collect remarks for all listed faults and group by fault_id
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

        $confirmedRFO = \App\Models\ReasonsForOutage::all();
        $sections = Section::all();

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

        return view('my_faults.index',compact('faults','remarksByFault','confirmedRFO','sections','faultAges','faultAgeStart','faultAgeEnd'))
        ->with('i');
    }

    public function refer(Request $request, $id)
    {
        $request->validate([
            'section_id' => ['required','exists:sections,id'],
            'remark' => ['required','string']
        ]);

        DB::beginTransaction();
        try {
            $fault = Fault::find($id);
            if (!$fault) {
                DB::rollBack();
                return redirect()->back()->with('fail', 'Fault not found');
            }

            $prevStatus = (int)($fault->status_id ?? 0);
            $fault->update([
                'status_id' => 7,
            ]);

            FaultLifecycle::recordStatusChange($fault, 7, $request->user()->id);
            FaultLifecycle::resolveAssignment($fault);

            $section = Section::find((int)$request->input('section_id'));
            $note = $section ? ('Referred to Section: ' . ($section->section ?? 'Section') . "\n" . $request->input('remark')) : $request->input('remark');

            FaultReferral::create([
                'fault_id' => $fault->id,
                'from_section_id' => auth()->user()->section_id,
                'to_section_id' => (int)$request->input('section_id'),
                'referred_by' => $request->user()->id,
                'previous_status_id' => $prevStatus,
                'work_note' => $note,
                'started_at' => now(),
            ]);
            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => $note,
            ]);

            DB::commit();
            return redirect()->route('my_faults.index')->with('success', 'Fault referred to section');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to refer fault');
        }
    }

    public function escalate(Request $request, Fault $fault)
    {
        $request->validate([
            'remark' => ['required','string']
        ]);

        DB::beginTransaction();
        try {
            $fault->update(['status_id' => \App\Services\FaultLifecycle::escalatedId()]);
            FaultLifecycle::recordStatusChange($fault, \App\Services\FaultLifecycle::escalatedId(), $request->user()->id);
            FaultLifecycle::resolveAssignment($fault);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => 'Escalated: '.$request->input('remark'),
            ]);

            DB::commit();
            return redirect()->route('my_faults.index')->with('success', 'Fault escalated to Chief Technician');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to escalate fault');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
