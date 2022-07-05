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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = DB::table('customers')
                ->orderBy('customers.created_at', 'desc')
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
        return view('customers.create',compact('city'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       // dd($request->all());
        DB::beginTransaction();
        try{
            $customer=  Customer::create(
                [
                    'customer' => $request['customer'],
                    'city_id' => $request['city_id'],
                    'suburb_id' =>$request['suburb_id'],
                    'pop_id' => $request['pop_id'],
                ]
            );
            $link= Link::create(
                [
                    'customer_id' => $customer->id,
                    'city_id' => $request['city_id'],
                    'suburb_id' =>$request['suburb_id'],
                    'pop_id' => $request['pop_id'],
                    'link' =>$request['link'],
                ]
            );
            
            if($customer&&$link)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect()->route('customers.index')
             ->with('success','Customer Created.');
        }

        catch(Exception $ex)
        {
            DB::rollback();
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
        $customer = DB::table('customers')
                ->leftjoin('cities','customers.city_id','=','cities.id')
                ->leftjoin('suburbs','customers.suburb_id','=','suburbs.id')
                ->leftjoin('pops','customers.pop_id','=','pops.id')
                ->leftjoin('links','customers.id','=','links.customer_id')
                ->where('customers.id','=',$id)
                ->get(['customers.id','customers.customer','cities.city','links.link','pops.pop','suburbs.suburb'])
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
                    ->leftjoin('cities','customers.city_id','=','cities.id')
                    ->leftjoin('suburbs','customers.suburb_id','=','suburbs.id')
                    ->leftjoin('pops','customers.pop_id','=','pops.id')
                    ->leftjoin('links','customers.id','=','links.customer_id')
                    ->where('customers.id','=',$id)
                    ->get(['customers.id','customers.customer','customers.city_id','customers.suburb_id','customers.pop_id','cities.city','links.link','pops.pop','suburbs.suburb'])
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
        DB::beginTransaction();
        try{
            $customer = Customer::find($id);
            $link = Link::find($id);
            $customer ->update(
                [
                    'customer' => $request['customer'],
                    'city_id' => $request['city_id'],
                    'suburb_id' =>$request['suburb_id'],
                    'pop_id' => $request['pop_id'],
                ]
            );
            $link ->update(
                [
                    'customer_id' => $customer->id,
                    'city_id' => $request['city_id'],
                    'suburb_id' =>$request['suburb_id'],
                    'pop_id' => $request['pop_id'],
                    'link' =>$request['link'],
                ]
            );
            if($customer&&$link)
            {
                DB::commit();
            }
            else
            {
                DB::rollback();
            }
            return redirect(route('customers.index'))
            ->with('success','Customer Updated');
        }
        catch(Exception $ex)
        {
            DB::rollback();
        }


/*         $customer = Customer::find($id);
        $customer ->update($request->all());
        return redirect(route('customers.index'))
        ->with('success','Customer updated successfully'); */
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
