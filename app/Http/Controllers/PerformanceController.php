<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Technician / Section / Department performance.
 *
 * Definitions (applied to faults LOGGED in the selected period):
 *  - Resolved   = status Rectified (CLT), Cleared by CT (CLC) or Restored (CLN)
 *  - Pending    = everything else
 *  - Resolution time = logged -> first time the fault reached one of the resolved statuses
 *                      (from fault_stage_logs; falls back to last update if no log exists)
 *  - Within SLA = resolved within 24 hours (same threshold as Operations Analytics)
 *  - Overdue    = still pending and logged more than 72 hours ago
 */
class PerformanceController extends Controller
{
    private const SLA_SECONDS = 24 * 3600;
    private const OVERDUE_HOURS = 72;

    public function index(Request $request)
    {
        $now = Carbon::now();
        [$start, $end, $filter, $selectedYear, $selectedMonth, $selectedQuarter, $periodLabel] = $this->resolvePeriod($request, $now);

        $selectedRegionRaw = trim((string) $request->input('region', ''));
        $selectedRegion = $selectedRegionRaw === '' ? null : $selectedRegionRaw;
        $availableRegions = DB::table('cities')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region')->toArray();

        $availableYears = DB::table('faults')->selectRaw('YEAR(created_at) as y')->distinct()->orderByDesc('y')->pluck('y')->toArray();
        if (empty($availableYears)) $availableYears = [$now->year];

        $resolvedIds = DB::table('statuses')->whereIn('status_code', ['CLT', 'CLC', 'CLN'])->pluck('id')->map(fn ($v) => (int) $v)->all();
        if (!$resolvedIds) $resolvedIds = [4, 5, 6];

        $facts = $this->factsQuery($start, $end, $selectedRegion, $resolvedIds, $now);

        // ---- per technician
        $users = $this->aggregate(
            DB::query()->fromSub($facts, 'x')
                ->join('users', 'users.id', '=', 'x.assignedTo')
                ->selectRaw('users.id as id, users.name as name')
                ->groupBy('users.id', 'users.name')
        );

        // ---- per section (distinct fault/section pairs)
        $faultSections = DB::table('fault_section')->whereNotNull('section_id')->select('fault_id', 'section_id')->distinct();
        $sections = $this->aggregate(
            DB::query()->fromSub($facts, 'x')
                ->joinSub($faultSections, 'fs', 'fs.fault_id', '=', 'x.id')
                ->join('sections', 'sections.id', '=', 'fs.section_id')
                ->leftJoin('departments', 'departments.id', '=', 'sections.department_id')
                ->selectRaw('sections.id as id, sections.section as name, departments.department as parent')
                ->groupBy('sections.id', 'sections.section', 'departments.department')
        );

        // ---- per department (a fault counts once per department even if it passed through several sections)
        $faultDepts = DB::table('fault_section')
            ->join('sections', 'sections.id', '=', 'fault_section.section_id')
            ->whereNotNull('sections.department_id')
            ->select('fault_section.fault_id', 'sections.department_id')->distinct();
        $departments = $this->aggregate(
            DB::query()->fromSub($facts, 'x')
                ->joinSub($faultDepts, 'fd', 'fd.fault_id', '=', 'x.id')
                ->join('departments', 'departments.id', '=', 'fd.department_id')
                ->selectRaw('departments.id as id, departments.department as name')
                ->groupBy('departments.id', 'departments.department')
        );

        // ---- overall
        $overall = $this->aggregate(DB::query()->fromSub($facts, 'x')->selectRaw('1 as id, "All" as name'))->first();
        $assignedOverall = $this->aggregate(DB::query()->fromSub($facts, 'x')->whereNotNull('x.assignedTo')->selectRaw('1 as id, "Assigned" as name'))->first();

        // Top performer: most faults resolved; ties -> higher rate, then faster.
        $rank = fn ($c) => $c->sortBy([['resolved', 'desc'], ['resolution_rate', 'desc'], ['avg_seconds', 'asc']])->values();
        $users = $rank($users);
        $sections = $rank($sections);
        $departments = $rank($departments);
        $topUser = $users->first(fn ($u) => $u->resolved > 0);
        $topSection = $sections->first(fn ($s) => $s->resolved > 0);
        $fastestUser = $users->filter(fn ($u) => $u->resolved >= 3)->sortBy('avg_seconds')->first();
        $attentionUser = $users->filter(fn ($u) => $u->overdue > 0)->sortByDesc('overdue')->first();

        // ---- trend (logged vs resolved), weekly for short periods, monthly otherwise
        $trend = $this->trend($start, $end, $selectedRegion, $resolvedIds);

        $chartData = [
            'users' => $users->map(fn ($r) => $this->chartRow($r))->values(),
            'sections' => $sections->map(fn ($r) => $this->chartRow($r))->values(),
            'departments' => $departments->map(fn ($r) => $this->chartRow($r))->values(),
            'trend' => $trend,
        ];

        $drillContext = [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'period_label' => $periodLabel,
            'region' => $selectedRegion,
            'impact' => 'all',
            'customer_id' => null,
        ];

        return view('performance.index', [
            'users' => $users,
            'sections' => $sections,
            'departments' => $departments,
            'overall' => $overall,
            'assignedOverall' => $assignedOverall,
            'topUser' => $topUser,
            'topSection' => $topSection,
            'fastestUser' => $fastestUser,
            'attentionUser' => $attentionUser,
            'chartData' => $chartData,
            'drillContext' => $drillContext,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'periodLabel' => $periodLabel,
            'filter' => $filter,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedQuarter' => $selectedQuarter,
            'availableYears' => $availableYears,
            'availableRegions' => $availableRegions,
            'selectedRegion' => $selectedRegion,
            'slaHours' => self::SLA_SECONDS / 3600,
            'overdueHours' => self::OVERDUE_HOURS,
        ]);
    }

