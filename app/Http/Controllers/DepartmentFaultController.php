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

        // Pagination and search parameters
        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q', ''));

        // Base query scoped to the user's section
        $faultsQuery = DB::table('faults')
            ->leftjoin('fault_section','faults.id','=','fault_section.fault_id')
            ->leftJoin('fault_referrals as fr', function($join) {
                $join->on('fr.fault_id','=','faults.id');
                $join->whereNull('fr.completed_at');
            })
            ->leftjoin('users','faults.assignedTo','=','users.id')
            ->leftjoin('sections','fault_section.section_id','=','sections.id')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
            ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->orderBy('faults.created_at', 'desc')
            ->where(function($q){
                $q->where('fault_section.section_id','=',auth()->user()->section_id)
                  ->orWhere('fr.to_section_id','=',auth()->user()->section_id);
            })
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.address',
                'faults.assignedTo',
                'account_manager_users.name as accountManager',
                'faults.suspectedRfo_id',
                'links.link',
                'statuses.description',
                'users.name',
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
                'fr.id as referral_id'
            ]);

        // Apply search across common visible columns and related names
        if ($q !== '') {
            $like = "%".$q."%";
            $faultsQuery->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('statuses.description', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like)
                   // Additional contact fields to broaden matching
                   ->orWhere('faults.contactName', 'like', $like)
                   ->orWhere('faults.phoneNumber', 'like', $like)
                   ->orWhere('faults.contactEmail', 'like', $like)
                   ->orWhere('faults.address', 'like', $like);
            });
        }

        $faults = $faultsQuery->paginate($perPage)->withQueryString();

        // Collect remarks for all listed faults and group by fault_id for faults.show
        $faultIds = $faults->getCollection()->pluck('id');
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

        return view('department_faults.index',compact('faults','remarksByFault','perPage'))
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

    public function completeReferral(Request $request, $referralId)
    {
        $request->validate([
            'remark' => ['required','string']
        ]);

        $ref = \App\Models\FaultReferral::find($referralId);
        if (!$ref) {
            return back()->with('fail', 'Referral not found');
        }
        $fault = Fault::find($ref->fault_id);
        if (!$fault) {
            return back()->with('fail', 'Fault not found');
        }

        $ref->completed_at = now();
        $ref->save();

        $prev = (int)($ref->previous_status_id ?? 3);
        $fault->update(['status_id' => $prev]);
        \App\Services\FaultLifecycle::reopenStageForStatus($fault, $prev, $request->user()->id);
        \App\Services\FaultLifecycle::reopenAssignment($fault);

        Remark::create([
            'fault_id' => $fault->id,
            'user_id' => $request->user()->id,
            'remark' => 'Referral completed: '.$request->input('remark'),
        ]);

        return back()->with('success', 'Referral work completed and fault returned');
    }

    public function referred(Request $request)
    {
        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q', ''));

        $faultsQuery = DB::table('faults')
            ->leftjoin('users','faults.assignedTo','=','users.id')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
            ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->leftJoin('fault_referrals as fr', function($join) {
                $join->on('fr.fault_id','=','faults.id');
                $join->whereNull('fr.completed_at');
            })

            ->leftjoin('fault_stage_logs as fsl', function($join) {
                    $join->on('fsl.fault_id','=','faults.id');
                    $join->on('fsl.status_id','=','faults.status_id');
                    $join->whereNull('fsl.ended_at');
                })
            ->orderBy('faults.created_at', 'desc')
            ->where('fr.to_section_id','=',auth()->user()->section_id)
            ->select([
                'faults.id',
                'faults.fault_ref_number',
                'customers.customer',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.address',
                'faults.assignedTo',
                'account_manager_users.name as accountManager',
                'faults.suspectedRfo_id',
                'links.link',
                'statuses.description',
                'users.name',
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
                'fr.id as referral_id',
                'fsl.started_at as stage_started_at'
            ]);

        if ($q !== '') {
            $like = "%".$q."%";
            $faultsQuery->where(function($qq) use ($like) {
                $qq->where('faults.fault_ref_number', 'like', $like)
                   ->orWhere('customers.customer', 'like', $like)
                   ->orWhere('account_manager_users.name', 'like', $like)
                   ->orWhere('links.link', 'like', $like)
                   ->orWhere('users.name', 'like', $like)
                   ->orWhere('statuses.description', 'like', $like)
                   ->orWhere('cities.city', 'like', $like)
                   ->orWhere('suburbs.suburb', 'like', $like)
                   ->orWhere('pops.pop', 'like', $like);
            });
        }

        $faults = $faultsQuery->paginate($perPage)->withQueryString();

        $faultIds = $faults->getCollection()->pluck('id');
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

        return view('department_faults.referred',compact('faults','remarksByFault','perPage'))
            ->with('i');
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

        // Explicit scope fields take precedence; fallback to updater
        $scopeSectionId = $settings->scope_section_id ?? null;
        $scopeRegion = $settings->scope_region ?? null;
        if (empty($scopeSectionId) || empty($scopeRegion)) {
            $scopeUser = null;
            if (!empty($settings->updated_by)) {
                $scopeUser = \App\Models\User::find((int)$settings->updated_by);
            }
            $scopeSectionId = $scopeSectionId ?: ($scopeUser->section_id ?? null);
            $scopeRegion = $scopeRegion ?: ($scopeUser->region ?? null);
        }

        // If the requested section does not match scope section, do nothing
        if (!empty($scopeSectionId) && (int)$scopeSectionId !== (int)$section_id) {
            return;
        }
   
        $users = User::join('departments','users.department_id','=','departments.id')
            ->leftjoin('sections','users.section_id','=','sections.id')
            ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
            ->where('sections.id','=',$section_id)
            ->where('user_statuses.status_name','=','active')
            // Limit to scope region if available
            ->when(!empty($scopeRegion), function($q) use ($scopeRegion) {
                $q->where('users.region', '=', $scopeRegion);
            })
            ->pluck('users.id')
            ->toArray();

        $faults = DB::table('fault_section')
            ->leftjoin('faults','fault_section.fault_id','=','faults.id')
            ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
            ->whereNull('faults.assignedTo')
            ->where('fault_section.section_id','=',$section_id)
            // Limit to scope region if available
            ->when(!empty($scopeRegion), function($q) use ($scopeRegion) {
                $q->where('cities.region', '=', $scopeRegion);
            })
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

