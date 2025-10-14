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
use App\Models\FaultSection;
use App\Models\ReasonsForOutage;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
 



class FaultController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:fault-list|fault-create|fault-edit|fault-delete', ['only' => ['index','store']]);
         $this->middleware('permission:fault-create', ['only' => ['create','store']]);
         $this->middleware('permission:fault-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:fault-delete', ['only' => ['destroy']]);
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
                ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
				->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
                ->leftjoin('account_managers', 'customers.account_manager_id','=','account_managers.id')
                ->leftjoin('users as account_manager_users','account_managers.user_id','=','account_manager_users.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->leftjoin('reasons_for_outages','faults.suspectedRfo_id','=','reasons_for_outages.id')
                ->leftjoin('cities','faults.city_id','=','cities.id')
                ->leftjoin('suburbs','faults.suburb_id','=','suburbs.id')
                ->leftjoin('pops','faults.pop_id','=','pops.id')
                ->orderBy('faults.created_at', 'desc')
                ->get([
                'faults.id',
                'faults.user_id',
                'faults.fault_ref_number',
                'customers.customer',
                'faults.customer_id',
                'faults.city_id',
                'faults.suburb_id',
                'faults.pop_id',
                'faults.link_id',
                'faults.status_id',
                'faults.contactName',
                'faults.phoneNumber',
                'faults.contactEmail',
                'faults.address',
                'account_manager_users.name as accountManager',
                'faults.suspectedRfo_id',
                'links.link',
                'statuses.description',
                'assigned_users.name as assignedTo',
                'reported_users.name as reportedBy',
                'faults.serviceType',
                'faults.serviceAttribute',
                'faults.faultType',
                'faults.priorityLevel',
                'faults.created_at',
                'cities.city',
                'suburbs.suburb',
                'pops.pop',
                'reasons_for_outages.RFO as RFO'
                ]);
        
        $city = City::all();
        $customer = DB::table('customers')
            ->orderBy('customers.customer', 'asc')
            ->get();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        $accountManager = AccountManager::all();
        $suspectedRFO = ReasonsForOutage::all();

        return view('faults.index',compact('faults','customer','city','accountManager','location','link','pop','suspectedRFO'))
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
      
        //Fault::create($request->all());

        DB::beginTransaction();
        try{
            request()->validate([
                'city_id' => 'required',
                'customer_id'=> 'required',
                'contactName'=> 'required',
                'phoneNumber'=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'contactEmail'=> 'required',
                'address'=> 'required',
                'suburb_id'=> 'required',
                'pop_id'=> 'required',
                'link_id'=> 'required',
                'suspectedRfo_id'=> 'required',
                'serviceType'=> 'required',
                'remark'=> 'required',
               'attachment' => 'null|mimes:png,jpg,jpeg|max:2048'
            ]);
           
            $req = $request->all();
        
            //This is where i am creating the fault
            $req['status_id'] = 1;
			$req['user_id'] =$request->user()->id;
			$req['fault_ref_number']="PWT".date("YmdHis");

            $fault = Fault::create($req);
            if($request->attachment){
                $path =  $request->file('attachment')->storePublicly('attachments','public');}
            else { $path = "NULL";}
            $remarkActivity_id = DB::table('remark_activities')->where('activity','=',$request['activity'])->get('remark_activities.id')->first();
            $remark = Remark::create(
                [
                    'fault_id'=> $fault->id,
                    'user_id' => $request->user()->id,
                    'remark' => $request['remark'],
                    'remarkActivity_id'=>$remarkActivity_id->id,
                    'file_path'=>$path
                ]
            );
           
        

            $fault_section = FaultSection::create(
                [
                    'fault_id'=> $fault->id,
                ]
            );
          //  $request->user()->posts()->create($request->only('body'));
            if($fault && $remark && $fault_section)
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

        catch(Exception $ex)
        {
            DB::rollback();
        }

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
            ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
            'account_managers.accountManager','faults.accountManager_id','faults.suspectedRfo_id','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','reasons_for_outages.RFO','faults.link_id','links.link'
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
            $suspectedRFO = ReasonsForOutage::all();


        return view('faults.edit',compact('fault','suspectedRFO','customers','cities','suburbs','pops','links','remarks','accountManagers'));


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

        // Derive Account Manager from the selected customer
        $customerId = $request->input('customer_id');
        $accountManagerId = null;
        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer && $customer->account_manager_id) {
                // Find or create the AccountManager record mapped to the user
                $amUserId = $customer->account_manager_id;
                $user = User::find($amUserId);
                $accountManager = AccountManager::firstOrCreate(
                    ['user_id' => $amUserId],
                    ['accountManager' => $user ? $user->name : 'Account Manager']
                );
                $accountManagerId = $accountManager->id;
            }
        }

        $data = $request->all();
        // Always set accountManager_id from customer association
        if ($accountManagerId) {
            $data['accountManager_id'] = $accountManagerId;
        }

        $fault->update($data);
        return redirect(route('faults.index'))
        ->with('success','Fault Updated');
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


    public function faults(Request $req)
    {
        $faults = DB::table('faults')
                ->leftjoin('customers','faults.customer_id','=','customers.id')
                ->leftjoin('links','faults.link_id','=','links.id')
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->orderBy('faults.created_at', 'desc')
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link','statuses.description'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);

        return response()->json($faults);
    }

    // Cascading selects helpers
    public function findSuburb($cityId)
    {
        $suburbs = Suburb::where('city_id', $cityId)->pluck('suburb', 'id');
        return response()->json($suburbs);
    }

    public function findPop($suburbId)
    {
        $pops = Pop::where('suburb_id', $suburbId)->pluck('pop', 'id');
        return response()->json($pops);
    }

    public function findLink($customerId)
    {
        $links = Link::where('customer_id', $customerId)->pluck('link', 'id');
        return response()->json($links);
    }
}
