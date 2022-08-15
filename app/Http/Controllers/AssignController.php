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
use DB;

class AssignController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:assign-fault-list|assign-fault-create|assign-fault-edit|assign-fault-delete', ['only' => ['index','store']]);
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
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->orderBy('faults.created_at', 'desc')
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
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
        ->where('faults.id','=',$id)
        ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
        'account_managers.accountManager','faults.accountManager_id','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','faults.suspectedRfo','faults.link_id','links.link'
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
    private function getLatestUserId(int $section_id): ?int
    {
        return FaultSection::where('section_id', $section_id)
            ->latest('fault_id')
            ->first()?->user_id;
    }

    private function getNextUserId(int $section_id, int $latestUserId): ?int
    {
        return DB::table('section_user')
            ->where('section_id', $section_id)
            ->where('user_id', ">", $latestUserId)
            ->orderBy('user_id')
            ->first()?->user_id;
    }

    private function getFirstUserId($section_id): ?int
    {
        return DB::table('section_user')
            ->where('section_id', $section_id)
            ->orderBy('user_id')
            ->first()
            ?->user_id;
    }

    public function getMeetingUserId(int $section_id): ?int
    {
        $latestUserId = $this->getLatestUserId($section_id);
        if ($latestUserId === null) {
            // First time the meeting is being held
            return $this->getFirstUserId($section_id);
        }

        $nextUserId = $this->getNextUserId($section_id, $latestUserId);
        if ($nextUserId === null) {
            // All users have had their turn, starting over
            return $this->getFirstUserId($section_id);
        }

        return $nextUserId;
    }
}
