<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\City;
use DB;

class CityController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:city-list|city-create|city-edit|city-delete', ['only' => ['index','store']]);
         $this->middleware('permission:city-create', ['only' => ['create','store']]);
         $this->middleware('permission:city-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:city-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = DB::table('cities')
                ->orderBy('cities.city', 'asc')
                ->get();
        return view('cities.index',compact('cities'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Support bulk creation via items[] or single via city
        if ($request->has('items')) {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.city' => 'required|string|distinct|unique:cities,city',
            ]);

            DB::beginTransaction();
            try {
                foreach ($request->input('items') as $item) {
                    City::create(['city' => $item['city']]);
                }
                DB::commit();
                return redirect()->route('cities.index')
                    ->with('success', 'City/Town(s) Created.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('fail', 'Something went wrong');
            }
        }

        // Single create fallback
        $request->validate([
            'city' => 'required|string|unique:cities,city'
        ]);

        $city = City::create($request->only('city'));

        if($city)
        {
            return redirect()->route('cities.index')
            ->with('success','City Created.');
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
        $city = DB::table('cities')
                ->where('cities.id','=',$id)
                ->get()
                ->first();
        return view('cities.show',compact('city'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = DB::table('cities')
                ->where('cities.id','=',$id)
                ->get()
                ->first();
        return view('cities.edit',compact('city'));
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
            'city' => 'required|string|unique:cities'
        ]);
        
        $city = City::find($id);
        $city ->update($request->all());

        if($city)
        {
            return redirect(route('cities.index'))
            ->with('success','City Updated');
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
        City::find($id)->delete();
        return redirect()->route('cities.index')
                        ->with('success','City deleted successfully');
    }
}
