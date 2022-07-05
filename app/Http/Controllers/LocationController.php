<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\suburb;
use DB;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = DB::table('suburbs')
            ->leftjoin('cities','suburbs.city_id','=','cities.id')
            ->orderBy('suburbs.created_at', 'desc')
            ->get(['suburbs.id','suburbs.suburb','cities.city']);
        return view('locations.index',compact('locations'))
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
        return view('locations.create',compact('city'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Suburb::create($request->all());
    
        return redirect()->route('locations.index')
        ->with('success','Location Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = DB::table('suburbs')
            ->leftjoin('cities','suburbs.city_id','=','cities.id')
            ->where('suburbs.id','=',$id)
            ->get(['suburbs.id','suburbs.suburb','cities.city'])
            ->first();
        return view('locations.show',compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $location = DB::table('suburbs')
            ->leftjoin('cities','suburbs.city_id','=','cities.id')
            ->where('suburbs.id','=',$id)
            ->get(['suburbs.id','suburbs.city_id','suburbs.suburb','cities.city'])
            ->first();
        $cities = City::all();
        return view('locations.edit',compact('location','cities'));
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
        $location = Suburb::find($id);
        $location ->update($request->all());
        return redirect(route('locations.index'))
        ->with('success','Location Updated');
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
