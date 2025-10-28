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
use App\Models\UserStatus;
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
/*         $user = auth()->user();
        
        $faults = Section::find(auth()->user()->section_id)->faults()
                ->leftJoin('users','fault_section.section_id','=','users.section_id')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->orderBy('faults.created_at', 'desc')
                ->where('users.id','=',auth()->user()->id)
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','faults.assignedTo',
                'account_managers.accountManager','faults.suspectedRfo','links.link','statuses.description','faults.assignedTo','users.name'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);

                $autoAssign = $this->autoAssign(auth()->user()->section_id);
        return view('department_faults.index',compact('faults','autoAssign'))
        ->with('i'); */

        $faults = DB::table('faults')
        ->leftjoin('fault_section','faults.id','=','fault_section.fault_id')
        ->leftjoin('users','faults.assignedTo','=','users.id')
        ->leftjoin('sections','fault_section.section_id','=','sections.id')
        ->leftjoin('customers','faults.customer_id','=','customers.id')
        ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
        ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
        ->leftjoin('links','faults.link_id','=','links.id')
        ->leftjoin('cities','faults.city_id','=','cities.id')
        ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
        ->leftjoin('pops','faults.pop_id','=','pops.id')
        ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
        ->leftjoin('statuses','faults.status_id','=','statuses.id')
        ->orderBy('faults.created_at', 'desc')
        ->where('fault_section.section_id','=',auth()->user()->section_id)
       ->get(['faults.id','faults.fault_ref_number','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','faults.assignedTo',
        'account_manager_users.name as accountManager','faults.suspectedRfo_id','links.link','statuses.description','faults.assignedTo','users.name'
       ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at','cities.city','suburbs.suburb','pops.pop','reasons_for_outages.RFO as RFO']);

        // Collect remarks for all listed faults and group by fault_id for faults.show
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

        return view('department_faults.index',compact('faults','remarksByFault'))
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
    /*         $user = auth()->user();
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
        ->with('i'); */

    $faults = DB::table('faults')
        ->leftjoin('fault_section','faults.id','=','fault_section.fault_id')
        ->leftjoin('users','faults.assignedTo','=','users.id')
        ->leftjoin('sections','fault_section.section_id','=','sections.id')
        ->leftjoin('customers','faults.customer_id','=','customers.id')
        ->leftjoin('links','faults.link_id','=','links.id')
        ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
        ->leftjoin('cities','faults.city_id','=','cities.id')
        ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
        ->leftjoin('pops','faults.pop_id','=','pops.id')
        ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
        ->orderBy('faults.created_at', 'desc')
        ->where('fault_section.section_id','=',auth()->user()->section_id)
        ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
        'account_managers.accountManager','faults.suspectedRfo_id','links.link','cities.city','suburbs.suburb','pops.pop','reasons_for_outages.RFO as RFO'
        ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
    
    // Collect remarks for all listed faults and group by fault_id for faults.show
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

    return view('department_faults.index',compact('faults','remarksByFault'))
        ->with('i');


    }



    public function autoAssign($section_id)
    {
        // Respect chief tech toggle: do nothing if disabled
        $settings = \App\Models\AutoAssignSetting::query()->first();
        if (!$settings || !($settings->auto_assign_enabled ?? false)) {
            return; // disabled
        }
   
        $users = User::join('departments','users.department_id','=','departments.id')
            ->leftjoin('sections','users.section_id','=','sections.id')
            ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('sections.id','=',$section_id)
            ->where('user_statuses.status_name','=','active')
            ->pluck('users.id')
            ->toArray();

        $faults = DB::table('fault_section')
            ->leftjoin('faults','fault_section.fault_id','=','faults.id')
            ->whereNull('faults.assignedTo')
            ->where('fault_section.section_id','=',$section_id)
            ->pluck('faults.id')
            ->toArray();

        $userslength=count($users);
        $userIndex = 0;
        $userfaults =[];

        for($i=0; $i < count($faults); $i++){

            $autoAssign  = $faults[$i];

            $userfaults[$autoAssign] = $users[$userIndex]; 

            $user = $users[$userIndex];

            $assign = Fault::find($autoAssign);
            $req['assignedTo'] = $userfaults[$autoAssign];
            $req['status_id'] = 3;
            $assign ->update($req);

            $userIndex ++;
        
            if($userIndex >= $userslength){
                $userIndex = 0;
            }
        }
        
    }

}

