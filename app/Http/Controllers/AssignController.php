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
use App\Models\User;
use App\Models\Section;
use App\Models\FaultSection;
use App\Models\UserStatus;
use DB;

class AssignController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:assigned-fault-list|assign-fault-create|assign-fault-edit|assign-fault-delete', ['only' => ['index','store']]);
         $this->middleware('permission:assign-fault', ['only' => ['edit','update']]); 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faults = DB::table('faults')
        ->leftjoin('fault_section','faults.id','=','fault_section.fault_id')
        ->leftjoin('users','faults.assignedTo','=','users.id')
        ->leftjoin('sections','fault_section.section_id','=','sections.id')
        ->leftjoin('customers','faults.customer_id','=','customers.id')
        ->leftjoin('links','faults.link_id','=','links.id')
        ->leftjoin('confirmed_rfos','faults.confirmedRfo_id','=','confirmed_rfos.id')
        ->leftjoin('suspected_rfos','faults.suspectedRfo_id','=','suspected_rfos.id')
        ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
        ->leftjoin('statuses','faults.status_id','=','statuses.id')
        ->orderBy('faults.created_at', 'desc')
        ->where('fault_section.section_id','=',auth()->user()->section_id)
       ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','faults.assignedTo',
       'account_managers.accountManager','faults.suspectedRfo_id','links.link','confirmed_rfos.ConfirmedRFO','suspected_rfos.SuspectedRFO','statuses.description','faults.assignedTo','users.name'
       ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);


       $autoAssign = $this->autoAssign(auth()->user()->section_id);
        return view('assign.index',compact('faults'))
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
        $fault = DB::table('faults')
        ->leftjoin('customers','faults.customer_id','=','customers.id')
        ->leftjoin('links','faults.link_id','=','links.id')
        ->leftjoin('cities','faults.city_id','=','cities.id')
        ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
        ->leftjoin('pops','faults.pop_id','=','pops.id')
        ->leftjoin('remarks','remarks.fault_id','=','faults.id')
        ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
        ->leftjoin('users','faults.assignedTo','=','users.id')
        ->where('faults.id','=',$id)
        ->get(['faults.id','faults.customer_id','customers.customer','faults.suspectedRfo_id','links.link','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','users.name',
        'account_managers.accountManager','faults.accountManager_id','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','faults.suspectedRfo_id','faults.link_id','links.link'
        ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','remarks.fault_id','remarks.remark','faults.created_at'])
        ->first();

        $cities = City::all();
        $customers = Customer::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        $links = Link::all();
        $remarks= Remark::all();
        $accountManagers = AccountManager::all();

        $technicians = DB::table('users')
                    ->leftJoin('sections','users.section_id','=','sections.id')
                    ->where('users.section_id','=',auth()->user()->section_id)
                    ->where('user_statuses.status_name','=','active')
                    ->get(['users.id','users.name']);
                    //dd($technicians);

        return view('assign.assign',compact('fault','customers','cities','suburbs','pops','links','remarks','accountManagers','technicians'));
    
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
        
        request()->validate([
            'assignedTo'=> 'required',
        ]);
        $fault = Fault::find($id);
        $req= $request->all();
        $req['status_id'] = 3;
        $fault ->update($req);
        return redirect(route('department_faults.index'))
        ->with('success','Fault Assigned');
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



    public function autoAssign($section_id)
    {
   
        $users = User::join('departments','users.department_id','=','departments.id')
            ->leftjoin('sections','users.section_id','=','sections.id')
            ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('sections.id','=',$section_id)
            ->where('user_statuses.id','=',1)
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


/* 
public function autoAssign(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            request()->validate([
                'section_id'=> 'required',
                'priorityLevel'=>'required',
                'faultType'=>'required',
                'confirmedRfo'=>'required'
            ]);

            $fault = Fault::find($id);
            $req= $request->all();
            $req['status_id'] = 2;
            $fault ->update($req);



            $fault_section = FaultSection::find($id);
            $fault_section -> update(
                [
                    'fault_id'=> $fault->id,
                    'section_id' => $request['section_id'],
                ]
            );

            $users = User::join('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->where('sections.id','=',1)
                ->pluck('users.id')
                ->toArray();

            $faults = DB::table('fault_section')
                ->leftjoin('faults','fault_section.fault_id','=','faults.id')
                ->whereNull('faults.assignedTo')
                ->where('fault_section.section_id','=',3)
                ->pluck('faults.id')
                ->toArray();

            $userslength=count($users);
            $userIndex = 0;

            for($i=0; $i < count($faults); $i++){
    
                $autoAssign  = $faults[$i];
    
                $autoAssign = Fault::find($autoAssign );

                $assign = DB::table('faults')
                        ->where('faults.id', $id)
                        ->get();

                        //->update(array('member_type' => $plan));
      
                $req[$autoAssign] = $users[$userIndex]; 
                $userIndex ++;
          
                if($userIndex >= $userslength){
                    $userIndex = 0;
                }
  
            }


            if($fault  && $fault_section && $userfaults)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect(route('department_faults.index')) 
            ->with('success','Fault Assessed');
        }
        catch(Exception $ex)
        {
            DB::rollback();
        }
//$this->assign();
    }
 */
}


