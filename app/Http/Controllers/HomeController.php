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
        // Available years from faults.created_at
        $availableYears = DB::table('faults')
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        // Determine selected year; support explicit "All Years" when year param exists but empty
        $hasYearParam = $request->has('year');
        $yearInput = $request->input('year');
        if ($hasYearParam && ($yearInput === null || $yearInput === '')) {
            $selectedYear = null; // All Years
        } elseif ($hasYearParam) {
            $selectedYear = (int)$yearInput;
        } else {
            $selectedYear = (int)($availableYears[0] ?? Carbon::now()->year);
        }

        // Available months (scoped to selected year if present)
        $monthsQuery = DB::table('faults');
        if ($selectedYear !== null) {
            $monthsQuery->whereRaw('YEAR(created_at) = ?', [$selectedYear]);
        }
        $availableMonths = $monthsQuery
            ->selectRaw('MONTH(created_at) as m')
            ->distinct()
            ->orderBy('m')
            ->pluck('m')
            ->toArray();

        // Determine selected month (ignored if All Years)
        $selectedMonthInput = $request->input('month');
        $selectedMonth = ($selectedMonthInput !== null && $selectedMonthInput !== '') ? (int)$selectedMonthInput : null;
        if ($selectedYear === null) {
            $selectedMonth = null; // month selection disabled for All Years
        } elseif ($selectedMonth !== null && !in_array($selectedMonth, $availableMonths)) {
            $selectedMonth = null; // fallback if invalid
        }

        // Date range (nulls mean no restriction: All Years)
        if ($selectedYear !== null) {
            if ($selectedMonth) {
                $fromDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
                $toDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            } else {
                $fromDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $toDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
            }
        } else {
            $fromDate = null;
            $toDate = null;
        }

        // Base counts respecting selected period
        $faultsQuery = DB::table('faults');
        if ($fromDate && $toDate) {
            $faultsQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $faultCount = $faultsQuery->count();

        $customersQuery = DB::table('customers');
        if ($fromDate && $toDate) {
            $customersQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $customerCount = $customersQuery->count();

        $linksQuery = DB::table('links');
        if ($fromDate && $toDate) {
            $linksQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $linkCount = $linksQuery->count();

        $recentQuery = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('links','faults.link_id','=','links.id');
        if ($fromDate && $toDate) {
            $recentQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $recentFaults = $recentQuery
            ->orderBy('faults.created_at','desc')
            ->limit(20)
            ->get(['faults.id','customers.customer','links.link','faults.created_at']);

        // Dashboard metrics (permission-gated in view)
        $nocClearedId = (int)(DB::table('statuses')->where('status_code','CLN')->value('id') ?? 6);

        $openFaultsQuery = DB::table('faults')
            ->where('status_id','!=',$nocClearedId);
        if ($fromDate && $toDate) {
            $openFaultsQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $openFaultsCount = $openFaultsQuery->count();

        $openFaultCreatedAts = (clone $openFaultsQuery)->pluck('created_at');

        $now = Carbon::now();
        $ageSeconds = collect($openFaultCreatedAts)->map(function($dt) use ($now){
            return Carbon::parse($dt)->diffInSeconds($now);
        });

        $avgOpenAgeSec = (int)floor(($ageSeconds->avg() ?? 0));
        $maxOpenAgeSec = (int)($ageSeconds->max() ?? 0);

        // Resolution metrics in selected period
        $avgResQuery = DB::table('fault_assignments')
            ->whereNotNull('resolved_at');
        if ($fromDate && $toDate) {
            $avgResQuery->whereBetween('resolved_at', [$fromDate, $toDate]);
        }
        $avgResolutionSec = (int)floor($avgResQuery->avg('duration_seconds') ?? 0);

        $techAvgQuery = DB::table('fault_assignments')
            ->leftJoin('users','fault_assignments.user_id','=','users.id')
            ->whereNotNull('fault_assignments.resolved_at');
        if ($fromDate && $toDate) {
            $techAvgQuery->whereBetween('fault_assignments.resolved_at', [$fromDate, $toDate]);
        }
        $techResolutionAverages = $techAvgQuery
            ->groupBy('fault_assignments.user_id','users.name')
            ->select('fault_assignments.user_id','users.name', DB::raw('AVG(fault_assignments.duration_seconds) as avg_sec'), DB::raw('COUNT(*) as tickets'))
            ->orderBy('avg_sec','asc')
            ->limit(5)
            ->get();

        // My technician stats (for logged-in user)
        $userId = auth()->id();
        $myAssignedQuery = DB::table('fault_assignments')->where('user_id', $userId);
        if ($fromDate && $toDate) {
            $myAssignedQuery->whereBetween('assigned_at', [$fromDate, $toDate]);
        }
        $myAssignedCount = $myAssignedQuery->count();

        $myResolvedQuery = DB::table('fault_assignments')
            ->where('user_id', $userId)
            ->whereNotNull('resolved_at');
        if ($fromDate && $toDate) {
            $myResolvedQuery->whereBetween('resolved_at', [$fromDate, $toDate]);
        }
        $myResolvedCount = $myResolvedQuery->count();
        $myAvgResolutionSec = (int)floor($myResolvedQuery->avg('duration_seconds') ?? 0);
        $myCompletionRate = $myAssignedCount > 0 ? round(($myResolvedCount / $myAssignedCount) * 100, 1) : 0;
    
        return view('home', compact(
            'faultCount','customerCount','linkCount','recentFaults',
            'openFaultsCount','avgOpenAgeSec','maxOpenAgeSec','avgResolutionSec','techResolutionAverages',
            'availableYears','availableMonths','selectedYear','selectedMonth',
            'myAssignedCount','myResolvedCount','myAvgResolutionSec','myCompletionRate'
        ));
    }
}
