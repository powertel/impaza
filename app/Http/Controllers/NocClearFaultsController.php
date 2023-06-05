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

class NocClearFaultsController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:noc-clear-faults-list|noc-clear-faults-create|noc-clear-faults-clear|noc-clear-faults-delete', ['only' => ['index','store']]);
         $this->middleware('permission:noc-clear-faults-create', ['only' => ['create','store']]);
         $this->middleware('permission:noc-clear-faults-clear', ['only' => ['edit','update']]);
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
            ->leftjoin('suspected_rfos','faults.suspectedRfo_id','=','suspected_rfos.id')
            ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.status_id','=',5)
            ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
            'account_managers.accountManager','faults.suspectedRfo_id','links.link','statuses.description'
            ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('clear_faults.noc_clear',compact('faults'))
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
        
        return redirect()->route('noc-clear.index')
            ->with('success','Fault Has Been Cleared By Noc');
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