    /** @return array{0:Carbon,1:Carbon,2:string,3:int|string,4:int,5:int,6:string} */
    private function resolvePeriod(Request $request, Carbon $now): array
    {
        $filter = (string) $request->input('filter', 'month');
        $year = $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);
        $quarter = (int) $request->input('quarter', $now->quarter);
        $month = ($month >= 1 && $month <= 12) ? $month : $now->month;
        $quarter = ($quarter >= 1 && $quarter <= 4) ? $quarter : $now->quarter;
        $y = ($year === 'all') ? 'all' : ((int) $year ?: $now->year);

        if ($filter === 'year') {
            if ($y === 'all') {
                $min = DB::table('faults')->min('created_at');
                $start = $min ? Carbon::parse($min)->startOfDay() : $now->copy()->startOfYear();
                $end = $now->copy()->endOfDay();
                $label = 'All time';
            } else {
                $start = Carbon::create($y, 1, 1)->startOfYear();
                $end = Carbon::create($y, 1, 1)->endOfYear();
                $label = (string) $y;
            }
        } elseif ($filter === 'quarter') {
            $yy = $y === 'all' ? $now->year : $y;
            $start = Carbon::create($yy, ($quarter - 1) * 3 + 1, 1)->startOfQuarter();
            $end = $start->copy()->endOfQuarter();
            $label = 'Q' . $quarter . ' ' . $yy;
        } elseif ($filter === 'weekly' || $filter === 'range') {
            $filter = 'weekly';
            try { $start = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : $now->copy()->startOfWeek(); } catch (\Throwable $e) { $start = $now->copy()->startOfWeek(); }
            try { $end = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $now->copy()->endOfWeek(); } catch (\Throwable $e) { $end = $now->copy()->endOfWeek(); }
            if ($end->lt($start)) [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            $label = $start->format('d M Y') . ' – ' . $end->format('d M Y');
        } else {
            $filter = 'month';
            $yy = $y === 'all' ? $now->year : $y;
            $start = Carbon::create($yy, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $label = $start->format('F Y');
        }

        return [$start, $end, $filter, $y, $month, $quarter, $label];
    }

    /** One row per fault logged in the period, with resolution facts. */
    private function factsQuery(Carbon $start, Carbon $end, ?string $region, array $resolvedIds, Carbon $now)
    {
        $firstResolved = DB::table('fault_stage_logs')
            ->whereIn('status_id', $resolvedIds)
            ->selectRaw('fault_id, MIN(started_at) as resolved_at')
            ->groupBy('fault_id');

        $in = implode(',', array_map('intval', $resolvedIds));
        $overdueBefore = $now->copy()->subHours(self::OVERDUE_HOURS)->toDateTimeString();

        $q = DB::table('faults as f')
            ->leftJoinSub($firstResolved, 'r', 'r.fault_id', '=', 'f.id')
            ->whereBetween('f.created_at', [$start, $end])
            ->selectRaw("f.id, f.assignedTo,
                CASE WHEN f.status_id IN ($in) THEN 1 ELSE 0 END as is_resolved,
                CASE WHEN f.status_id IN ($in) THEN GREATEST(0, TIMESTAMPDIFF(SECOND, f.created_at, COALESCE(r.resolved_at, f.updated_at))) END as res_seconds,
                CASE WHEN f.status_id NOT IN ($in) AND f.created_at < ? THEN 1 ELSE 0 END as is_overdue", [$overdueBefore]);

        if ($region) {
            $q->join('cities as c', 'c.id', '=', 'f.city_id')->where('c.region', $region);
        }

        return $q;
    }

    private function aggregate($query)
    {
        $sla = self::SLA_SECONDS;
        return $query
            ->selectRaw("COUNT(*) as total,
                SUM(x.is_resolved) as resolved,
                SUM(x.is_overdue) as overdue,
                AVG(x.res_seconds) as avg_seconds,
                SUM(CASE WHEN x.res_seconds IS NOT NULL AND x.res_seconds <= $sla THEN 1 ELSE 0 END) as within_sla")
            ->get()
            ->map(function ($r) {
                $r->total = (int) $r->total;
                $r->resolved = (int) $r->resolved;
                $r->pending = max(0, $r->total - $r->resolved);
                $r->overdue = (int) $r->overdue;
                $r->within_sla = (int) $r->within_sla;
                $r->avg_seconds = $r->avg_seconds !== null ? (int) round((float) $r->avg_seconds) : null;
                $r->resolution_rate = $r->total > 0 ? round($r->resolved / $r->total * 100, 1) : 0;
                $r->sla_rate = $r->resolved > 0 ? round($r->within_sla / $r->resolved * 100, 1) : null;
                $r->avg_label = self::duration($r->avg_seconds);
                return $r;
            });
    }

    private function chartRow($r): array
    {
        return [
            'id' => (int) $r->id,
            'name' => (string) $r->name,
            'total' => $r->total,
            'resolved' => $r->resolved,
            'pending' => $r->pending,
            'rate' => $r->resolution_rate,
            'avg' => $r->avg_label,
        ];
    }

    private function trend(Carbon $start, Carbon $end, ?string $region, array $resolvedIds): array
    {
        $end = $end->copy()->min(Carbon::now()->endOfDay());  // no empty future buckets
        $days = $start->diffInDays($end) + 1;
        $weekly = $days <= 92;
        $labels = $keys = $logged = $resolved = [];

        $cursor = $weekly ? $start->copy()->startOfWeek() : $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $bStart = $cursor->copy()->max($start);
            $bEnd = ($weekly ? $cursor->copy()->endOfWeek() : $cursor->copy()->endOfMonth())->min($end);
            $labels[] = $weekly ? $bStart->format('d M') : $bStart->format('M Y');
            $keys[] = ['from' => $bStart->toDateString(), 'to' => $bEnd->toDateString()];

            $base = DB::table('faults as f')->whereBetween('f.created_at', [$bStart, $bEnd]);
            if ($region) $base->join('cities as c', 'c.id', '=', 'f.city_id')->where('c.region', $region);
            $logged[] = (clone $base)->count();
            $resolved[] = (clone $base)->whereIn('f.status_id', $resolvedIds)->count();

            $cursor = $weekly ? $cursor->addWeek() : $cursor->addMonth();
        }

        return ['labels' => $labels, 'keys' => $keys, 'logged' => $logged, 'resolved' => $resolved, 'bucket' => $weekly ? 'week' : 'month'];
    }

    public static function duration(?int $seconds): string
    {
        if ($seconds === null) return '—';
        $d = intdiv($seconds, 86400);
        $h = intdiv($seconds % 86400, 3600);
        $m = intdiv($seconds % 3600, 60);
        if ($d > 0) return "{$d}d {$h}h";
        if ($h > 0) return "{$h}h {$m}m";
        return "{$m}m";
    }
}
