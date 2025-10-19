<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

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

        // Dashboard metrics (permission-gated in view)
        $nocClearedId = (int)(DB::table('statuses')->where('status_code','CLN')->value('id') ?? 6);

        $openFaultsCount = DB::table('faults')->where('status_id','!=',$nocClearedId)->count();

        $openFaultCreatedAts = DB::table('faults')
            ->where('status_id','!=',$nocClearedId)
            ->pluck('created_at');

        $now = Carbon::now();
        $ageSeconds = collect($openFaultCreatedAts)->map(function($dt) use ($now){
            return Carbon::parse($dt)->diffInSeconds($now);
        });

        $avgOpenAgeSec = (int)floor(($ageSeconds->avg() ?? 0));
        $maxOpenAgeSec = (int)($ageSeconds->max() ?? 0);

        $fromDate = Carbon::now()->subDays(30);
        $avgResolutionSec = (int)floor(DB::table('fault_assignments')
            ->whereNotNull('resolved_at')
            ->where('resolved_at','>=',$fromDate)
            ->avg('duration_seconds') ?? 0);

        $techResolutionAverages = DB::table('fault_assignments')
            ->leftJoin('users','fault_assignments.user_id','=','users.id')
            ->whereNotNull('fault_assignments.resolved_at')
            ->where('fault_assignments.resolved_at','>=',$fromDate)
            ->groupBy('fault_assignments.user_id','users.name')
            ->select('fault_assignments.user_id','users.name', DB::raw('AVG(fault_assignments.duration_seconds) as avg_sec'), DB::raw('COUNT(*) as tickets'))
            ->orderBy('avg_sec','asc')
            ->limit(5)
            ->get();
    
        return view('home', compact('faultCount','customerCount','linkCount','recentFaults','openFaultsCount','avgOpenAgeSec','maxOpenAgeSec','avgResolutionSec','techResolutionAverages'));
    }
}
