<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Fault;
use App\Models\FaultAssignment;

class CallCentreController extends Controller
{
    public function index(Request $request)
    {
        $filter = strtolower((string) $request->input('filter', 'month'));
        $selectedRegionRaw = trim((string) $request->input('region', ''));
        $selectedRegion = $selectedRegionRaw === '' ? null : $selectedRegionRaw;
        $availableRegions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region')->toArray();
        $availableYears = DB::table('faults')
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        $now = Carbon::now();
        $yearInput = $request->input('year', $now->year);
        $isAllYears = strtolower((string)$yearInput) === 'all';
        $selectedYear = $isAllYears ? null : (int) $yearInput;
        $selectedMonth = (int) ($request->input('month', $now->month));
        $quarter = (int) ($request->input('quarter', 1));

        $periodStart = $now->copy()->startOfMonth();
        $periodEnd = $now->copy()->endOfMonth();

        if ($filter === 'month') {
            $periodStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $periodEnd = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
        } elseif ($filter === 'year') {
            if ($isAllYears && !empty($availableYears)) {
                $minYear = min($availableYears);
                $maxYear = max($availableYears);
                $periodStart = Carbon::create($minYear, 1, 1)->startOfYear();
                $periodEnd = Carbon::create($maxYear, 12, 31)->endOfYear();
            } else {
                $periodStart = Carbon::create($selectedYear ?? $now->year, 1, 1)->startOfYear();
                $periodEnd = Carbon::create($selectedYear ?? $now->year, 12, 31)->endOfYear();
            }
        } elseif ($filter === 'weekly') {
            $start = $request->input('start_date');
            $end = $request->input('end_date');
            if ($start && !$end) {
                $periodStart = Carbon::parse($start)->startOfWeek(Carbon::MONDAY);
                $periodEnd = Carbon::parse($start)->endOfWeek(Carbon::SUNDAY);
            } elseif (!$start && $end) {
                $periodStart = Carbon::parse($end)->startOfWeek(Carbon::MONDAY);
                $periodEnd = Carbon::parse($end)->endOfWeek(Carbon::SUNDAY);
            } elseif ($start && $end) {
                $periodStart = Carbon::parse($start)->startOfDay();
                $periodEnd = Carbon::parse($end)->endOfDay();
            } else {
                $periodStart = $now->copy()->startOfWeek(Carbon::MONDAY);
                $periodEnd = $now->copy()->endOfWeek(Carbon::SUNDAY);
            }
        } elseif ($filter === 'quarter') {
            $q = max(1, min(4, $quarter));
            $qStartMonth = [1 => 1, 2 => 4, 3 => 7, 4 => 10][$q];
            $periodStart = Carbon::create($selectedYear, $qStartMonth, 1)->startOfMonth();
            $periodEnd = Carbon::create($selectedYear, $qStartMonth + 2, 1)->endOfMonth();
        }

        $periodLabelText = 'Period total';
        if ($filter === 'month') $periodLabelText = 'Month total';
        elseif ($filter === 'year') $periodLabelText = $isAllYears ? 'All years total' : 'Year total';
        elseif ($filter === 'quarter') $periodLabelText = 'Quarter total';
        elseif ($filter === 'weekly') $periodLabelText = 'Week total';

        $faultIdsRegion = null;
        if ($selectedRegion) {
            $faultIdsRegion = DB::table('faults')
                ->join('cities', 'faults.city_id', '=', 'cities.id')
                ->where('cities.region', '=', $selectedRegion)
                ->pluck('faults.id')
                ->unique()
                ->values()
                ->toArray();
        }

        $newFaultsTotal = Fault::whereBetween('created_at', [$periodStart, $periodEnd])
            ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
            ->count();
        $clearedStatusId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
        $latestClearedInPeriod = DB::table('fault_stage_logs')
            ->where('status_id', $clearedStatusId)
            ->whereBetween('started_at', [$periodStart, $periodEnd])
            ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
            ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
            ->groupBy('fault_id')
            ->get();
        $resolvedTotal = $latestClearedInPeriod->count();

        $weeklyLabels = [];
        $weeklyRanges = [];
        $weeklyRangeStarts = [];
        $weeklyRangeEnds = [];
        $cursor = $periodStart->copy()->startOfWeek(Carbon::MONDAY);
        $endBound = $periodEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $weekIndex = 1;
        while ($cursor->lte($endBound)) {
            $ws = $cursor->copy()->startOfWeek(Carbon::MONDAY);
            $we = $cursor->copy()->endOfWeek(Carbon::SUNDAY);
            $weeklyRanges[] = [$ws, $we];
            $weeklyRangeStarts[] = $ws->copy()->format('Y-m-d');
            $weeklyRangeEnds[] = $we->copy()->format('Y-m-d');
            $weeklyLabels[] = 'Week ' . $weekIndex;
            $cursor->addWeek();
            $weekIndex++;
        }

        $weeklyNewFaults = [];
        $weeklyResolved = [];
        $weeklyOutstanding = [];
        $weeklyResolved3DaysPerc = [];
        $weeklyOpening = [];
        $weeklyTotals = [];
        $weeklyShiftMorning = [];
        $weeklyShiftAfternoon = [];
        $weeklyShiftNight = [];
        $todayEnd = Carbon::now()->endOfDay();
        foreach ($weeklyRanges as [$ws,$we]) {
            if ($ws->gt($todayEnd)) {
                $weeklyNewFaults[] = 0;
                $weeklyResolved[] = 0;
                $weeklyOutstanding[] = 0;
                $weeklyResolved3DaysPerc[] = 0;
                $weeklyOpening[] = 0;
                $weeklyTotals[] = 0;
                $weeklyShiftMorning[] = 0;
                $weeklyShiftAfternoon[] = 0;
                $weeklyShiftNight[] = 0;
                continue;
            }
            $weEff = $we->gt($todayEnd) ? $todayEnd->copy() : $we->copy();
            $resolvedUpToStartIds = DB::table('fault_stage_logs')
                ->where('status_id', $clearedStatusId)
                ->where('started_at','<=',$ws)
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                ->select('fault_id', DB::raw('MAX(started_at) as ra'))
                ->groupBy('fault_id')
                ->pluck('fault_id')
                ->unique()
                ->values();
            $openingCount = Fault::where('created_at','<',$ws)
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                ->whereNotIn('id', $resolvedUpToStartIds)
                ->count();
            $weeklyNewFaults[] = Fault::whereBetween('created_at', [$ws,$weEff])
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                ->count();
            $latestInWeek = DB::table('fault_stage_logs')
                ->where('status_id', $clearedStatusId)
                ->whereBetween('started_at', [$ws,$weEff])
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
                ->groupBy('fault_id')
                ->get();
            $weeklyResolved[] = $latestInWeek->count();
            $resolvedUpToDateIds = DB::table('fault_stage_logs')
                ->where('status_id', $clearedStatusId)
                ->where('started_at','<=',$weEff)
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                ->select('fault_id', DB::raw('MAX(started_at) as ra'))
                ->groupBy('fault_id')
                ->pluck('fault_id')
                ->unique()
                ->values();
            $weeklyOutstanding[] = Fault::where('created_at','<=',$weEff)
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                ->whereNotIn('id', $resolvedUpToDateIds)
                ->count();
            $weeklyOpening[] = $openingCount;
            $weeklyTotals[] = $openingCount + end($weeklyNewFaults);

            $ids = $latestInWeek->pluck('fault_id')->unique()->values();
            $createdMap = Fault::whereIn('id', $ids)->pluck('created_at','id');
            $tot = $latestInWeek->count();
            $w3 = 0;
            foreach ($latestInWeek as $row) {
                $created = $createdMap[$row->fault_id] ?? null;
                if (!$created) continue;
                $mins = Carbon::parse($created)->diffInMinutes(Carbon::parse($row->resolved_at));
                if ($mins <= 4320) $w3++;
            }
            $weeklyResolved3DaysPerc[] = $tot > 0 ? round(($w3 / $tot) * 100, 2) : 0;

            $morningCount = Fault::whereBetween('created_at', [$ws,$weEff])
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                ->whereTime('created_at', '>=', '06:00')
                ->whereTime('created_at', '<=', '13:59')
                ->count();
            $afternoonCount = Fault::whereBetween('created_at', [$ws,$weEff])
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                ->whereTime('created_at', '>=', '14:00')
                ->whereTime('created_at', '<=', '21:59')
                ->count();
            $nightCount = Fault::whereBetween('created_at', [$ws,$weEff])
                ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                ->where(function($q){
                    $q->whereTime('created_at', '>=', '22:00')
                      ->orWhereTime('created_at', '<=', '05:59');
                })
                ->count();
            $weeklyShiftMorning[] = $morningCount;
            $weeklyShiftAfternoon[] = $afternoonCount;
            $weeklyShiftNight[] = $nightCount;
        }

        $latestMap = $latestClearedInPeriod->keyBy('fault_id');
        $faultsForResolved = Fault::whereIn('id', $latestClearedInPeriod->pluck('fault_id')->unique()->values())->get(['id','created_at']);
        $resolvedRows = collect();
        foreach ($faultsForResolved as $f) {
            $resolvedAt = $latestMap[$f->id]->resolved_at ?? null;
            if ($resolvedAt) { $resolvedRows->push((object)['created_at' => $f->created_at, 'resolved_at' => $resolvedAt]); }
        }
        $bins = [
            '0_3' => 0,
            '4_7' => 0,
            '8_14' => 0,
            '15_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0,
        ];
        foreach ($resolvedRows as $r) {
            $m = Carbon::parse($r->created_at)->diffInMinutes(Carbon::parse($r->resolved_at));
            if ($m <= 4320) $bins['0_3']++;
            elseif ($m <= 10080) $bins['4_7']++;
            elseif ($m <= 20160) $bins['8_14']++;
            elseif ($m <= 43200) $bins['15_30']++;
            elseif ($m <= 86400) $bins['31_60']++;
            elseif ($m <= 129600) $bins['61_90']++;
            else $bins['90_plus']++;
        }
        $w3Strict = 0;
        foreach ($resolvedRows as $r) {
            $mins = Carbon::parse($r->created_at)->diffInMinutes(Carbon::parse($r->resolved_at));
            if ($mins <= 4320) $w3Strict++;
        }
        $within3DaysPercent = $resolvedTotal > 0 ? round(($w3Strict / $resolvedTotal) * 100, 2) : 0;

        $effectiveEnd = $periodEnd->copy();
        $todayEnd = Carbon::now()->endOfDay();
        if ($effectiveEnd->gt($todayEnd)) { $effectiveEnd = $todayEnd; }

        $resolvedUpToEndIds = DB::table('fault_stage_logs')
            ->where('status_id', $clearedStatusId)
            ->where('started_at','<=',$effectiveEnd)
            ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
            ->select('fault_id', DB::raw('MAX(started_at) as ra'))
            ->groupBy('fault_id')
            ->pluck('fault_id')
            ->unique()
            ->values();
        $outstandingFaults = Fault::where('created_at','<=',$effectiveEnd)
            ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
            ->whereNotIn('id', $resolvedUpToEndIds)
            ->get(['id','created_at']);
        $outstandingTotal = $outstandingFaults->count();
        $outBins = [
            '0_3' => 0,
            '4_7' => 0,
            '8_14' => 0,
            '15_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0,
        ];
        $over3DaysCount = 0;
        foreach ($outstandingFaults as $f) {
            $mOpen = Carbon::parse($f->created_at)->diffInMinutes($effectiveEnd);
            if ($mOpen <= 4320) $outBins['0_3']++;
            elseif ($mOpen <= 10080) { $outBins['4_7']++; $over3DaysCount++; }
            elseif ($mOpen <= 20160) { $outBins['8_14']++; $over3DaysCount++; }
            elseif ($mOpen <= 43200) { $outBins['15_30']++; $over3DaysCount++; }
            elseif ($mOpen <= 86400) { $outBins['31_60']++; $over3DaysCount++; }
            elseif ($mOpen <= 129600) { $outBins['61_90']++; $over3DaysCount++; }
            else { $outBins['90_plus']++; $over3DaysCount++; }
        }
        $over3DaysPercent = $outstandingTotal > 0 ? round(($over3DaysCount / $outstandingTotal) * 100, 2) : 0;

        $faultsInRange = Fault::whereBetween('created_at', [$periodStart, $periodEnd])
            ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
            ->select('id','created_at')
            ->get();
        $assignmentsInRange = collect();
        $dailyLabels = [];
        $dailyNewFaults = [];
        $dailyResolved = [];
        $dailyOutstanding = [];
        $dailyResolved3DaysPerc = [];
        $dailyShiftMorning = [];
        $dailyShiftAfternoon = [];
        $dailyShiftNight = [];
        $dailyOpening = [];
        $dailyTotals = [];
        if ($filter === 'weekly') {
            $cur = $periodStart->copy();
            $endBound = $periodEnd->copy();
            $todayEnd = Carbon::now()->endOfDay();
            if ($endBound->gt($todayEnd)) { $endBound = $todayEnd; }
            while ($cur->lte($endBound)) {
                $ds = $cur->format('Y-m-d');
                $dayStart = $cur->copy()->startOfDay();
                $dayEnd = $cur->copy()->endOfDay();
                $dailyLabels[] = $ds;
                $dailyNewFaults[] = Fault::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->count();
                $resolvedIdsUpToStart = DB::table('fault_stage_logs')
                    ->where('status_id',$clearedStatusId)
                    ->where('started_at','<=',$dayStart)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                    ->select('fault_id', DB::raw('MAX(started_at) as ra'))
                    ->groupBy('fault_id')
                    ->pluck('fault_id')
                    ->unique()
                    ->values();
                $openingCountDay = Fault::where('created_at','<',$dayStart)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->whereNotIn('id', $resolvedIdsUpToStart)
                    ->count();
                $dailyOpening[] = $openingCountDay;
                $latestInDay = DB::table('fault_stage_logs')
                    ->where('status_id',$clearedStatusId)
                    ->whereBetween('started_at', [$dayStart, $dayEnd])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                    ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
                    ->groupBy('fault_id')
                    ->get();
                $dailyResolved[] = $latestInDay->count();
                $resolvedIdsUpToDay = DB::table('fault_stage_logs')
                    ->where('status_id',$clearedStatusId)
                    ->where('started_at','<=',$dayEnd)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                    ->select('fault_id', DB::raw('MAX(started_at) as ra'))
                    ->groupBy('fault_id')
                    ->pluck('fault_id')
                    ->unique()
                    ->values();
                $dailyOutstanding[] = Fault::where('created_at','<=',$dayEnd)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->whereNotIn('id', $resolvedIdsUpToDay)
                    ->count();
                $dailyTotals[] = $openingCountDay + end($dailyNewFaults);
                $idsDay = $latestInDay->pluck('fault_id')->unique()->values();
                $createdMapDay = Fault::whereIn('id', $idsDay)->pluck('created_at','id');
                $totDay = $latestInDay->count();
                $w3Day = 0;
                foreach ($latestInDay as $r) {
                    $createdAt = $createdMapDay[$r->fault_id] ?? null;
                    if (!$createdAt) continue;
                    $minsDiff = Carbon::parse($createdAt)->diffInMinutes(Carbon::parse($r->resolved_at));
                    if ($minsDiff <= 4320) $w3Day++;
                }
                $dailyResolved3DaysPerc[] = $totDay > 0 ? round(($w3Day / $totDay) * 100, 2) : 0;
                $morningDay = Fault::whereBetween('created_at', [$dayStart,$dayEnd])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->whereTime('created_at','>=','06:00')
                    ->whereTime('created_at','<=','13:59')
                    ->count();
                $afternoonDay = Fault::whereBetween('created_at', [$dayStart,$dayEnd])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->whereTime('created_at','>=','14:00')
                    ->whereTime('created_at','<=','21:59')
                    ->count();
                $nightDay = Fault::whereBetween('created_at', [$dayStart,$dayEnd])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->where(function($q){
                        $q->whereTime('created_at','>=','22:00')
                          ->orWhereTime('created_at','<=','05:59');
                    })
                    ->count();
                $dailyShiftMorning[] = $morningDay;
                $dailyShiftAfternoon[] = $afternoonDay;
                $dailyShiftNight[] = $nightDay;
                $cur->addDay();
            }
        }

        $monthlyLabels = [];
        $monthlyOpening = [];
        $monthlyNewFaults = [];
        $monthlyResolved = [];
        $monthlyOutstanding = [];
        $monthlyTotals = [];
        $monthlyResolved3DaysPerc = [];
        if ($filter === 'year') {
            $baseYear = $isAllYears ? ($selectedYear ?? $now->year) : ($selectedYear ?? $now->year);
            for ($m = 1; $m <= 12; $m++) {
                $ms = \Carbon\Carbon::create($baseYear, $m, 1)->startOfMonth();
                $me = \Carbon\Carbon::create($baseYear, $m, 1)->endOfMonth();
                $monthlyLabels[] = $ms->format('M');
                $resolvedIdsUpToStart = \DB::table('fault_stage_logs')
                    ->where('status_id', $clearedStatusId)
                    ->where('started_at', '<=', $ms)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                    ->select('fault_id', \DB::raw('MAX(started_at) as ra'))
                    ->groupBy('fault_id')
                    ->pluck('fault_id')
                    ->unique()
                    ->values();
                $openMonth = Fault::where('created_at','<',$ms)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->whereNotIn('id', $resolvedIdsUpToStart)
                    ->count();
                $newMonth = Fault::whereBetween('created_at', [$ms,$me])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->count();
                $latestClearedInMonth = \DB::table('fault_stage_logs')
                    ->where('status_id', $clearedStatusId)
                    ->whereBetween('started_at', [$ms,$me])
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                    ->select('fault_id', \DB::raw('MAX(started_at) as resolved_at'))
                    ->groupBy('fault_id')
                    ->get();
                $resolvedMonth = $latestClearedInMonth->count();
                $resolvedIdsUpToEnd = \DB::table('fault_stage_logs')
                    ->where('status_id', $clearedStatusId)
                    ->where('started_at', '<=', $me)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('fault_id', $faultIdsRegion); })
                    ->select('fault_id', \DB::raw('MAX(started_at) as ra'))
                    ->groupBy('fault_id')
                    ->pluck('fault_id')
                    ->unique()
                    ->values();
                $outMonth = Fault::where('created_at','<=',$me)
                    ->when(!empty($faultIdsRegion), function($q) use ($faultIdsRegion){ $q->whereIn('id', $faultIdsRegion); })
                    ->whereNotIn('id', $resolvedIdsUpToEnd)
                    ->count();
                $idsMonth = $latestClearedInMonth->pluck('fault_id')->unique()->values();
                $createdMapMonth = Fault::whereIn('id', $idsMonth)->pluck('created_at','id');
                $totMonth = $latestClearedInMonth->count();
                $w3Month = 0;
                foreach ($latestClearedInMonth as $r) {
                    $createdAt = $createdMapMonth[$r->fault_id] ?? null;
                    if (!$createdAt) continue;
                    $minsDiff = \Carbon\Carbon::parse($createdAt)->diffInMinutes(\Carbon\Carbon::parse($r->resolved_at));
                    if ($minsDiff <= 4320) $w3Month++;
                }
                $monthlyOpening[] = $openMonth;
                $monthlyNewFaults[] = $newMonth;
                $monthlyResolved[] = $resolvedMonth;
                $monthlyOutstanding[] = $outMonth;
                $monthlyTotals[] = $openMonth + $newMonth;
                $monthlyResolved3DaysPerc[] = $totMonth > 0 ? round(($w3Month / $totMonth) * 100, 2) : 0;
            }
        }

        return view('call_centre.reports', [
            'filter' => $filter,
            'availableRegions' => $availableRegions,
            'selectedRegion' => $selectedRegion,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'quarter' => $quarter,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'newFaultsTotal' => $newFaultsTotal,
            'resolvedTotal' => $resolvedTotal,
            'weeklyLabels' => $weeklyLabels,
            'weeklyNewFaults' => $weeklyNewFaults,
            'weeklyResolved' => $weeklyResolved,
            'weeklyOutstanding' => $weeklyOutstanding,
            'weeklyResolved3DaysPerc' => $weeklyResolved3DaysPerc,
            'resolvedBins' => $bins,
            'within3DaysPercent' => $within3DaysPercent,
            'outstandingTotal' => $outstandingTotal,
            'outstandingBins' => $outBins,
            'over3DaysCount' => $over3DaysCount,
                'over3DaysPercent' => $over3DaysPercent,
                'periodLabelText' => $periodLabelText,
                'dailyLabels' => $dailyLabels,
                'dailyNewFaults' => $dailyNewFaults,
                'dailyResolved' => $dailyResolved,
                'dailyOutstanding' => $dailyOutstanding,
                'dailyResolved3DaysPerc' => $dailyResolved3DaysPerc,
                'dailyShiftMorning' => $dailyShiftMorning,
                'dailyShiftAfternoon' => $dailyShiftAfternoon,
                'dailyShiftNight' => $dailyShiftNight,
                'dailyOpening' => $dailyOpening,
                'dailyTotals' => $dailyTotals,
                'weeklyShiftMorning' => $weeklyShiftMorning,
                'weeklyShiftAfternoon' => $weeklyShiftAfternoon,
                'weeklyShiftNight' => $weeklyShiftNight,
                'weeklyOpening' => $weeklyOpening,
                'weeklyTotals' => $weeklyTotals,
                'weeklyRangeStarts' => $weeklyRangeStarts,
                'weeklyRangeEnds' => $weeklyRangeEnds,
                'monthlyLabels' => $monthlyLabels,
                'monthlyOpening' => $monthlyOpening,
                'monthlyNewFaults' => $monthlyNewFaults,
                'monthlyResolved' => $monthlyResolved,
                'monthlyOutstanding' => $monthlyOutstanding,
                'monthlyTotals' => $monthlyTotals,
                'monthlyResolved3DaysPerc' => $monthlyResolved3DaysPerc,
        ]);
    }
}
