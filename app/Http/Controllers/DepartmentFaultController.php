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

class DepartmentFaultController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:department-faults-list|department-faults-create|department-faults-edit|department-faults-delete', ['only' => ['index','store']]);
         $this->middleware('permission:department-faults-create', ['only' => ['create','store']]);
         $this->middleware('permission:department-faults-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:department-faults-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $user = auth()->user();
        $faults = Section::find(auth()->user()->section_id)->faults()
                ->leftJoin('users','fault_section.section_id','=','users.section_id')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->orderBy('faults.updated_at', 'asc')
                ->where('users.id','=',auth()->user()->id)
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link','statuses.description'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('department_faults.index',compact('faults'))
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

    public function getSections(Request $req)
    {
        $user = auth()->user();
        $faults = Section::find(auth()->user()->section_id)->faults()
                ->leftJoin('users','fault_section.section_id','=','users.section_id')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->orderBy('faults.created_at', 'desc')
                ->where('users.id','=',auth()->user()->id)
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('department_faults.index',compact('faults'))
        ->with('i');
/* 
        $faults = Section::has('faults')->get();
        return view('department_faults.index',compact('faults'))
    ->with('i'); */

    }
}
