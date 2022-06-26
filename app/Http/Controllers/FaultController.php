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
        $faults = Fault::latest()->paginate(10);
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
    public function show(Fault $fault)
    {
        return view('faults.show',compact('fault'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Fault $fault)
    {
        $city = City::all();
        $customer = Customer::all();
        $suburb = Suburb::all();
        $pop = Pop::all();
        $link = Link::all();
        return view('faults.edit',compact('fault','customer','city','suburb','pop','link'));
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
