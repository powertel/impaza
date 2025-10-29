<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Suburb;
use App\Models\Pop;
use DB;

class PopController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:pop-list|pop-create|pop-edit|pop-delete', ['only' => ['index','store']]);
         $this->middleware('permission:pop-create', ['only' => ['create','store']]);
         $this->middleware('permission:pop-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:pop-delete', ['only' => ['destroy']]);
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

        $popsQuery = DB::table('pops')
            ->leftjoin('cities','pops.city_id','=','cities.id')
            ->leftjoin('suburbs','pops.suburb_id','=','suburbs.id')
            ->orderBy('suburbs.created_at', 'desc')
            ->select(['pops.id','pops.pop','pops.city_id','pops.suburb_id','suburbs.suburb','cities.city']);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $popsQuery->where(function($qq) use ($like){
                $qq->where('cities.city','like',$like)
                   ->orWhere('suburbs.suburb','like',$like)
                   ->orWhere('pops.pop','like',$like);
            });
        }

        $pops = $popsQuery->paginate($perPage)->withQueryString();
        // Provide datasets for modal-based create/edit on index
        $cities = City::all();
        $suburbs = Suburb::all();
        return view('pops.index',compact('pops','cities','suburbs'))
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
        // Support batch creation via repeater (single location + multiple POPs)
        if ($request->has('items')) {
            $validated = $request->validate([
                'city_id' => 'required|integer|exists:cities,id',
                'suburb_id' => 'required|integer|exists:suburbs,id',
                'items' => 'required|array|min:1',
                'items.*.pop' => 'required|string|distinct|unique:pops,pop',
            ]);
            DB::beginTransaction();
            try {
                foreach ($validated['items'] as $item) {
                    Pop::create([
                        'city_id' => $validated['city_id'],
                        'suburb_id' => $validated['suburb_id'],
                        'pop' => $item['pop'],
                    ]);
                }
                DB::commit();
                return redirect()->route('pops.index')
                ->with('success','POPs Created');
            } catch (\Exception $ex) {
                DB::rollBack();
                return back()->withErrors(['error' => $ex->getMessage()])->withInput();
            }
        } else {
            $request->validate([
                'city_id' => 'required',
                'suburb_id' => 'required',
                'pop' => 'required|string|unique:pops,pop'
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
