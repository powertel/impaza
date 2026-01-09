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
use App\Models\User;
use DB;
use App\Services\FaultLifecycle;

class NocClearFaultsController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:noc-clear-faults-list|noc-clear-faults-create|noc-clear-faults-clear|noc-clear-faults-delete', ['only' => ['index','store']]);
         $this->middleware('permission:noc-clear-faults-create', ['only' => ['create','store']]);
         $this->middleware('permission:noc-clear-faults-clear', ['only' => ['edit','update','revoke']]);
         $this->middleware('permission:noc-clear-faults-delete', ['only' => ['destroy']]);
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
            // Join open stage for current status to get start time
            ->leftjoin('fault_stage_logs as fsl', function($join) {
                $join->on('fsl.fault_id','=','faults.id');
                $join->on('fsl.status_id','=','faults.status_id');
                $join->whereNull('fsl.ended_at');
            })
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.status_id','=',4)
            ->get([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.address',
                'account_manager_users.name as accountManager',
                'faults.suspectedRfo_id',
                'links.link',
                'statuses.description',
                'assessed_users.name as assessedBy',
                'faults.serviceType',
                'faults.serviceAttribute',
                'faults.faultType',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'suspectedRFO.RFO as RFO',
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

        return view('clear_faults.noc_clear',compact('faults','remarksByFault','faultAges','faultAgeStart','faultAgeEnd'))
            ->with('i');
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
        $validated = $request->validate([
            'remark' => ['required', 'string'],
        ]);

        $fault = Fault::findOrFail($id);
        $fault->update(['status_id' => 6]);
        FaultLifecycle::recordStatusChange($fault, 6, $request->user()->id);
        // Ensure assignment window is closed when NOC clears
        FaultLifecycle::resolveAssignment($fault);

        $remarkActivityId = (int) (DB::table('remark_activities')
            ->where('activity', '=', 'ON NOC CLEAR')
            ->value('id') ?? 0);

        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => $validated['remark'],
            'remarkActivity_id' => $remarkActivityId,
            'file_path' => null,
        ]);
        
        return redirect()->back()
            ->with('success','Fault Has Been Cleared By Noc');
    }

    /**
     * Revoke a technician-cleared fault back to rectification.
     * Moves status to 3 (Fault is under rectification), reopens assignment window.
     */
    public function revoke(Request $request, $id)
    {
        $fault = Fault::find($id);
        if (!$fault) {
            return redirect()->back()->with('fail', 'Fault not found');
        }

        $req = $request->all();
        $req['status_id'] = 3; // Under rectification
        $fault->update($req);
        // End current stage (likely Technician Cleared) and reopen Rectification stage to continue timing
        FaultLifecycle::reopenStageForStatus($fault, 3, $request->user()->id);
        // Do NOT reassign. Instead, reopen the last assignment to the same owner to continue timing
        FaultLifecycle::reopenAssignment($fault);

        return redirect()->back()
            ->with('success', 'Fault has been revoked to Technician for rework');
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
