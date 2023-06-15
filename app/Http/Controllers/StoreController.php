<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Store;
use App\Models\Fault;

class StoreController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:materials', ['only' => ['index','store']]);
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

        ->get([
        'faults.id',
        'faults.fault_ref_number',
        'faults.faultType',
        'stores.materials',
        'stores.SAP_ref',
        ]);

        //dd($faults);


        return view('stores.index',compact('stores'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::all();
        return view('stores.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Fault $fault)
    {

              Store::create(
            [
            // 'fault_id'=> $fault->id,
            // 'user_id' => $request->user()->id,
            'SAP_ref' =>  $request['SAP_ref'],
            'materials'=>  $request['materials'],
            ]

        );

        return back();
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
        ->leftjoin('stores','stores.stores_id','=','stores.id')
        ->where('stores.id','=',$id)
        ->get(['stores.id','faults.fault_ref_number','faults.faultType','stores.requisition_number','stores.SAP_ref','stores.created_at'])
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
}
