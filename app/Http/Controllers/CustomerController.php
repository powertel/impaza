<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use DB;

class CustomerController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index','store']]);
         $this->middleware('permission:customer-create', ['only' => ['create','store']]);
         $this->middleware('permission:customer-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = DB::table('customers')
                ->orderBy('customers.customer', 'asc')
                ->get(['customers.id','customers.customer']);
        return view('customers.index',compact('customers'))
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
        $location = Suburb::all();
        $link = Link::all();
        $pop = Pop::all();
        return view('customers.create',compact('city','location','link','pop'));
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
            'customer' => 'required|string|unique:customers',
        ]);

        $customer=  Customer::create($request->all());

        if($customer)
        {
            return redirect()->route('customers.index')
             ->with('success','Customer Created.');
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
        // $customer = DB::table('customers')
        //         ->leftjoin('cities','customers.city_id','=','cities.id')
        //         ->leftjoin('suburbs','customers.suburb_id','=','suburbs.id')
        //         ->leftjoin('pops','customers.pop_id','=','pops.id')
        //         ->leftjoin('links','customers.id','=','links.customer_id')
        //         ->where('customers.id','=',$id)
        //         ->get(['customers.id','customers.customer','cities.city','links.link','pops.pop','suburbs.suburb'])
        //         ->first();
        // return view('customers.show',compact('customer'));
        $customer = DB::table('customers')
        ->where('customers.id','=',$id)
        ->get()
        ->first();
        return view('customers.show',compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = DB::table('customers')
                    ->where('customers.id','=',$id)
                    ->get(['customers.id','customers.customer'])
                    ->first();
        $cities = City::all();
        $suburbs = Suburb::all();
        $pops = Pop::all();
        return view('customers.edit',compact('customer','cities','suburbs','pops'));
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

        $customer = Customer::find($id);
        $customer ->update($request->all());
        return redirect(route('customers.index'))
        ->with('success','Customer updated successfully'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("customers")->where('id',$id)->delete();
        return redirect()->route('customers.index');
    }
}
