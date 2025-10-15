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
            ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.status_id','=',4)
            ->where('users.section_id','=',auth()->user()->section_id)
            ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
            'account_managers.accountManager','faults.suspectedRfo_id','links.link','faults.suspectedRfo_id','links.link','statuses.description'
            ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('clear_faults.chief_tech_clear',compact('faults'))
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

        return redirect()->route('faults.edit',$id)
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
