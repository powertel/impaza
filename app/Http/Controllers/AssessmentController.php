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
use App\Services\FaultLifecycle;
use App\Models\AutoAssignSetting;
use Illuminate\Support\Facades\Log;


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
            ->leftjoin('fault_section','faults.id','=','fault_section.fault_id')
            ->leftjoin('users','faults.assignedTo','=','users.id')
            ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
            ->leftjoin('sections','fault_section.section_id','=','sections.id')
            ->leftjoin('customers','faults.customer_id','=','customers.id')
            ->leftjoin('links','faults.link_id','=','links.id')
            ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
            ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
            ->leftjoin('statuses','faults.status_id','=','statuses.id')
            ->leftjoin('cities','faults.city_id','=','cities.id')
            ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
            ->leftjoin('pops','faults.pop_id','=','pops.id')
            ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
            ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
            // Join open stage for current status to get start time
            ->leftjoin('fault_stage_logs as fsl', function($join) {
                $join->on('fsl.fault_id','=','faults.id');
                $join->on('fsl.status_id','=','faults.status_id');
                $join->whereNull('fsl.ended_at');
            })
            ->orderBy('faults.created_at', 'desc')
            ->where('faults.status_id','=',1)
            ->get(['faults.id','faults.fault_ref_number','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','faults.assignedTo',
                'account_manager_users.name as accountManager','faults.suspectedRfo_id','links.link','statuses.description','users.name','assessed_users.name as assessedBy','faults.status_id as status_id',
                'cities.city as city','cities.region as region','faults.city_id as city_id','suburbs.suburb as suburb','pops.pop as pop','faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at',
                'suspectedRFO.RFO as RFO','confirmedRFO.RFO as confirmedRFO', 'fsl.started_at as stage_started_at']);
        // Datasets required for modal-based actions on the assessments page
        $sections = Section::all();
        $confirmedRFO = ReasonsForOutage::all();

        // For edit/view modal parity, provide datasets used by faults.edit partial
        $customers = Customer::orderBy('customer','asc')->get();
        $cities = City::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        $links = Link::all();
        $accountManagers = AccountManager::all();
        $suspectedRFO = ReasonsForOutage::all();

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

        $faultAges = [];$faultAgeStart = [];$faultAgeEnd = [];
        $nocClearedId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
        $faultIdsList = $faults->pluck('id')->all();
        if (!empty($faultIdsList)) {
            $clearedLogs = DB::table('fault_stage_logs')
                ->whereIn('fault_id', $faultIdsList)
                ->where('status_id', $nocClearedId)
                ->select('fault_id','started_at')
                ->get()
                ->keyBy('fault_id');
            foreach ($faults as $f) {
                $start = \Carbon\Carbon::parse($f->created_at);
                $end = (isset($clearedLogs[$f->id])) ? \Carbon\Carbon::parse($clearedLogs[$f->id]->started_at) : \Carbon\Carbon::now();
                $days = $start->diffInDays($end);
                $hours = $start->copy()->addDays($days)->diffInHours($end) % 24;
                $minutes = $start->copy()->addDays($days)->addHours($hours)->diffInMinutes($end) % 60;
                $faultAges[$f->id] = ($days > 0 ? ($days.'d ') : '').($hours.'h ').($minutes.'m');
                $faultAgeStart[$f->id] = $start->format('c');
                $faultAgeEnd[$f->id] = isset($clearedLogs[$f->id]) ? \Carbon\Carbon::parse($clearedLogs[$f->id]->started_at)->format('c') : null;
            }
        }

        return view('assessments.index',compact('faults','sections','confirmedRFO','remarksByFault','faultAges','faultAgeStart','faultAgeEnd'))
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
                'city_id'=> 'required',
                'suburb_id'=> 'required',
                'pop_id'=> 'required',
                'link_id'=> 'required',
                'suspectedRfo_id'=> 'required',
                'serviceType'=> 'required',
                'serviceAttribute'=> 'required',
                'remark'=> 'required'
            ]);
            // Derive Account Manager from the selected customer
            $customer = Customer::find($request->input('customer_id'));
            $amUserId = $customer ? $customer->account_manager_id : null;
            $accountManagerId = null;
            if ($amUserId) {
                $user = User::find($amUserId);
                $accountManager = AccountManager::firstOrCreate(
                    ['user_id' => $amUserId],
                    ['accountManager' => $user ? $user->name : 'Account Manager']
                );
                $accountManagerId = $accountManager->id;
            }

            $data = $request->all();
            $data['accountManager_id'] = $accountManagerId;
            $fault = Fault::create($data);
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
        ->leftjoin('reasons_for_outages as suspectedRFO','faults.suspectedRfo_id','=','suspectedRFO.id')
        ->leftjoin('reasons_for_outages as confirmedRFO','faults.confirmedRfo_id','=','confirmedRFO.id')
        ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
        ->where('faults.id','=',$id)
        ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address','suspectedRFO.RFO','confirmedRFO.RFO as confirmedRFO',
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
            $validated = $request->validate([
                'priorityLevel' => ['required'],
                'faultType' => ['required'],
                'remark' => ['required','string'],
            ]);

            $fault = Fault::find($id);
            if (!$fault) {
                DB::rollBack();
                return redirect(route('assessments.index'))
                    ->with('fail', 'Fault not found');
            }

            $req = $request->only(['priorityLevel','faultType']);
            $req['status_id'] = 2;
            $req['assessed_by'] = $request->user()->id;
            $fault->update($req);
            FaultLifecycle::recordStatusChange($fault, 2, $request->user()->id);

            $sectionId = null;
            if ($request->input('faultType') === 'Logical') {
                $sectionId = 1;
            } elseif ($request->input('faultType') === 'Physical') {
                $sectionId = 2;
            }

            $fault_section = FaultSection::firstOrCreate(['fault_id' => $id]);
            $fault_section->update([
                'fault_id' => $fault->id,
                'section_id' => $sectionId,
            ]);

            if (!is_null($sectionId)) {
                $this->autoAssign($sectionId);
            }

            $remarkActivityId = (int) (DB::table('remark_activities')
                ->where('activity', '=', 'ON ASSESSMENT')
                ->value('id') ?? 0);

            Remark::create([
                'fault_id' => $fault->id,
                'user_id' => $request->user()->id,
                'remark' => $validated['remark'],
                'remarkActivity_id' => $remarkActivityId,
                'file_path' => null,
            ]);

            DB::commit();
            return redirect(route('assessments.index'))
            ->with('success','Fault Assessed');
        }
        catch(\Exception $ex)
        {
            DB::rollback();
            return redirect(route('assessments.index'))
                ->with('fail', 'Failed to assess fault');
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
        $scopeSectionId = (int)$section_id;

        // Fetch all enabled settings for this section
        // This handles the "scope disabled or missing" issue by checking ALL configured scopes for this section
        $settingsCollection = AutoAssignSetting::query()
            ->where('scope_section_id', $scopeSectionId)
            ->where('auto_assign_enabled', true)
            ->get();

        if ($settingsCollection->isEmpty()) {
            Log::info('Auto-assign skipped: no enabled settings for section', [
                'assessed_section_id' => $scopeSectionId,
            ]);
            return;
        }

        foreach ($settingsCollection as $settings) {
            $scopeRegion = $settings->scope_region; // e.g. 'East' or NULL

            $considerRegion = (bool)($settings->consider_region ?? true);
            $considerZones = (bool)($settings->consider_zones ?? false);
            $considerLeave = (bool)($settings->consider_leave ?? true);
            $isOffHours = \App\Services\FaultLifecycle::isOffHours();
            $isWeekendOff = (bool)($settings->weekend_standby_enabled ?? true) && now()->isWeekend();

            // 1. Get Eligible Technicians for this Scope
            $usersQuery = User::join('departments','users.department_id','=','departments.id')
                ->leftjoin('sections','users.section_id','=','sections.id')
                ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
                ->where('sections.id','=',$scopeSectionId)
                // Filter by region if defined in settings
                ->when(in_array($scopeSectionId, [2,3], true) && !empty($scopeRegion), function($q) use ($scopeRegion) {
                    $q->where('users.region', '=', $scopeRegion);
                })
                // Off-hours/Standby Logic
                ->where(function($q) use ($isOffHours) {
                    if ($isOffHours) {
                        $q->whereIn('user_statuses.status_name', ['Standby','Assignable']);
                    } else {
                        $q->where('user_statuses.status_name', '=', 'Assignable');
                    }
                })
                ->whereNotIn('user_statuses.status_name', $considerLeave ? ['Unassignable','On Leave'] : ['Unassignable'])
                ->when($isOffHours, function($q) use ($isWeekendOff) {
                    if ($isWeekendOff) {
                        $q->where('users.weekend_standby', '=', true);
                    } else {
                        $q->where('users.weekly_standby', '=', true);
                    }
                });

            $users = $usersQuery->pluck('users.id')->toArray();
            
            // Fallback: If no users found with strict checks, try relaxed checks (only if necessary)
            if (empty($users)) {
                 $users = User::join('departments','users.department_id','=','departments.id')
                    ->leftjoin('sections','users.section_id','=','sections.id')
                    ->leftjoin('user_statuses','users.user_status','=','user_statuses.id')
                    ->where('sections.id','=',$scopeSectionId)
                    ->where('user_statuses.status_name', '=', 'Assignable')
                    ->when(in_array($scopeSectionId, [2,3], true) && !empty($scopeRegion), function($q) use ($scopeRegion) {
                        $q->where('users.region', '=', $scopeRegion);
                    })
                    ->whereNotIn('user_statuses.status_name', $considerLeave ? ['Unassignable','On Leave'] : ['Unassignable'])
                    ->pluck('users.id')
                    ->toArray();
                 
                 if (!empty($users)) {
                    Log::warning('Auto-assign fallback: relaxed technician pool used', [
                        'section_id' => $scopeSectionId,
                        'scope_region' => $scopeRegion,
                    ]);
                 }
            }

            if (empty($users)) {
                // No techs for this setting scope
                continue; 
            }

            // 2. Find unassigned assessed faults matching this scope
            $faults = DB::table('fault_section')
                ->leftjoin('faults','fault_section.fault_id','=','faults.id')
                ->leftJoin('cities', 'faults.city_id', '=', 'cities.id')
                ->leftJoin('pops', 'faults.pop_id', '=', 'pops.id')
                ->where('faults.status_id','=',2) // Assessed
                ->whereNull('faults.assignedTo')
                ->where('fault_section.section_id','=',$scopeSectionId)
                ->when(in_array($scopeSectionId, [2,3], true) && !empty($scopeRegion), function($q) use ($scopeRegion) {
                    $q->where('cities.region', '=', $scopeRegion);
                })
                ->select('faults.id', 'cities.region as fault_region', 'pops.zone_id')
                ->get();

            if ($faults->isEmpty()) {
                continue;
            }

            // 3. Round-Robin Assignment Loop
            // Base RR key for this section/region
            $rrKeySuffix = in_array($scopeSectionId, [2,3], true) ? ($scopeRegion ?: 'all') : 'all';
            $rrKey = 'last_assigned_user_index_' . $scopeSectionId . '_' . $rrKeySuffix;
            $lastAssignedUserIndex = Cache::get($rrKey, 0);

            foreach ($faults as $f) {
                $autoAssign = $f->id;
                $faultRegion = $f->fault_region;
                $zoneId = $f->zone_id;
                
                $eligibleUsers = $users;
                $appliedZoneFilter = false;

                // Zone Logic
                if ($considerZones && $zoneId) {
                    $zoneUserIds = \DB::table('technician_zone')
                        ->where('zone_id', $zoneId)
                        ->whereIn('user_id', $eligibleUsers)
                        ->pluck('user_id')
                        ->toArray();
                    
                    if (!empty($zoneUserIds)) {
                        $eligibleUsers = $zoneUserIds;
                        $appliedZoneFilter = true;
                    }
                }

                // Region Logic (Fallback if Zone not applied)
                if (!$appliedZoneFilter && $considerRegion && $faultRegion && empty($scopeRegion)) {
                    // Only need to filter by faultRegion if scopeRegion was global/null
                    $regionUserIds = User::whereIn('id', $eligibleUsers)
                        ->where('region', $faultRegion)
                        ->pluck('id')
                        ->toArray();
                    
                    if (!empty($regionUserIds)) {
                        $eligibleUsers = $regionUserIds;
                    }
                }

                if (empty($eligibleUsers)) {
                    Log::info('Auto-assign skipped for fault: no eligible technicians', [
                        'fault_id' => $autoAssign,
                        'section_id' => $scopeSectionId,
                        'scope_region' => $scopeRegion,
                    ]);
                    continue;
                }

                // RR Key Selection
                $currentRrKey = $rrKey;
                $currentIndex = $lastAssignedUserIndex;
                if ($appliedZoneFilter) {
                    $currentRrKey = 'last_assigned_user_index_' . $scopeSectionId . '_zone_' . $zoneId;
                    $currentIndex = Cache::get($currentRrKey, 0);
                }

                // Re-index and pick
                $eligibleUsers = array_values($eligibleUsers);
                $idx = $currentIndex % count($eligibleUsers);
                $selectedUserId = $eligibleUsers[$idx];

                // Update Fault
                $assign = Fault::find($autoAssign);
                if (!$assign) continue;

                $req['assignedTo'] = $selectedUserId;
                $req['status_id'] = 3;
                $assign->update($req);
                
                FaultLifecycle::recordStatusChange($assign, 3, auth()->id());
                FaultLifecycle::startAssignment($assign, $selectedUserId, auth()->id(), $isOffHours, $faultRegion);

                // Update Index
                $nextIndex = $idx + 1;
                Cache::put($currentRrKey, $nextIndex);
                
                if (!$appliedZoneFilter) {
                    $lastAssignedUserIndex = $nextIndex;
                    // Update the loop variable too so next fault uses new index
                    Cache::put($rrKey, $lastAssignedUserIndex);
                }
                
                Log::info("Auto-assigned fault {$autoAssign} to user {$selectedUserId} (ZoneFilter: " . ($appliedZoneFilter?'Yes':'No') . ")");
            }
        }
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
