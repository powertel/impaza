<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Fault;

class WeeklyMetrics extends Command
{
    protected $signature = 'weekly:metrics {--start=} {--end=}';
    protected $description = 'Compute Call Centre metrics for the current week (daily granularity)';

    public function handle(): int
    {
        $now = Carbon::now();
        $startOpt = $this->option('start');
        $endOpt = $this->option('end');
        $periodStart = $startOpt ? Carbon::parse($startOpt)->startOfDay() : $now->copy()->startOfWeek();
        $periodEnd = $endOpt ? Carbon::parse($endOpt)->endOfDay() : $now->copy()->endOfWeek();
        $todayEnd = $now->copy()->endOfDay();
        if ($periodEnd->gt($todayEnd)) { $periodEnd = $todayEnd; }

        $clearedStatusId = (int) (DB::table('statuses')->where('status_code', 'CLN')->value('id') ?? 6);

        $dailyLabels = [];
        $dailyNewFaults = [];
        $dailyResolved = [];
        $dailyOutstanding = [];
        $dailyResolved3DaysPerc = [];

        $cur = $periodStart->copy();
        while ($cur->lte($periodEnd)) {
            $ds = $cur->format('Y-m-d');
            $dayStart = $cur->copy()->startOfDay();
            $dayEnd = $cur->copy()->endOfDay();
            $dailyLabels[] = $ds;

            $dailyNewFaults[] = Fault::whereBetween('created_at', [$dayStart, $dayEnd])->count();

            $latestInDay = DB::table('fault_stage_logs')
                ->where('status_id',$clearedStatusId)
                ->whereBetween('started_at', [$dayStart, $dayEnd])
                ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
                ->groupBy('fault_id')
                ->get();
            $dailyResolved[] = $latestInDay->count();

            $resolvedIdsUpToDay = DB::table('fault_stage_logs')
                ->where('status_id',$clearedStatusId)
                ->where('started_at','<=',$dayEnd)
                ->select('fault_id', DB::raw('MAX(started_at) as ra'))
                ->groupBy('fault_id')
                ->pluck('fault_id')
                ->unique()
                ->values();
            $dailyOutstanding[] = Fault::whereBetween('created_at', [$periodStart, $dayEnd])
                ->whereNotIn('id', $resolvedIdsUpToDay)
                ->count();

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

        $latestInWeek = DB::table('fault_stage_logs')
            ->where('status_id',$clearedStatusId)
            ->whereBetween('started_at', [$periodStart, $periodEnd])
            ->select('fault_id', DB::raw('MAX(started_at) as resolved_at'))
            ->groupBy('fault_id')
            ->get();
        $faultIdsWeek = $latestInWeek->pluck('fault_id')->unique()->values();
        $createdMapWeek = Fault::whereIn('id', $faultIdsWeek)->pluck('created_at','id');
        $binsResolved = ['0_3'=>0,'4_7'=>0,'8_14'=>0,'15_30'=>0,'31_60'=>0,'61_90'=>0,'90_plus'=>0];
        foreach ($latestInWeek as $r) {
            $c = $createdMapWeek[$r->fault_id] ?? null; if (!$c) continue;
            $d = Carbon::parse($c)->diffInDays(Carbon::parse($r->resolved_at));
            if ($d <= 3) $binsResolved['0_3']++; elseif ($d <= 7) $binsResolved['4_7']++; elseif ($d <= 14) $binsResolved['8_14']++; elseif ($d <= 30) $binsResolved['15_30']++; elseif ($d <= 60) $binsResolved['31_60']++; elseif ($d <= 90) $binsResolved['61_90']++; else $binsResolved['90_plus']++;
        }
        $resolvedUpToEndIdsWeek = DB::table('fault_stage_logs')->where('status_id',$clearedStatusId)->where('started_at','<=',$periodEnd)->select('fault_id', DB::raw('MAX(started_at) as ra'))->groupBy('fault_id')->pluck('fault_id')->unique()->values();
        $outstandingWeek = Fault::whereBetween('created_at', [$periodStart, $periodEnd])->whereNotIn('id', $resolvedUpToEndIdsWeek)->get(['id','created_at']);
        $binsOutstanding = ['0_3'=>0,'4_7'=>0,'8_14'=>0,'15_30'=>0,'31_60'=>0,'61_90'=>0,'90_plus'=>0];
        foreach ($outstandingWeek as $f) {
            $d = Carbon::parse($f->created_at)->diffInDays($periodEnd);
            if ($d <= 3) $binsOutstanding['0_3']++; elseif ($d <= 7) $binsOutstanding['4_7']++; elseif ($d <= 14) $binsOutstanding['8_14']++; elseif ($d <= 30) $binsOutstanding['15_30']++; elseif ($d <= 60) $binsOutstanding['31_60']++; elseif ($d <= 90) $binsOutstanding['61_90']++; else $binsOutstanding['90_plus']++;
        }

        $summary = [
            'period' => [
                'start' => $periodStart->format('c'),
                'end' => $periodEnd->format('c'),
            ],
            'labels' => $dailyLabels,
            'new' => $dailyNewFaults,
            'resolved' => $dailyResolved,
            'outstanding' => $dailyOutstanding,
            'resolvedWithin3DaysPct' => $dailyResolved3DaysPerc,
            'resolvedAgeBins' => $binsResolved,
            'outstandingAgeBins' => $binsOutstanding,
        ];

        $this->line(json_encode($summary));
        return Command::SUCCESS;
    }
}