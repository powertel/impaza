<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Fault;
use DB;
use Carbon\Carbon;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
      
        $TD_global= 0;
        $TD_NOC=0;
        $TD_HRE=0;
        $TD_BYO=0;
        $Resolved= DB::table('faults')->where('faults.status_id','=',6)->get();
        $ResolvedNOC= DB::table('faults')
        ->where('faults.status_id','=',6)
        ->where('section_id','=',1)->get();
        $ResolvedHRE= DB::table('faults')
        ->where('faults.status_id','=',6)
        ->where('section_id','!=',1)->where('section_id','!=',2)->get();
        $ResolvedBYO= DB::table('faults')
        ->where('faults.status_id','=',6)
        ->where('section_id','=',2)->get();
        $Resolved_count =  $Resolved->count();
        $ResolvedNOC_count = $ResolvedNOC->count();
        $ResolvedHRE_count = $ResolvedHRE->count();
        $ResolvedBYO_count = $ResolvedBYO->count();
        $global_count= DB::table('faults')->count();
        $NOC_count =  DB::table('fault_section')->where('section_id','=','1')->count();
        $HRE_count = DB::table('fault_section')->where('section_id','!=','1')->where('section_id','!=',2)->count();
        $BYO_count =DB::table('fault_section')->where('section_id','=','2')->count();
      
            foreach($Resolved as $RS){
             $log=  Carbon::parse($RS->created_at);
             $clear= Carbon::parse($RS->updated_at);
             $TD =   $log->diffInMinutes($clear); 
             $TD_global +=  $TD;
           }
      
            foreach($ResolvedNOC as $RS){
             $log=  Carbon::parse($RS->created_at);
             $clear= Carbon::parse($RS->updated_at);
             $TD =   $log->diffInMinutes($clear); 
             $TD_NOC +=  $TD;
           }
             
  
  
         foreach($ResolvedHRE as $RS){
          $log=  Carbon::parse($RS->created_at);
          $clear= Carbon::parse($RS->updated_at);
          $TD =   $log->diffInMinutes($clear); 
          $TD_HRE +=  $TD;
           }
     
        foreach($ResolvedBYO as $RS){
         $log=  Carbon::parse($RS->created_at);
         $clear= Carbon::parse($RS->updated_at);
         $TD =   $log->diffInMinutes($clear); 
         $TD_BYO +=  $TD;
           }
             
        
        return view('reports.faultresolution',compact('TD_NOC','TD_HRE','TD_BYO','TD_global','global_count','NOC_count','HRE_count','BYO_count','Resolved_count','ResolvedNOC_count','ResolvedHRE_count','ResolvedBYO_count'));

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
