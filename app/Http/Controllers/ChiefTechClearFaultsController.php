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

class ChiefTechClearFaultsController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:chief-tech-clear-faults-list|chief-tech-clear-faults-create|chief-tech-clear-faults-clear|chief-tech-clear-faults-delete', ['only' => ['index','store']]);
         $this->middleware('permission:chief-tech-clear-faults-create', ['only' => ['create','store']]);
         $this->middleware('permission:chief-tech-clear-faults-clear', ['only' => ['edit','update']]);
         $this->middleware('permission:chief-tech-clear-faults-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.status_id','=',4)
            ->where('users.section_id','=',auth()->user()->section_id)
            ->get([
                'faults.id',
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
                'reasons_for_outages.RFO as RFO'
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

        return view('clear_faults.chief_tech_clear',compact('faults','remarksByFault'))
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
        $req['status_id'] = 5;
        $fault ->update($req);
        FaultLifecycle::recordStatusChange($fault, 5, $request->user()->id);

        return redirect()->back()
            ->with('success','Fault Has Been Cleared by Chief Technician');
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
