<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Fault;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use App\Models\Remark;
use App\Models\AccountManager;
use App\Models\Section;
use App\Models\FaultSection;
use App\Models\ReasonsForOutage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AssessmentController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:fault-assessment', ['only' => ['edit','update']]);
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
                ->orderBy('faults.created_at', 'desc')
                ->where('users.id','=',auth()->user()->id)
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('assessments.index',compact('faults'))
        ->with('i'); */

        $faults = DB::table('faults')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','faults.confirmedRfo_id','=','reasons_for_outages.id')
            ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.status_id','=',1)
            ->get(['faults.id','customers.customer','faults.contactName','reasons_for_outages.RFO','faults.phoneNumber','faults.contactEmail','faults.address',
            'account_managers.accountManager','faults.suspectedRfo_id','links.link','statuses.description'
            ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('assessments.index',compact('faults'))
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
        DB::beginTransaction();
        try{
            request()->validate([
                'city_id' => 'required',
                'customer_id'=> 'required',
                'contactName'=> 'required',
                'phoneNumber'=> 'required',
                'contactEmail'=> 'required',
                'address'=> 'required',
                'accountManager_id'=> 'required',
                'city_id'=> 'required',
                'suburb_id'=> 'required',
                'pop_id'=> 'required',
                'link_id'=> 'required',
                'suspectedRfo_id'=> 'required',
                'serviceType'=> 'required',
                'serviceAttribute'=> 'required',
                'remark'=> 'required'
            ]);
            $fault = Fault::create($request->all());
            $remark = Remark::create(
                [
                    'fault_id'=> $fault->id,
                    'user_id' => $request->user()->id,
                    'remark' => $request['remark'],
                ]
            );
          //  $request->user()->posts()->create($request->only('body'));
            if($fault&&$remark)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('faults.index')
            ->with('success', 'Fault Created');
        }

        catch(\Exception $ex)
        {
            DB::rollback();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fault = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('cities','faults.city_id','=','cities.id')
                ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
                ->leftjoin('pops','faults.pop_id','=','pops.id')
                ->leftjoin('remarks','remarks.fault_id','=','faults.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','faults.confirmedRfo_id','=','reasons_for_outages.id')
                ->where('faults.id','=',$id)
                ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','faults.suspectedRfo_id','faults.link_id','links.link'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','remarks.fault_id','remarks.remark','reasons_for_outages.RFO','faults.created_at'])
                ->first();

               $remarks= DB::table('remarks')
               ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
               ->leftjoin('users','remarks.user_id','=','users.id')
               ->where('remarks.fault_id','=',$id)
               ->get(['remarks.id','remarks.created_at','remarks.remark','remarks.file_path','users.name','remark_activities.activity']);
        return view('assess.show',compact('fault','remarks'));
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
        ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
        ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
        ->where('faults.id','=',$id)
        ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','reasons_for_outages.RFO',
        'account_managers.accountManager','faults.accountManager_id','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','faults.suspectedRfo_id','faults.link_id','links.link'
        ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','remarks.fault_id','remarks.remark','faults.created_at'])
        ->first();
        $remarks= DB::table('remarks')
        ->leftjoin('remark_activities','remarks.remarkActivity_id','=','remark_activities.id')
        ->leftjoin('users','remarks.user_id','=','users.id')
        ->where('remarks.fault_id','=',$id)
        ->get(['remarks.id','remarks.created_at','remarks.remark','remarks.file_path','users.name','remark_activities.activity']);
        $cities = City::all();
        $customers = Customer::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        $links = Link::all();
        $accountManagers = AccountManager::all();
        $sections = Section::all();
        $confirmedRFO = ReasonsForOutage::all();
        $suspectedRFO = ReasonsForOutage::all();


    return view('assessments.assess',compact('fault','customers','confirmedRFO','cities','suburbs','suspectedRFO','pops','links','remarks','accountManagers','sections'));
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


      
        DB::beginTransaction();
        try{
            request()->validate([
                'section_id'=> 'required',
                'priorityLevel'=>'required',
                'faultType'=>'required',
                'confirmedRfo_id'=>'required'
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
			
			$this->autoAssign($request['section_id']);

          if($fault  && $fault_section)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect(route('assessments.index'))
            ->with('success','Fault Assessed');
        }
        catch(\Exception $ex)
        {
            DB::rollback();
        }

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
	
	private function autoAssign($section_id)
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
            // Retrieve the last assigned user index from persistent storage
        $lastAssignedUserIndex = Cache::get('last_assigned_user_index', 0);
        $userfaults =[];

        for($i=0; $i < count($faults); $i++){

            $autoAssign  = $faults[$i];

            $userfaults[$autoAssign] = $users[$lastAssignedUserIndex]; 

            $user = $users[$lastAssignedUserIndex];

            $assign = Fault::find($autoAssign);
            $req['assignedTo'] = $userfaults[$autoAssign];
            $req['status_id'] = 3;
            $assign ->update($req);

            $lastAssignedUserIndex ++;
        
            if($lastAssignedUserIndex >= $userslength){
                $lastAssignedUserIndex = 0;
            }
        }
        // Store the updated last assigned user index in persistent storage
        Cache::put('last_assigned_user_index', $lastAssignedUserIndex);
    }


    public function assign(){

        $users = User::join('departments','users.department_id','=','departments.id')
        ->leftjoin('sections','users.section_id','=','sections.id')
        ->where('sections.id','=','3')
        ->pluck('users.id')
        ->toArray();
//dd($users);
         $faults = DB::table('fault_section')
         ->leftjoin('faults','fault_section.fault_id','=','faults.id')
         ->whereNull('faults.assignedTo')
         ->where('fault_section.section_id','=', '3')
        ->pluck('faults.id')
        ->toArray();

        $userfaults =[];
        $userslength=count($users);
        $userIndex = 0;


        for($i=0; $i < count($faults); $i++){

            $autoAssign  = $faults[$i];

            $userfaults[$autoAssign] = $users[$userIndex];
            //$assign = $users[$userIndex];
            $userIndex ++;

            if($userIndex >= $userslength){
                $userIndex = 0;
            }

        }

        return $userfaults;

    }
}
