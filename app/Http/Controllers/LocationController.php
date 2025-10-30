<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\suburb;
use DB;

class LocationController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:location-list|location-create|location-edit|location-delete', ['only' => ['index','store']]);
         $this->middleware('permission:location-create', ['only' => ['create','store']]);
         $this->middleware('permission:location-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:location-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = (int) request('per_page', 20);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 20;
        $q = trim((string) request('q', ''));

        $locationsQuery = DB::table('suburbs')
            ->leftjoin('cities','suburbs.city_id','=','cities.id')
            ->orderBy('suburbs.created_at', 'desc')
            ->select(['suburbs.id','suburbs.suburb','suburbs.city_id','cities.city']);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $locationsQuery->where(function($qq) use ($like){
                $qq->where('cities.city','like',$like)
                   ->orWhere('suburbs.suburb','like',$like);
            });
        }

        $locations = $locationsQuery->paginate($perPage)->withQueryString();
        $cities = City::all();
        return view('locations.index',compact('locations','cities'))
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
        // Support batch creation via repeater (single city + multiple suburbs)
        if ($request->has('items')) {
            $validated = $request->validate([
                'city_id' => 'required|integer|exists:cities,id',
                'items' => 'required|array|min:1',
                'items.*.suburb' => 'required|string|distinct|unique:suburbs,suburb',
            ]);
            DB::beginTransaction();
            try {
                foreach ($validated['items'] as $item) {
                    Suburb::create([
                        'city_id' => $validated['city_id'],
                        'suburb' => $item['suburb'],
                    ]);
                }
                DB::commit();
                return redirect()->route('locations.index')
                ->with('success','Locations Created');
            } catch (\Exception $ex) {
                DB::rollBack();
                return back()->withErrors(['error' => $ex->getMessage()])->withInput();
            }
        } else {
            request()->validate([
                'city_id' => 'required',
                'suburb' => 'required|string|unique:suburbs'
            ]);
            $location = Suburb::create($request->all());
            
            if($location)
            {
                return redirect()->route('locations.index')
                ->with('success','Location Created');
            }
            else
            {
                return back()->with('fail','Something went wrong');
            }
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
        request()->validate([
            'city_id' => 'required',
            'suburb' => 'required|string|unique:suburbs'
        ]);

        $location = Suburb::find($id);
        $location ->update($request->all());

        if($location)
        {
            return redirect(route('locations.index'))
            ->with('success','Location Updated');
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
