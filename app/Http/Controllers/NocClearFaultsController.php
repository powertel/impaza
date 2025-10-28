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
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
            // Join open stage for current status to get start time
            ->leftjoin('fault_stage_logs as fsl', function($join) {
                $join->on('fsl.fault_id','=','faults.id');
                $join->on('fsl.status_id','=','faults.status_id');
                $join->whereNull('fsl.ended_at');
            })
            ->orderBy('faults.created_at', 'desc')
            // Show faults cleared by Technician (CLT: status_id = 5) for NOC review
            ->where('faults.status_id','=',5)
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
                'faults.serviceType',
                'faults.serviceAttribute',
                'faults.faultType',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'reasons_for_outages.RFO as RFO',
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

        return view('clear_faults.noc_clear',compact('faults','remarksByFault'))
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
        $fault = Fault::find($id);
        $req= $request->all();
        $req['status_id'] = 6;
        $fault ->update($req);
        FaultLifecycle::recordStatusChange($fault, 6, $request->user()->id);
        // Ensure assignment window is closed when NOC clears
        FaultLifecycle::resolveAssignment($fault);
        
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
