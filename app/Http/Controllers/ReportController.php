<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Fault;
use App\Models\Suburb;
use App\Models\City;
use App\Models\Pop;
use App\Models\Customer;
use App\Models\Link;
use App\Models\Remark;
use App\Models\AccountManager;
use App\Models\FaultSection;
use DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   $faults = Fault::all();
        $Resolved = DB::table('faults')
        ->where('faults.status_id','=',6)
        ->count();
        $ResolvedNOC = DB::table('faults')
        ->where('faults.status_id','=',6)
        ->where('section_id','=',1)
        ->count();
        $ResolvedHRE = DB::table('faults')
        ->where('faults.status_id','=',6)
        ->where('section_id','!=','1')
        ->count();
        $ResolvedBYO = DB::table('faults')
        ->where('faults.status_id','=',6)
        ->where('section_id','=',2)
        ->count();
        $global= DB::table('faults')->count();
        $NOC_count = DB::table('fault_section')
               ->where('section_id','=','1')->count();
        $HRE_count = DB::table('fault_section')
               ->where('section_id','!=','1')->count();
        $BYO_count = DB::table('fault_section')
               ->where('section_id','=','2')->count();
        
        
        return view('reports.faultresolution',compact('faults','global','NOC_count','HRE_count','BYO_count','Resolved','ResolvedNOC','ResolvedHRE','ResolvedBYO'));

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
