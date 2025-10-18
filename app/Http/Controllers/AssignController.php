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
use Illuminate\Support\Facades\DB;
use App\Services\FaultLifecycle;
use Illuminate\Support\Facades\Log;
use App\Models\AutoAssignSetting;

class AssignController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:assigned-fault-list', ['only' => ['index','create']]);
        $this->middleware('permission:assign-fault', ['only' => ['store','assignFault']]);
        $this->middleware('permission:re-assign-fault', ['only' => ['edit','update']]); 
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
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
            ->orderBy('faults.created_at', 'desc')
            ->where('fault_section.section_id','=',auth()->user()->section_id)
            ->where('cities.region','=',auth()->user()->region)
            ->whereNotNull('faults.assignedTo')
            ->where('faults.status_id','=',3)
            ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','faults.assignedTo',
                'account_manager_users.name as accountManager','faults.suspectedRfo_id','links.link','statuses.description','users.name','faults.status_id as status_id',
                'cities.city as city','cities.region as region','faults.city_id as city_id','suburbs.suburb as suburb','pops.pop as pop','faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at',
                'reasons_for_outages.RFO as RFO']);

        // Collect remarks grouped by fault_id for conversation modal
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

        // Technicians list for modal re-assign (current section, Assignable)
        $technicians = DB::table('users')
            ->leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.section_id','=',auth()->user()->section_id)
            ->where('users.region','=',auth()->user()->region)
            ->where('user_statuses.status_name','=','Assignable')
            ->orderBy('users.name','asc')
            ->get(['users.id','users.name']);

        return view('assign.index',compact('faults','technicians','remarksByFault'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // List faults waiting to be assigned (status = 2)
        $faults = DB::table('faults')
            ->leftjoin('fault_section','faults.id','=','fault_section.fault_id')
            ->leftjoin('users','faults.assignedTo','=','users.id')
            ->leftjoin('sections','fault_section.section_id','=','sections.id')
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
            ->where('fault_section.section_id','=',auth()->user()->section_id)
            ->where('faults.status_id','=',2)
            ->whereNull('faults.assignedTo')
            ->where('cities.region','=',auth()->user()->region)
            ->get(['faults.id','faults.fault_ref_number','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','faults.assignedTo',
                'account_manager_users.name as accountManager','faults.suspectedRfo_id','links.link','statuses.description','users.name','faults.status_id as status_id',
                'cities.city as city','cities.region as region','faults.city_id as city_id','suburbs.suburb as suburb','pops.pop as pop','faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at',
                'reasons_for_outages.RFO as RFO']);

        // Collect remarks grouped by fault_id for conversation modal
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

        // Technicians list for assignment (current section, Assignable)
        $technicians = DB::table('users')
            ->leftJoin('sections','users.section_id','=','sections.id')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.section_id','=',auth()->user()->section_id)
            ->where('users.region','=',auth()->user()->region)
            ->where('user_statuses.status_name','=','Assignable')
            ->orderBy('users.name','asc')
            ->get(['users.id','users.name']);

        return view('assign.waiting',compact('faults','technicians','remarksByFault'))
            ->with('i');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'fault_id' => 'required|integer|exists:faults,id',
            'assignedTo' => 'required|integer|exists:users,id',
        ]);

        $fault = Fault::find($request->input('fault_id'));
        if (!$fault) {
            return back()->withErrors(['error' => 'Fault not found'])->withInput();
        }

        // Only allow assigning if the fault is unassigned and in Assessments (status 2)
        if ((int)$fault->status_id !== 2 || !is_null($fault->assignedTo)) {
            return back()->withErrors(['error' => 'Fault is not in an assignable state']).withInput();
        }

        // Enforce region parity with logged-in user
        $faultRegion = \DB::table('cities')->where('id', $fault->city_id)->value('region');
        if ($faultRegion !== auth()->user()->region) {
            return back()->withErrors(['error' => 'You can only assign faults in your region'])->withInput();
        }

        // Ensure selected technician is in current section/region and eligible
        $isTechEligible = \DB::table('users')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.id', '=', $request->input('assignedTo'))
            ->where('users.section_id', '=', auth()->user()->section_id)
            ->where('users.region', '=', auth()->user()->region)
            ->where('user_statuses.status_name', '=', 'Assignable')
            ->exists();

        if (!$isTechEligible) {
            return back()->withErrors(['assignedTo' => 'Selected technician is not eligible']).withInput();
        }

        // Transition to Rectification and assign technician
        $fault->update([
            'assignedTo' => (int)$request->input('assignedTo'),
            'status_id' => 3,
        ]);

        FaultLifecycle::recordStatusChange($fault, 3, $request->user()->id);
        FaultLifecycle::startAssignment(
            $fault,
            (int)$request->input('assignedTo'),
            $request->user()->id,
            FaultLifecycle::isOffHours(),
            $faultRegion
        );

        return redirect()->back()->with('success', 'Fault Assigned');
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
                    'faults.serviceType',
                    'faults.serviceAttribute',
                    'faults.faultType',
                    'faults.priorityLevel',
                    'faults.created_at',
                    'cities.city as city',
                    'suburbs.suburb as suburb',
                    'pops.pop as pop',
                    'reasons_for_outages.RFO as RFO'
                ]);


        $cities = City::all();
        $customers = Customer::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        $links = Link::all();
        $remarks= Remark::all();
        $accountManagers = AccountManager::all();
		
		$technicians = DB::table('users')
                    ->leftJoin('sections','users.section_id','=','sections.id')
					->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
                    ->where('users.section_id','=',auth()->user()->section_id)
                    ->where('user_statuses.status_name','=','Assignable')
                    ->get(['users.id','users.name']);

                    // dd($technicians); // removed debug
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
        $req = $request->only(['assignedTo']);

        // Determine if this is an initial assignment (status transitions to rectification)
        $isInitialAssign = ($fault && (int)$fault->status_id !== 3);

        if ($isInitialAssign) {
            // Move fault to Rectification and start a new rectification stage log entry
            $fault->status_id = 3;
            $fault->update($req + ['status_id' => 3]);
            FaultLifecycle::recordStatusChange($fault, 3, $request->user()->id);
        } else {
            // Re-assign within the same rectification stage — keep stage timing continuous
            $fault->update($req);
        }

        // Close any open assignment and start a new one for the selected technician
        $region = \DB::table('cities')->where('id', $fault->city_id)->value('region');
        FaultLifecycle::startAssignment($fault, (int)$req['assignedTo'], $request->user()->id, FaultLifecycle::isOffHours(), $region);
        return redirect()->back()
        ->with('success','Fault Re-Assigned');
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





