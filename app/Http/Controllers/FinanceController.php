<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use DB;

class FinanceController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:finance|finance-link-update', ['only' => ['index','store']]);
         $this->middleware('permission:finance-link-update', ['only' => ['edit','update']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finance_links = DB::table('links')
            ->leftjoin('customers','links.customer_id','=','customers.id')
            ->leftjoin('cities','links.city_id','=','cities.id')
            ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
            ->leftjoin('pops','links.pop_id','=','pops.id')
            ->leftjoin('link_statuses','links.link_status','=','link_statuses.id')
            ->orderBy('cities.city', 'asc')
            ->get(['links.id','links.link','links.contract_number','customers.customer','cities.city','pops.pop','suburbs.suburb','link_statuses.link_status']);
        return view('finance.index',compact('finance_links'))
        ->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
                ->leftjoin('cities','links.city_id','=','cities.id')
                ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
                ->leftjoin('pops','links.pop_id','=','pops.id')
                ->where('links.id','=',$id)
                ->get(['links.id','links.link','links.contract_number','customers.customer','cities.city','pops.pop','suburbs.suburb'])
                ->first();
        return view('finance.show',compact('link'));
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
        ->leftjoin('cities','links.city_id','=','cities.id')
        ->leftjoin('suburbs','links.suburb_id','=','suburbs.id')
        ->leftjoin('pops','links.pop_id','=','pops.id')
        ->where('links.id','=',$id)
        ->get(['links.id','links.link','links.contract_number','links.customer_id','links.city_id','links.pop_id','links.suburb_id','customers.customer','cities.city','pops.pop','suburbs.suburb'])
        ->first();
        $customers = Customer::all();
        $cities = City::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
return view('finance.edit',compact('link','customers','cities','suburbs','pops',));
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
            'contract_number'=> 'required',
        ]);

        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 2;
        $link ->update($req);
        return redirect(route('finance.index'))
        ->with('success','Link Connected');
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

    public function disconnect(Request $request, $id)
    {

        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 3;
        $link ->update($req);
        return redirect(route('finance.index'))
        ->with('success','Link Disconnected');
    }

    public function reconnect(Request $request, $id)
    {

        $link = Link::find($id);
        $req= $request->all();
        $req['link_status'] = 2;
        $link ->update($req);
        return redirect(route('finance.index'))
        ->with('success','Link Reconnnected');
    }



}
