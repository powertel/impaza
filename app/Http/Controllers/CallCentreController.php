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
        $selectedYear = (int) ($request->input('year', $now->year));
        $selectedMonth = (int) ($request->input('month', $now->month));
        $quarter = (int) ($request->input('quarter', 1));

        $periodStart = $now->copy()->startOfMonth();
        $periodEnd = $now->copy()->endOfMonth();

        if ($filter === 'month') {
            $periodStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $periodEnd = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
        } elseif ($filter === 'year') {
            $periodStart = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $periodEnd = Carbon::create($selectedYear, 12, 31)->endOfYear();
        } elseif ($filter === 'weekly') {
            $start = $request->input('start_date');
            $end = $request->input('end_date');
            $periodStart = $start ? Carbon::parse($start)->startOfDay() : $now->copy()->startOfWeek();
            $periodEnd = $end ? Carbon::parse($end)->endOfDay() : $now->copy()->endOfWeek();
        } elseif ($filter === 'quarter') {
            $q = max(1, min(4, $quarter));
            $qStartMonth = [1 => 1, 2 => 4, 3 => 7, 4 => 10][$q];
            $periodStart = Carbon::create($selectedYear, $qStartMonth, 1)->startOfMonth();
            $periodEnd = Carbon::create($selectedYear, $qStartMonth + 2, 1)->endOfMonth();
        }

        $periodLabelText = 'Period total';
        if ($filter === 'month') $periodLabelText = 'Month total';
        elseif ($filter === 'year') $periodLabelText = 'Year total';
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

        $weeklyLabels = ['Week 1','Week 2','Week 3','Week 4'];
        $weeklyRanges = [];
        if ($filter === 'month') {
            $w1s = $periodStart->copy()->startOfDay();
            $w1e = $periodStart->copy()->day(7)->endOfDay();
            $w2s = $periodStart->copy()->day(8)->startOfDay();
            $w2e = $periodStart->copy()->day(14)->endOfDay();
            $w3s = $periodStart->copy()->day(15)->startOfDay();
            $w3e = $periodStart->copy()->day(21)->endOfDay();
            $w4s = $periodStart->copy()->day(22)->startOfDay();
            $w4e = $periodEnd->copy()->endOfDay();
            $weeklyRanges = [[$w1s,$w1e],[$w2s,$w2e],[$w3s,$w3e],[$w4s,$w4e]];
        } else {
            $len = $periodStart->diffInDays($periodEnd) + 1;
            $chunk = (int) ceil($len / 4);
            $s = $periodStart->copy()->startOfDay();
            for ($i=0; $i<4; $i++) { $e = $s->copy()->addDays($chunk - 1)->endOfDay(); if ($e->gt($periodEnd)) $e = $periodEnd->copy()->endOfDay(); $weeklyRanges[] = [$s->copy(), $e]; $s = $e->copy()->addDay()->startOfDay(); }
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
                $days = Carbon::parse($created)->diffInDays(Carbon::parse($row->resolved_at));
                if ($days <= 3) $w3++;
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
            $days = Carbon::parse($r->created_at)->diffInDays(Carbon::parse($r->resolved_at));
            if ($days <= 3) $bins['0_3']++;
            elseif ($days <= 7) $bins['4_7']++;
            elseif ($days <= 14) $bins['8_14']++;
            elseif ($days <= 30) $bins['15_30']++;
            elseif ($days <= 60) $bins['31_60']++;
            elseif ($days <= 90) $bins['61_90']++;
            else $bins['90_plus']++;
        }
        $within3DaysPercent = $resolvedTotal > 0 ? round(($bins['0_3'] / $resolvedTotal) * 100, 2) : 0;

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
            $days = Carbon::parse($f->created_at)->diffInDays($periodEnd);
            if ($days <= 3) $outBins['0_3']++;
            elseif ($days <= 7) { $outBins['4_7']++; $over3DaysCount++; }
            elseif ($days <= 14) { $outBins['8_14']++; $over3DaysCount++; }
            elseif ($days <= 30) { $outBins['15_30']++; $over3DaysCount++; }
            elseif ($days <= 60) { $outBins['31_60']++; $over3DaysCount++; }
            elseif ($days <= 90) { $outBins['61_90']++; $over3DaysCount++; }
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
                    $daysDiff = Carbon::parse($createdAt)->diffInDays(Carbon::parse($r->resolved_at));
                    if ($daysDiff <= 3) $w3Day++;
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