public function assignFault(Request $request)
    {
        $request->validate([
            'fault_id' => 'required|integer|exists:faults,id',
            'assignedTo' => 'required|integer|exists:users,id',
        ]);

        $faultId = (int)$request->input('fault_id');
        $techId = (int)$request->input('assignedTo');
        $userId = optional($request->user())->id;

        $fault = Fault::find($faultId);
        if (!$fault) {
            Log::warning('Assign failed: fault not found', [
                'fault_id' => $faultId,
                'tech_id' => $techId,
                'user_id' => $userId,
                'origin' => $request->getSchemeAndHttpHost(),
                'path' => $request->path(),
            ]);
            return back()->withErrors(['error' => 'Fault not found'])->withInput();
        }

        // Only allow assigning if the fault is unassigned and in Assessments (status 2)
        if ((int)$fault->status_id !== 2 || !is_null($fault->assignedTo)) {
            Log::warning('Assign failed: fault not in assignable state', [
                'fault_id' => $fault->id,
                'status_id' => (int)$fault->status_id,
                'assignedTo_current' => $fault->assignedTo,
                'tech_id' => $techId,
                'user_id' => $userId,
            ]);
            return back()->withErrors(['error' => 'Fault is not in an assignable state'])->withInput();
        }

        // Enforce region parity with logged-in user
        $faultRegion = \DB::table('cities')->where('id', $fault->city_id)->value('region');
        if ($faultRegion !== auth()->user()->region) {
            Log::warning('Assign failed: region mismatch', [
                'fault_id' => $fault->id,
                'fault_region' => $faultRegion,
                'user_region' => auth()->user()->region,
                'user_id' => $userId,
            ]);
            return back()->withErrors(['error' => 'You can only assign faults in your region'])->withInput();
        }

        // Ensure selected technician is in current section/region and eligible
        $isTechEligible = \DB::table('users')
            ->leftJoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('users.id', '=', $techId)
            ->where('users.section_id', '=', auth()->user()->section_id)
            ->where('users.region', '=', auth()->user()->region)
            ->where('user_statuses.status_name', '=', 'Assignable')
            ->exists();

        if (!$isTechEligible) {
            Log::warning('Assign failed: technician not eligible', [
                'fault_id' => $fault->id,
                'tech_id' => $techId,
                'user_section' => auth()->user()->section_id,
                'user_region' => auth()->user()->region,
                'user_id' => $userId,
            ]);
            return back()->withErrors(['assignedTo' => 'Selected technician is not eligible']).withInput();
        }

        // Transition to Rectification and assign technician
        $fault->update([
            'assignedTo' => $techId,
            'status_id' => 3,
        ]);

        FaultLifecycle::recordStatusChange($fault, 3, $userId);
        FaultLifecycle::startAssignment(
            $fault,
            $techId,
            $userId,
            FaultLifecycle::isOffHours(),
            $faultRegion
        );

        Log::info('Assign success', [
            'fault_id' => $fault->id,
            'tech_id' => $techId,
            'user_id' => $userId,
            'status_id' => 3,
        ]);

        return redirect()->back()->with('success', 'Fault Assigned');
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


