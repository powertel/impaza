<?php

namespace App\Http\Controllers;

use App\Models\Fault;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use Illuminate\Http\Request;
use DB;

class FaultController extends Controller
{
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
                ->orderBy('faults.created_at', 'desc')
                ->get(['faults.id','customers.customerName','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'faults.accountManager','faults.suspectedRfo','links.linkName'
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
        $customer = Customer::all();
        return view('faults.create',compact('customer','city'));
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
        ->pluck("linkName","id");
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
        Fault::create($request->all());
    
        return redirect()->route('faults.index');
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
                ->where('faults.id','=',$id)
                ->get(['faults.id','customers.customerName','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
                'faults.accountManager','cities.city','suburbs.suburb','pops.pop','faults.suspectedRfo','links.linkName'
                ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.remarks','faults.created_at'])
                ->first();

               //dd($fault);
        return view('faults.show',compact('fault'));
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
            ->where('faults.id','=',$id)
            ->get(['faults.id','faults.customer_id','customers.customerName','faults.contactName','faults.phoneNumber','faults.contactEmail','faults.address',
            'faults.accountManager','faults.city_id','cities.city','faults.suburb_id','suburbs.suburb','faults.pop_id','pops.pop','faults.suspectedRfo','faults.link_id','links.linkName'
            ,'faults.serviceType','faults.serviceAttribute','faults.faultType','faults.priorityLevel','faults.remarks','faults.created_at'])
            ->first();

            $cities = City::all();
            $customers = Customer::all();
            $suburbs = Suburb::all();
            $pops = Pop::all();
            $links = Link::all();
    
        return view('faults.edit',compact('fault','customers','cities','suburbs','pops','links'));

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
