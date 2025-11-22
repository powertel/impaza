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

        $newFaultsTotal = Fault::whereBetween('created_at', [$periodStart, $periodEnd])->count();
        $clearedStatusId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);
        $latestClearedInPeriod = DB::table('fault_stage_logs')
            ->where('status_id', $clearedStatusId)
            ->whereBetween('started_at', [$periodStart, $periodEnd])
            ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
            ->groupBy('fault_id')
            ->get();
        $resolvedTotal = $latestClearedInPeriod->count();

        $weeklyLabels = [];
        $weeklyRanges = [];
        $cursor = $periodStart->copy()->startOfWeek(Carbon::MONDAY);
        $endBound = $periodEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $weekIndex = 1;
        while ($cursor->lte($endBound)) {
            $ws = $cursor->copy()->startOfWeek(Carbon::MONDAY);
            $we = $cursor->copy()->endOfWeek(Carbon::SUNDAY);
            $weeklyRanges[] = [$ws, $we];
            $weeklyLabels[] = 'Week ' . $weekIndex;
            $cursor->addWeek();
            $weekIndex++;
        }

        $weeklyNewFaults = [];
        $weeklyResolved = [];
        $weeklyOutstanding = [];
        $weeklyResolved3DaysPerc = [];
        foreach ($weeklyRanges as [$ws,$we]) {
            $weeklyNewFaults[] = Fault::whereBetween('created_at', [$ws,$we])->count();
            $latestInWeek = DB::table('fault_stage_logs')
                ->where('status_id', $clearedStatusId)
                ->whereBetween('started_at', [$ws,$we])
                ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
                ->groupBy('fault_id')
                ->get();
            $weeklyResolved[] = $latestInWeek->count();
            $resolvedUpToDateIds = DB::table('fault_stage_logs')
                ->where('status_id', $clearedStatusId)
                ->where('started_at','<=',$we)
                ->select('fault_id', DB::raw('MAX(started_at) as ra'))
                ->groupBy('fault_id')
                ->pluck('fault_id')
                ->unique()
                ->values();
            $weeklyOutstanding[] = Fault::whereBetween('created_at', [$periodStart, $we])->whereNotIn('id', $resolvedUpToDateIds)->count();

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

        $resolvedUpToEndIds = DB::table('fault_stage_logs')
            ->where('status_id', $clearedStatusId)
            ->where('started_at','<=',$periodEnd)
            ->select('fault_id', DB::raw('MAX(started_at) as ra'))
            ->groupBy('fault_id')
            ->pluck('fault_id')
            ->unique()
            ->values();
        $outstandingFaults = Fault::whereBetween('created_at', [$periodStart, $periodEnd])
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
            $mOpen = Carbon::parse($f->created_at)->diffInMinutes($periodEnd);
            if ($mOpen <= 4320) $outBins['0_3']++;
            elseif ($mOpen <= 10080) { $outBins['4_7']++; $over3DaysCount++; }
            elseif ($mOpen <= 20160) { $outBins['8_14']++; $over3DaysCount++; }
            elseif ($mOpen <= 43200) { $outBins['15_30']++; $over3DaysCount++; }
            elseif ($mOpen <= 86400) { $outBins['31_60']++; $over3DaysCount++; }
            elseif ($mOpen <= 129600) { $outBins['61_90']++; $over3DaysCount++; }
            else { $outBins['90_plus']++; $over3DaysCount++; }
        }
        $over3DaysPercent = $outstandingTotal > 0 ? round(($over3DaysCount / $outstandingTotal) * 100, 2) : 0;

        $faultsInRange = Fault::whereBetween('created_at', [$periodStart, $periodEnd])->select('id','created_at')->get();
        $assignmentsInRange = collect();
        $dailyLabels = [];
        $dailyNewFaults = [];
        $dailyResolved = [];
        $dailyOutstanding = [];
        $dailyResolved3DaysPerc = [];
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
                $dailyNewFaults[] = Fault::whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $latestInDay = DB::table('fault_stage_logs')->where('status_id',$clearedStatusId)->whereBetween('started_at', [$dayStart, $dayEnd])->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))->groupBy('fault_id')->get();
                $dailyResolved[] = $latestInDay->count();
                $resolvedIdsUpToDay = DB::table('fault_stage_logs')->where('status_id',$clearedStatusId)->where('started_at','<=',$dayEnd)->select('fault_id', DB::raw('MAX(started_at) as ra'))->groupBy('fault_id')->pluck('fault_id')->unique()->values();
                $dailyOutstanding[] = Fault::whereBetween('created_at', [$periodStart, $dayEnd])->whereNotIn('id', $resolvedIdsUpToDay)->count();
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
                $cur->addDay();
            }
        }

        return view('call_centre.reports', [
            'filter' => $filter,
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
        ]);
    }
}