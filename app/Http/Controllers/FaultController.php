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
use DB;

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
                ->leftjoin('account_managers','faults.accountManager_id','=','account_managers.id')
                ->leftjoin('statuses','faults.status_id','=','statuses.id')
                ->orderBy('faults.created_at', 'desc')
                ->get(['faults.id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.suspectedRfo','links.link','statuses.description'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.created_at']);
        return view('faults.index',compact('faults'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $city = City::all();
        $customer = DB::table('customers')
            ->orderBy('customers.customer', 'asc')
            ->get();
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        $accountManager = AccountManager::all();
        return view('faults.create',compact('customer','city','accountManager','location','link','pop'));
    }

    public function findSuburb($id)
    {
        $suburb = Suburb::where('city_id',$id)
        ->pluck("suburb","id");
        return response()->json($suburb);
    }

    public function findPop($id)
    {
        $pop = Pop::where('suburb_id',$id)
        ->pluck("pop","id");
        return response()->json($pop);
    }

    public function findLink($id)
    {
        $link = Link::where('customer_id',$id)
        ->where('links.link_status','=',2)
        ->pluck("link","id");
        return response()->json($link);
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
                'accountManager_id'=> 'required',
                'city_id'=> 'required',
                'suburb_id'=> 'required',
                'pop_id'=> 'required',
                'link_id'=> 'required',
                'suspectedRfo'=> 'required',
                'serviceType'=> 'required',
                'remark'=> 'required',
            ]);

            $req = $request->all();
            //I used the approach ye last time
            //This is where i am creating the fault
            $req['status_id'] = 1;

            $fault = Fault::create($req);
            $remark = Remark::create(
                [
                    'fault_id'=> $fault->id,
                    'user_id' => $request->user()->id,
                    'remark' => $request['remark'],
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
                ->where('faults.id','=',$id)
                ->get(['faults.id','faults.customer_id','customers.customer','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'account_managers.accountManager','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','faults.suspectedRfo','faults.link_id','links.link'
                ,'faults.serviceType','faults.confirmedRfo','faults.faultType','faults.priorityLevel','remarks.fault_id','remarks.remark','faults.created_at'])
                ->first();

               $remarks= Remark::all();
        return view('faults.show',compact('fault','remarks'));
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

        return view('faults.edit',compact('fault','customers','cities','suburbs','pops','links','remarks','accountManagers'));

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
        $fault ->update($request->all());
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
}
