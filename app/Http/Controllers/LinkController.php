<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            ->orderBy('links.created_at', 'desc')
            ->get(['links.id','links.linkName','customers.customerName']);
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
        return view('links.create',compact('customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Link::create($request->all());
    
        return redirect()->route('links.index');
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
            ->where('links.id','=',$id)
            ->get(['links.id','links.linkName','links.customer_id','customers.customerName'])
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
                ->where('links.id','=',$id)
                ->get(['links.id','links.linkName','links.customer_id','customers.customerName'])
                ->first();
                $customers = Customer::all();
        return view('links.edit',compact('link','customers'));
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
        ->with('success','Product updated successfully');
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
