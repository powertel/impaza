<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use DB;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = DB::table('links')
            ->leftjoin('customers','links.customer_id','=','customers.id')
            ->leftjoin('cities','links.city_id','=','cities.id')
            ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
            ->leftjoin('pops','links.pop_id','=','pops.id')
            ->orderBy('links.created_at', 'desc')
            ->get(['links.id','links.link','customers.customer','cities.city','pops.pop','suburbs.suburb']);
        return view('links.index',compact('links'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer = Customer::all();
        $city = City::all();
        return view('links.create',compact('customer','city'));
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
            'pop_id' => 'required',
            'customer_id' => 'required',
            'link' => 'required|string|unique:links'
        ]);
        $link = Link::create($request->all());

        if($link)
        {
            return redirect()->route('links.index')
            ->with('success','Link Created');
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
        $link = DB::table('links')
                ->leftjoin('customers','links.customer_id','=','customers.id')
                ->leftjoin('cities','customers.city_id','=','cities.id')
                ->leftjoin('suburbs','customers.suburb_id','=','suburbs.id')
                ->leftjoin('pops','customers.pop_id','=','pops.id')
                ->where('links.id','=',$id)
                ->get(['links.id','links.link','customers.customer','cities.city','pops.pop','suburbs.suburb'])
                ->first();
        return view('links.show',compact('link'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $link = DB::table('links')
                ->leftjoin('customers','links.customer_id','=','customers.id')
                ->leftjoin('cities','customers.city_id','=','cities.id')
                ->leftjoin('suburbs','customers.suburb_id','=','suburbs.id')
                ->leftjoin('pops','customers.pop_id','=','pops.id')
                ->where('links.id','=',$id)
                ->get(['links.id','links.link','links.customer_id','links.city_id','links.pop_id','links.suburb_id','customers.customer','cities.city','pops.pop','suburbs.suburb'])
                ->first();
                $customers = Customer::all();
                $cities = City::all();
                $suburbs = Suburb::all();
                $pops = Pop::all();
        return view('links.edit',compact('link','customers','cities','suburbs','pops',));
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
        $link = Link::find($id);
        $link ->update($request->all());
        return redirect(route('links.index'))
        ->with('success','Link Updated');
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
