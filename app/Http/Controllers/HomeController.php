<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $faultCount = DB::table('faults')->count();
        $customerCount = DB::table('customers')->count();
        $linkCount = DB::table('links')->count();
    
        $recentFaults = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->orderBy('faults.created_at','desc')
            ->limit(10)
            ->get(['faults.id','customers.customer','links.link','faults.created_at']);
    
        return view('home', compact('faultCount','customerCount','linkCount','recentFaults'));
    }
}
