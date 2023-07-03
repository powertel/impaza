<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use App\Models\Fault;
use App\Models\StoreStatus;

class StoreController extends Controller
{
    private $faultId;

    function __construct()
    {

         $this->middleware('permission:materials', ['only' => ['index','edit']]);
         $this->middleware('permission:request-material', ['only' => ['create','store']]);
         $this->middleware('permission:materials', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = DB::table('stores')
        ->leftjoin('faults','stores.fault_id','=','faults.id')
        ->leftjoin('store_statuses','stores.store_status','=','store_statuses.id')
        ->get([
        'faults.id',
        'faults.fault_ref_number',
        'faults.faultType',
        'stores.materials',
        'stores.SAP_ref',
        'store_statuses.store_status',
        ]);
        return view('stores.index',compact('stores'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */




    public function create(Request $request)
    {
        $this->faultId = $request->f_id;
    $stores = DB::table('stores')
    ->leftjoin('faults','stores.fault_id','=','faults.id')
    ->leftjoin('store_statuses','stores.store_status','=','store_statuses.id')
    ->orderBy('stores.created_at', 'desc')
    ->get([
    'faults.fault_ref_number',
    'faults.faultType',
    'stores.materials',
    'stores.id',
    'stores.SAP_ref',
    ])->first();
    return view('stores.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



       //dd($request->f_id);
        request()->validate([
            'SAP_ref' => 'required',
            'materials' => 'required',
        ]);
        $req = $request->all();
        $req['fault_id'] = $request->f_id;
        $req['store_status'] = 1;
        $store = Store::create($req);

        if($store)
        {
            return redirect()->route('my_faults.index')
            ->with('success','Material Requested');
        }
        else
        {
            return back()->with('fail','Something went wrong');
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
        $stores = DB::table('stores')
            ->leftjoin('faults','stores.fault_id','=','faults.id')
            ->leftjoin('users as request_users','stores.user_id','=','request_users.id')
            ->where('faults.id','=',$id)
            ->get(['stores.id','stores.materials','stores.SAP_ref','request_users.name as requestedBy','stores.created_at','stores.fault_id','faults.fault_ref_number','faults.faultType','faults.suspectedRfo','faults.serviceType','faults.confirmedRfo','faults.faultType','faults.priorityLevel'])
            ->first();
           return view('stores.show',compact('stores'));
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
        ->leftjoin('stores','faults.fault_id','=','stores.id')
        ->where('stores.id','=',$id)
        ->get(['stores.id','faults.fault_ref_number','faults.faultType','stores.requisition_number','stores.SAP_ref','stores.created_at'])
        ->first();
        return view('my_faults.index',compact('stores','faults'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
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
    public function deny(Request $request, $id)
    {

        $store = Store::find($id);
        $req= $request->all();
        $req['store_status'] = 3;
        $store ->update($req);
        return redirect(route('stores.index'))
        ->with('success','Request Denied');
    }

    public function issue(Request $request, $id)
    {

        $store = Store::find($id);
        $req= $request->all();
        $req['store_status'] = 2;
        $store ->update($req);
        return redirect(route('stores.index'))
        ->with('success','Request Granted');
    }
}
