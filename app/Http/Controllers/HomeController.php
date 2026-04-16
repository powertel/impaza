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
        $availableRegions = DB::table('cities')
            ->whereNotNull('region')
            ->select('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region')
            ->toArray();

        $selectedRegionInput = $request->input('region');
        $selectedRegion = ($selectedRegionInput !== null && $selectedRegionInput !== '' && strtolower((string)$selectedRegionInput) !== 'all') ? (string)$selectedRegionInput : null;
        // Available years from faults.created_at
        $availableYears = DB::table('faults')
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        // Determine selected year; treat empty or 'all' as All Years
        $hasYearParam = $request->has('year');
        $yearInput = $request->input('year');
        if ($hasYearParam && ($yearInput === null || $yearInput === '' || strtolower((string)$yearInput) === 'all')) {
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
        $selectedMonth = ($selectedMonthInput !== null && $selectedMonthInput !== '' && strtolower((string)$selectedMonthInput) !== 'all') ? (int)$selectedMonthInput : null;
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
        if ($selectedRegion !== null) {
            $faultsQuery->leftJoin('cities','faults.city_id','=','cities.id')
                        ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $faultsQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $faultCount = $faultsQuery->count();

        if ($selectedRegion !== null) {
            $customersQuery = DB::table('customers')
                ->leftJoin('links','links.customer_id','=','customers.id')
                ->leftJoin('cities','links.city_id','=','cities.id')
                ->where('cities.region','=',$selectedRegion)
                ->distinct();
            if ($fromDate && $toDate) {
                $customersQuery->whereBetween('links.created_at', [$fromDate, $toDate]);
            }
            $customerCount = $customersQuery->count('customers.id');
        } else {
            $customersQuery = DB::table('customers');
            if ($fromDate && $toDate) {
                $customersQuery->whereBetween('created_at', [$fromDate, $toDate]);
            }
            $customerCount = $customersQuery->count();
        }

        $linksQuery = DB::table('links');
        if ($selectedRegion !== null) {
            $linksQuery->leftJoin('cities','links.city_id','=','cities.id')
                      ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $linksQuery->whereBetween('links.created_at', [$fromDate, $toDate]);
        }
        $linkCount = $linksQuery->count();

        $recentQuery = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->leftJoin('links','faults.link_id','=','links.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id');
        if ($selectedRegion !== null) {
            $recentQuery->leftJoin('cities','faults.city_id','=','cities.id')
                        ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $recentQuery->whereBetween('faults.updated_at', [$fromDate, $toDate]);
        }
        $recentFaults = $recentQuery
            ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
            ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
			->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
            ->orderBy('faults.updated_at','desc')
            ->limit(10)
            ->get(['faults.fault_ref_number','faults.status_id','customers.customer','links.link','faults.updated_at',
                'statuses.status_code as status_code','statuses.description as status_description',
                'assigned_users.name as assignedTo',
                'reported_users.name as reportedBy',
                'assessed_users.name as assessedBy']);

        // Monthly counts for charts
        $monthlyLabels = [];
        $monthlyCounts = [];
        if ($selectedYear !== null) {
            foreach (range(1,12) as $m) {
                $monthlyLabels[] = Carbon::create(null, $m, 1)->format('M');
                $countQuery = DB::table('faults')
                    ->whereYear('faults.created_at', $selectedYear)
                    ->whereMonth('faults.created_at', $m);
                if ($selectedRegion !== null) {
                    $countQuery->leftJoin('cities','faults.city_id','=','cities.id')
                               ->where('cities.region','=',$selectedRegion);
                }
                $count = $countQuery->count();
                $monthlyCounts[] = (int)$count;
            }
        } else {
            // Last 12 months rolling
            $cursor = Carbon::now()->startOfMonth()->subMonths(11);
            for ($i = 0; $i < 12; $i++) {
                $label = $cursor->format('M');
                $next = (clone $cursor)->endOfMonth();
                $countQuery = DB::table('faults')
                    ->whereBetween('faults.created_at', [$cursor, $next]);
                if ($selectedRegion !== null) {
                    $countQuery->leftJoin('cities','faults.city_id','=','cities.id')
                               ->where('cities.region','=',$selectedRegion);
                }
                $count = $countQuery->count();
                $monthlyLabels[] = $label;
                $monthlyCounts[] = (int)$count;
                $cursor->addMonth();
            }
        }

        // Status distribution within selected period
        $statusQuery = DB::table('faults')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->select('statuses.description as name', DB::raw('COUNT(*) as c'))
            ->groupBy('faults.status_id','statuses.description');
        if ($selectedRegion !== null) {
            $statusQuery->leftJoin('cities','faults.city_id','=','cities.id')
                        ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $statusQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $statusRows = $statusQuery->orderBy('c','desc')->limit(6)->get();
        $statusLabels = $statusRows->pluck('name')->map(fn($n)=>$n ?? 'Unknown')->values()->toArray();
        $statusValues = $statusRows->pluck('c')->map(fn($x)=>(int)$x)->values()->toArray();

        // Top customers by fault count (for horizontal bars)
        $topCustQuery = DB::table('faults')
            ->leftJoin('customers','faults.customer_id','=','customers.id')
            ->select('customers.customer as name', DB::raw('COUNT(*) as c'))
            ->groupBy('faults.customer_id','customers.customer')
            ->orderBy('c','desc')
            ->limit(5);
        if ($selectedRegion !== null) {
            $topCustQuery->leftJoin('cities','faults.city_id','=','cities.id')
                         ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $topCustQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $topCustomers = $topCustQuery->get();
        $topCustomerLabels = $topCustomers->pluck('name')->map(fn($n)=>$n ?? 'Unknown')->values()->toArray();
        $topCustomerCounts = $topCustomers->pluck('c')->map(fn($x)=>(int)$x)->values()->toArray();

        // Dashboard metrics (permission-gated in view)
        $nocClearedId = (int)(DB::table('statuses')->where('status_code','CLN')->value('id') ?? 6);

        $openFaultsQuery = DB::table('faults')
            ->where('status_id','!=',$nocClearedId);
        if ($selectedRegion !== null) {
            $openFaultsQuery->leftJoin('cities','faults.city_id','=','cities.id')
                            ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $openFaultsQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $openFaultsCount = $openFaultsQuery->count();

        $inProgressFaultsQuery = DB::table('faults')
            ->where('status_id','!=',$nocClearedId);
        if ($selectedRegion !== null) {
            $inProgressFaultsQuery->leftJoin('cities','faults.city_id','=','cities.id')
                                  ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $inProgressFaultsQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $inProgressFaultsCount = $inProgressFaultsQuery->count();

        $resolvedFaultsQuery = DB::table('faults')
            ->where('status_id','=', $nocClearedId);
        if ($selectedRegion !== null) {
            $resolvedFaultsQuery->leftJoin('cities','faults.city_id','=','cities.id')
                                ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $resolvedFaultsQuery->whereBetween('faults.created_at', [$fromDate, $toDate]);
        }
        $resolvedFaultsCount = $resolvedFaultsQuery->count();

        $todayFaultsQuery = DB::table('faults');
        if ($selectedRegion !== null) {
            $todayFaultsQuery->leftJoin('cities','faults.city_id','=','cities.id')
                             ->where('cities.region','=',$selectedRegion);
        }
        $todayFaultsQuery->whereDate('faults.created_at', Carbon::today());
        $todayFaultsCount = $todayFaultsQuery->count();

        $openFaultCreatedAts = (clone $openFaultsQuery)->pluck('faults.created_at');

        $now = Carbon::now();
        $ageSeconds = collect($openFaultCreatedAts)->map(function($dt) use ($now){
            return Carbon::parse($dt)->diffInSeconds($now);
        });

        $avgOpenAgeSec = (int)floor(($ageSeconds->avg() ?? 0));
        $maxOpenAgeSec = (int)($ageSeconds->max() ?? 0);

        // Resolution metrics in selected period
        $avgResQuery = DB::table('fault_assignments')
            ->whereNotNull('resolved_at');
        if ($selectedRegion !== null) {
            $avgResQuery->leftJoin('faults','fault_assignments.fault_id','=','faults.id')
                        ->leftJoin('cities','faults.city_id','=','cities.id')
                        ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $avgResQuery->whereBetween('resolved_at', [$fromDate, $toDate]);
        }
        $avgResolutionSec = (int)floor($avgResQuery->avg('duration_seconds') ?? 0);

        $techAvgQuery = DB::table('fault_assignments')
            ->leftJoin('users','fault_assignments.user_id','=','users.id')
            ->whereNotNull('fault_assignments.resolved_at');
        if ($selectedRegion !== null) {
            $techAvgQuery->leftJoin('faults','fault_assignments.fault_id','=','faults.id')
                        ->leftJoin('cities','faults.city_id','=','cities.id')
                        ->where('cities.region','=',$selectedRegion);
        }
        if ($fromDate && $toDate) {
            $techAvgQuery->whereBetween('fault_assignments.resolved_at', [$fromDate, $toDate]);
        }
        $techResolutionAverages = $techAvgQuery
            ->groupBy('fault_assignments.user_id','users.name')
            ->select('fault_assignments.user_id','users.name', DB::raw('AVG(fault_assignments.duration_seconds) as avg_sec'), DB::raw('COUNT(*) as tickets'))
            ->orderBy('avg_sec','asc')
            ->limit(10)
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
            'openFaultsCount','inProgressFaultsCount','resolvedFaultsCount','todayFaultsCount','avgOpenAgeSec','maxOpenAgeSec','avgResolutionSec','techResolutionAverages',
            'availableYears','availableMonths','selectedYear','selectedMonth',
            'myAssignedCount','myResolvedCount','myAvgResolutionSec','myCompletionRate',
            'monthlyLabels','monthlyCounts','statusLabels','statusValues',
            'topCustomerLabels','topCustomerCounts',
            'availableRegions','selectedRegion'
        ));
    }
}
