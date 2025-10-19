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
    public function index(Request $request)
    {
        // Available years and months from data (faults created_at)
        $availableYears = DB::table('faults')
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        $selectedYear = (int)($request->input('year') ?? ($availableYears[0] ?? Carbon::now()->year));

        $availableMonths = DB::table('faults')
            ->whereRaw('YEAR(created_at) = ?', [$selectedYear])
            ->selectRaw('MONTH(created_at) as m')
            ->distinct()
            ->orderBy('m')
            ->pluck('m')
            ->toArray();

        $selectedMonth = $request->filled('month') ? (int)$request->input('month') : null;
        if ($selectedMonth !== null && !in_array($selectedMonth, $availableMonths)) {
            $selectedMonth = null; // fallback to whole year if invalid
        }

        // Date range
        if ($selectedMonth) {
            $fromDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $toDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
        } else {
            $fromDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $toDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
        }

        // Base counts respecting selected period
        $faultCount = DB::table('faults')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        $customerCount = DB::table('customers')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        $linkCount = DB::table('links')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
    
        $recentFaults = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->whereBetween('faults.created_at', [$fromDate, $toDate])
            ->orderBy('faults.created_at','desc')
            ->limit(20)
            ->get(['faults.id','customers.customer','links.link','faults.created_at']);

        // Dashboard metrics (permission-gated in view)
        $nocClearedId = (int)(DB::table('statuses')->where('status_code','CLN')->value('id') ?? 6);

        $openFaultsCount = DB::table('faults')
            ->where('status_id','!=',$nocClearedId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        $openFaultCreatedAts = DB::table('faults')
            ->where('status_id','!=',$nocClearedId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->pluck('created_at');

        $now = Carbon::now();
        $ageSeconds = collect($openFaultCreatedAts)->map(function($dt) use ($now){
            return Carbon::parse($dt)->diffInSeconds($now);
        });

        $avgOpenAgeSec = (int)floor(($ageSeconds->avg() ?? 0));
        $maxOpenAgeSec = (int)($ageSeconds->max() ?? 0);

        // Resolution metrics in selected period
        $avgResolutionSec = (int)floor(DB::table('fault_assignments')
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$fromDate, $toDate])
            ->avg('duration_seconds') ?? 0);

        $techResolutionAverages = DB::table('fault_assignments')
            ->leftJoin('users','fault_assignments.user_id','=','users.id')
            ->whereNotNull('fault_assignments.resolved_at')
            ->whereBetween('fault_assignments.resolved_at', [$fromDate, $toDate])
            ->groupBy('fault_assignments.user_id','users.name')
            ->select('fault_assignments.user_id','users.name', DB::raw('AVG(fault_assignments.duration_seconds) as avg_sec'), DB::raw('COUNT(*) as tickets'))
            ->orderBy('avg_sec','asc')
            ->limit(5)
            ->get();
    
        return view('home', compact(
            'faultCount','customerCount','linkCount','recentFaults',
            'openFaultsCount','avgOpenAgeSec','maxOpenAgeSec','avgResolutionSec','techResolutionAverages',
            'availableYears','availableMonths','selectedYear','selectedMonth'
        ));
    }
}
