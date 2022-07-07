<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Suburb;
use App\Models\Pop;
use DB;

class PopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pops = DB::table('pops')
            ->leftjoin('cities','pops.city_id','=','cities.id')
            ->leftjoin('suburbs','pops.suburb_id','=','suburbs.id')
            ->orderBy('suburbs.created_at', 'desc')
            ->get(['pops.id','pops.pop','suburbs.suburb','cities.city']);
        return view('pops.index',compact('pops'))
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
        $location = Suburb::all();
        $pop = Pop::all();
        return view('pops.create',compact('city','location'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        request()->validate([
            'city_id' => 'required',
            'suburb_id' => 'required',
            'pop' => 'required|string|unique:pops'
        ]);
        $pop = Pop::create($request->all());

        if($pop)
        {
            return redirect()->route('pops.index')
            ->with('success','Pop Created');
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
        $pop = DB::table('pops')
            ->leftjoin('cities','pops.city_id','=','cities.id')
            ->leftjoin('suburbs','pops.suburb_id','=','suburbs.id')
            ->where('pops.id','=',$id)
            ->get(['pops.id','pops.pop','suburbs.suburb','cities.city'])
            ->first();
        return view('pops.show',compact('pop'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pop = DB::table('pops')
            ->leftjoin('cities','pops.city_id','=','cities.id')
            ->leftjoin('suburbs','pops.suburb_id','=','suburbs.id')
            ->where('pops.id','=',$id)
            ->get(['pops.id','pops.pop','pops.city_id','pops.suburb_id','suburbs.suburb','cities.city'])
            ->first();
        $cities = City::all();
        $suburbs = Suburb::all();
        return view('pops.edit',compact('pop','cities','suburbs'));
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
            'city_id' => 'required',
            'suburb_id' => 'required',
            'pop' => 'required|string|unique:pops'
        ]);
        $pop = Pop::find($id);
        $pop ->update($request->all());

        if($pop)
        {
            return redirect(route('pops.index'))
            ->with('success','Pop Updated');
        }
        else
        {
            return back()->with('fail','Something went wrong');
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
}
