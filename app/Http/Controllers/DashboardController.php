<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Fault;
use App\Models\FaultAssignment;
use App\Models\ReasonsForOutage;
use App\Models\Customer;
use App\Models\Link;
use App\Models\Status;
use App\Models\City;
use App\Models\Section;
use App\Models\FaultSection;
use App\Models\User;

class DashboardController extends Controller
{
    public function reports(Request $request)
    {
        $period = $request->string('period')->toString() ?: 'this_month';

        $impact = strtolower(trim((string) $request->input('impact', 'all')));
        if (!in_array($impact, ['all', 'direct', 'pop'], true)) {
            $impact = 'all';
        }

        $availableYears = DB::table('faults')
            ->selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

        $yearInput = $request->input('year');
        $monthInput = $request->input('month');
        $selectedYear = ($request->has('year') && $yearInput !== '' && strtolower((string)$yearInput) !== 'all') ? (int)$yearInput : null;
        $selectedMonth = ($request->has('month') && $monthInput !== '' && strtolower((string)$monthInput) !== 'all') ? (int)$monthInput : null;
        $selectedQuarterInput = $request->input('quarter');
        $selectedQuarter = $selectedQuarterInput !== null && $selectedQuarterInput !== '' ? (int) $selectedQuarterInput : null;
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $hasQuarter = $selectedQuarter !== null;
        $hasDateRange = ($startDateInput !== null && $startDateInput !== '') || ($endDateInput !== null && $endDateInput !== '');
        $allTime = false;

        $selectedRegionRaw = trim((string) $request->input('region', ''));
        $selectedRegion = $selectedRegionRaw === '' ? null : $selectedRegionRaw;
        $availableRegions = DB::table('cities')->select('region')->whereNotNull('region')->distinct()->orderBy('region')->pluck('region')->toArray();

        $now = Carbon::now();
        $hasAnyFilter =
            $request->has('year')
            || $request->has('month')
            || $request->has('quarter')
            || $request->has('start_date')
            || $request->has('end_date')
            || $request->has('region');
        if (!$hasAnyFilter) {
            $selectedYear = (int) $now->year;
            $selectedMonth = (int) $now->month;
        }
        if (
            $request->has('month')
            && $monthInput !== null
            && $monthInput !== ''
            && strtolower((string) $monthInput) !== 'all'
            && (!$request->has('year') || $yearInput === null || $yearInput === '' || strtolower((string) $yearInput) === 'all')
            && !$hasQuarter
            && !$hasDateRange
        ) {
            $params = $request->query();
            $params['year'] = (int) $now->year;
            return redirect()->route('dashboard.reports', $params);
        }
        if (
            $request->has('quarter')
            && $selectedQuarterInput !== null
            && $selectedQuarterInput !== ''
            && (!$request->has('year') || $yearInput === null || $yearInput === '' || strtolower((string) $yearInput) === 'all')
            && !$hasDateRange
        ) {
            $params = $request->query();
            $params['year'] = (int) $now->year;
            return redirect()->route('dashboard.reports', $params);
        }
        if ($selectedMonth !== null && $selectedYear === null && !$hasQuarter && !$hasDateRange) {
            $selectedYear = (int) $now->year;
        }
        $allTime = ($selectedYear === null && $selectedMonth === null && !$hasQuarter && !$hasDateRange);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $currentStart = $startOfMonth; $currentEnd = $endOfMonth; $prevStart = $lastMonthStart; $prevEnd = $lastMonthEnd; $prevMonthNum = (int)$now->copy()->subMonthNoOverflow()->format('n');
        if (!$hasQuarter && !$hasDateRange && ($selectedYear !== null || $selectedMonth !== null)) {
            if ($selectedYear !== null && $selectedMonth !== null) {
                $currentStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
                $currentEnd = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
                $prev = $currentStart->copy()->subMonthNoOverflow();
                $prevStart = $prev->startOfMonth();
                $prevEnd = $prev->endOfMonth();
                $prevMonthNum = (int)$prev->format('n');
            } elseif ($selectedYear !== null && $selectedMonth === null) {
                $currentStart = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $currentEnd = Carbon::create($selectedYear, 12, 31)->endOfYear();
                $prevYear = $selectedYear - 1;
                $prevStart = Carbon::create($prevYear, 1, 1)->startOfYear();
                $prevEnd = Carbon::create($prevYear, 12, 31)->endOfYear();
            } else { // month selected across all years
                $prevMonthNum = $selectedMonth === 1 ? 12 : $selectedMonth - 1;
            }
        }

        if ($hasQuarter) {
            $quarterYear = $selectedYear !== null ? $selectedYear : (int) $now->year;
            $startMonth = ($selectedQuarter - 1) * 3 + 1;
            $currentStart = Carbon::create($quarterYear, $startMonth, 1)->startOfQuarter();
            $currentEnd = Carbon::create($quarterYear, $startMonth, 1)->endOfQuarter();

            $prevBase = Carbon::create($quarterYear, $startMonth, 1)->subMonths(3);
            $prevStart = $prevBase->copy()->startOfQuarter();
            $prevEnd = $prevBase->copy()->endOfQuarter();
        } elseif ($hasDateRange) {
            $rangeStart = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : Carbon::create(2000, 1, 1)->startOfDay();
            $rangeEnd = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : $now->copy()->endOfDay();
            if ($rangeEnd->lessThan($rangeStart)) {
                $tmp = $rangeStart;
                $rangeStart = $rangeEnd;
                $rangeEnd = $tmp;
            }
            $currentStart = $rangeStart;
            $currentEnd = $rangeEnd;

            $rangeSeconds = $currentEnd->diffInSeconds($currentStart) ?: 1;
            $prevEnd = $currentStart->copy()->subSecond();
            $prevStart = $prevEnd->copy()->subSeconds($rangeSeconds);
        }

        $periodLabelText = null;
        if ($allTime) {
            $minCreatedAt = Fault::min('created_at');
            $periodStart = $minCreatedAt ? Carbon::parse($minCreatedAt)->startOfDay() : $now->copy()->startOfDay();
            $periodEnd = $now->copy()->endOfDay();
            $periodLabelText = 'All time';
        } else {
            $periodStart = $currentStart;
            $periodEnd = $currentEnd;
            if ($hasDateRange) {
                $periodLabelText = 'Custom date range';
            } elseif ($hasQuarter && $selectedQuarter !== null) {
                $periodLabelText = 'Quarter ' . $selectedQuarter;
            } elseif ($selectedYear !== null && $selectedMonth !== null) {
                $periodLabelText = $currentStart->format('F Y');
            } elseif ($selectedYear !== null && $selectedMonth === null) {
                $periodLabelText = (string) $selectedYear;
            } elseif ($selectedMonth !== null && $selectedYear === null) {
                $periodLabelText = $currentStart->format('F');
            } else {
                $periodLabelText = 'Current month';
            }
        }

        if ($selectedMonth !== null && $selectedYear === null && !$hasQuarter && !$hasDateRange) {
            $faultsThisMonthQuery = Fault::query()->whereMonth('created_at', $selectedMonth);
            if ($selectedRegion) {
                $faultsThisMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $faultsThisMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $faultsThisMonthQuery->whereNotNull('root_fault_id');
            $faultsThisMonth = $faultsThisMonthQuery->count();

            $faultsLastMonthQuery = Fault::query()->whereMonth('created_at', $prevMonthNum);
            if ($selectedRegion) {
                $faultsLastMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $faultsLastMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $faultsLastMonthQuery->whereNotNull('root_fault_id');
            $faultsLastMonth = $faultsLastMonthQuery->count();
        } elseif (!$allTime) {
            $faultsThisMonthQuery = Fault::whereBetween('created_at', [$currentStart, $currentEnd]);
            if ($selectedRegion) {
                $faultsThisMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $faultsThisMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $faultsThisMonthQuery->whereNotNull('root_fault_id');
            $faultsThisMonth = $faultsThisMonthQuery->count();

            $faultsLastMonthQuery = Fault::whereBetween('created_at', [$prevStart, $prevEnd]);
            if ($selectedRegion) {
                $faultsLastMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $faultsLastMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $faultsLastMonthQuery->whereNotNull('root_fault_id');
            $faultsLastMonth = $faultsLastMonthQuery->count();
        } else {
            $faultsThisMonthQuery = Fault::query();
            if ($selectedRegion) {
                $faultsThisMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $faultsThisMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $faultsThisMonthQuery->whereNotNull('root_fault_id');
            $faultsThisMonth = $faultsThisMonthQuery->count();

            $faultsLastMonthQuery = Fault::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd]);
            if ($selectedRegion) {
                $faultsLastMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $faultsLastMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $faultsLastMonthQuery->whereNotNull('root_fault_id');
            $faultsLastMonth = $faultsLastMonthQuery->count();
        }

        // KPI: New Customers (respects month/year filters; all-time when both are "All")
        if ($selectedMonth !== null && $selectedYear === null) {
            $customersThisMonth = Customer::query()->whereMonth('created_at', $selectedMonth)->count();
            $customersLastMonth = Customer::query()->whereMonth('created_at', $prevMonthNum)->count();
        } elseif (!$allTime) {
            $customersThisMonth = Customer::whereBetween('created_at', [$currentStart, $currentEnd])->count();
            $customersLastMonth = Customer::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        } else {
            $customersThisMonth = Customer::count();
            $customersLastMonth = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        }

        // KPI: Avg MTTR from stage logs (respects filters; all-time when both are "All")
        $resolvedStatusIds = Status::where('status_code', 'like', 'CL%')->pluck('id');
        $slaThreshold = 24 * 3600; // 24 hours

        if ($selectedMonth !== null && $selectedYear === null && !$hasQuarter && !$hasDateRange) {
            $mttrThisMonthQuery = Fault::whereIn('status_id', $resolvedStatusIds)
                ->whereMonth('updated_at', $selectedMonth);
            if ($selectedRegion) {
                $mttrThisMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $mttrThisMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $mttrThisMonthQuery->whereNotNull('root_fault_id');
            $mttrThisMonth = $mttrThisMonthQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as val')
                ->value('val') ?? 0;

            $mttrLastMonthQuery = Fault::whereIn('status_id', $resolvedStatusIds)
                ->whereMonth('updated_at', $prevMonthNum);
            if ($selectedRegion) {
                $mttrLastMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $mttrLastMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $mttrLastMonthQuery->whereNotNull('root_fault_id');
            $mttrLastMonth = $mttrLastMonthQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as val')
                ->value('val') ?? 0;
        } elseif (!$allTime) {
            $mttrThisMonthQuery = Fault::whereIn('status_id', $resolvedStatusIds)
                ->whereBetween('updated_at', [$currentStart, $currentEnd]);
            if ($selectedRegion) {
                $mttrThisMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $mttrThisMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $mttrThisMonthQuery->whereNotNull('root_fault_id');
            $mttrThisMonth = $mttrThisMonthQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as val')
                ->value('val') ?? 0;

            $mttrLastMonthQuery = Fault::whereIn('status_id', $resolvedStatusIds)
                ->whereBetween('updated_at', [$prevStart, $prevEnd]);
            if ($selectedRegion) {
                $mttrLastMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $mttrLastMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $mttrLastMonthQuery->whereNotNull('root_fault_id');
            $mttrLastMonth = $mttrLastMonthQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as val')
                ->value('val') ?? 0;
        } else {
            $mttrThisMonthQuery = Fault::whereIn('status_id', $resolvedStatusIds);
            if ($selectedRegion) {
                $mttrThisMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $mttrThisMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $mttrThisMonthQuery->whereNotNull('root_fault_id');
            $mttrThisMonth = $mttrThisMonthQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as val')
                ->value('val') ?? 0;

            $mttrLastMonthQuery = Fault::whereIn('status_id', $resolvedStatusIds)
                ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd]);
            if ($selectedRegion) {
                $mttrLastMonthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $mttrLastMonthQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $mttrLastMonthQuery->whereNotNull('root_fault_id');
            $mttrLastMonth = $mttrLastMonthQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as val')
                ->value('val') ?? 0;
        }

        // SLA compliance (duration < 24h in stage logs; respects filters; all-time when both are "All")
        if ($selectedMonth !== null && $selectedYear === null && !$hasQuarter && !$hasDateRange) {
            $slaQuery = Fault::whereIn('status_id', $resolvedStatusIds)->whereMonth('updated_at', $selectedMonth);
        } elseif (!$allTime) {
            $slaQuery = Fault::whereIn('status_id', $resolvedStatusIds)->whereBetween('updated_at', [$currentStart, $currentEnd]);
        } else {
            $slaQuery = Fault::whereIn('status_id', $resolvedStatusIds);
        }
        if ($selectedRegion) {
            $slaQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $slaQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $slaQuery->whereNotNull('root_fault_id');
        $slaCount = $slaQuery->count();
        $slaMetCount = (clone $slaQuery)->whereRaw('TIMESTAMPDIFF(SECOND, created_at, updated_at) <= ?', [$slaThreshold])->count();
        $slaCompliance = $slaCount > 0 ? round(($slaMetCount / $slaCount) * 100, 1) : 0;

        // Faults per past 12 months (labels and counts)
        $monthlyLabels = [];
        $monthlyCounts = [];

        $trendEnd = $periodEnd->copy()->endOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $from = $trendEnd->copy()->subMonths($i)->startOfMonth();
            $to = $trendEnd->copy()->subMonths($i)->endOfMonth();
            $label = $from->format('M Y');
            
            $monthlyQuery = Fault::whereBetween('created_at', [$from, $to]);
            if ($selectedRegion) {
                $monthlyQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $monthlyQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $monthlyQuery->whereNotNull('root_fault_id');
            $count = $monthlyQuery->count();
            $monthlyLabels[] = $label;
            $monthlyCounts[] = $count;
        }

        // Status distribution (join statuses for labels if available)
        $statusBreakdownQuery = Fault::select('status_id', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $statusBreakdownQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $statusBreakdownQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $statusBreakdownQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $statusBreakdownQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $statusBreakdownQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $statusBreakdownQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $statusBreakdownQuery->whereNotNull('root_fault_id');
        $statusBreakdown = $statusBreakdownQuery->groupBy('status_id')->get();
        $statusLabels = [];
        $statusValues = [];
        foreach ($statusBreakdown as $row) {
            $label = 'Status ' . ($row->status_id ?? 'N/A');
            if ($row->status_id) {
                $s = Status::find($row->status_id);
                if ($s) $label = $s->description ?? $s->status_code ?? $label;
            }
            $statusLabels[] = $label;
            $statusValues[] = (int) $row->c;
        }

        // Category split: direct faults vs POP-impacted child faults
        // Rule: child faults have root_fault_id set, direct faults have root_fault_id NULL
        $faultCategoryBase = Fault::query();
        if ($hasDateRange || $hasQuarter) {
            $faultCategoryBase->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $faultCategoryBase->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $faultCategoryBase->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $faultCategoryBase->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $faultCategoryBase->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }

        if ($impact === 'direct') {
            $faultCategoryTotal = (clone $faultCategoryBase)->whereNull('root_fault_id')->count();
            $directFaultCount = $faultCategoryTotal;
            $popImpactedCount = 0;
        } elseif ($impact === 'pop') {
            $faultCategoryTotal = (clone $faultCategoryBase)->whereNotNull('root_fault_id')->count();
            $popImpactedCount = $faultCategoryTotal;
            $directFaultCount = 0;
        } else {
            $faultCategoryTotal = (clone $faultCategoryBase)->count();
            $popImpactedCount = (clone $faultCategoryBase)->whereNotNull('root_fault_id')->count();
            $directFaultCount = max(0, $faultCategoryTotal - $popImpactedCount);
        }
        $faultCategoryLabels = ['Direct Faults', 'POP Impacted Faults'];
        $faultCategoryValues = [$directFaultCount, $popImpactedCount];

        // Monthly trend by category (last 12 months, ending at selected period end)
        $faultCategoryMonthlyLabels = [];
        $faultCategoryMonthlyDirect = [];
        $faultCategoryMonthlyPop = [];
        $trendEndForCategory = $periodEnd->copy()->endOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $from = $trendEndForCategory->copy()->subMonths($i)->startOfMonth();
            $to = $trendEndForCategory->copy()->subMonths($i)->endOfMonth();
            $faultCategoryMonthlyLabels[] = $from->format('M Y');

            $monthBase = Fault::whereBetween('created_at', [$from, $to]);
            if ($selectedRegion) {
                $monthBase->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }

            if ($impact === 'direct') {
                $monthTotal = (clone $monthBase)->whereNull('root_fault_id')->count();
                $faultCategoryMonthlyPop[] = 0;
                $faultCategoryMonthlyDirect[] = (int) $monthTotal;
            } elseif ($impact === 'pop') {
                $monthTotal = (clone $monthBase)->whereNotNull('root_fault_id')->count();
                $faultCategoryMonthlyPop[] = (int) $monthTotal;
                $faultCategoryMonthlyDirect[] = 0;
            } else {
                $monthPop = (clone $monthBase)->whereNotNull('root_fault_id')->count();
                $monthTotal = (clone $monthBase)->count();
                $faultCategoryMonthlyPop[] = (int) $monthPop;
                $faultCategoryMonthlyDirect[] = (int) max(0, $monthTotal - $monthPop);
            }
        }

        // RFO distribution (confirmed)
        $rfoBreakdownQuery = Fault::select('confirmedRfo_id', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $rfoBreakdownQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $rfoBreakdownQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $rfoBreakdownQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $rfoBreakdownQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $rfoBreakdownQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $rfoBreakdownQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $rfoBreakdownQuery->whereNotNull('root_fault_id');
        $rfoBreakdown = $rfoBreakdownQuery->groupBy('confirmedRfo_id')->get();
        $rfoLabels = [];
        $rfoValues = [];
        foreach ($rfoBreakdown as $row) {
            $label = 'RFO ' . ($row->confirmedRfo_id ?? 'N/A');
            if ($row->confirmedRfo_id) {
                $rfo = ReasonsForOutage::find($row->confirmedRfo_id);
                if ($rfo) $label = $rfo->RFO ?? $label;
            }
            $rfoLabels[] = $label;
            $rfoValues[] = (int) $row->c;
        }

        // RFO distribution (suspected)
        $suspectedRfoBreakdownQuery = Fault::select('suspectedRfo_id', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $suspectedRfoBreakdownQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $suspectedRfoBreakdownQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $suspectedRfoBreakdownQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $suspectedRfoBreakdownQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $suspectedRfoBreakdownQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $suspectedRfoBreakdownQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $suspectedRfoBreakdownQuery->whereNotNull('root_fault_id');
        $suspectedRfoBreakdown = $suspectedRfoBreakdownQuery->groupBy('suspectedRfo_id')->get();
        $suspectedRfoLabels = [];
        $suspectedRfoValues = [];
        foreach ($suspectedRfoBreakdown as $row) {
            $label = 'RFO ' . ($row->suspectedRfo_id ?? 'N/A');
            if ($row->suspectedRfo_id) {
                $rfo = ReasonsForOutage::find($row->suspectedRfo_id);
                if ($rfo) $label = $rfo->RFO ?? $label;
            }
            $suspectedRfoLabels[] = $label;
            $suspectedRfoValues[] = (int) $row->c;
        }

        // RFO trend (last 6 months)
        $rfoMonthlyLabels = [];
        $rfoMonthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $from = $now->copy()->subMonths($i)->startOfMonth();
            $to = $now->copy()->subMonths($i)->endOfMonth();
            $rfoMonthlyLabels[] = $from->format('M Y');
            $rfoMonthlyQuery = Fault::whereBetween('created_at', [$from, $to])
                ->whereNotNull('confirmedRfo_id');
            if ($selectedRegion) {
                $rfoMonthlyQuery->whereHas('city', function ($q) use ($selectedRegion) {
                    $q->where('region', $selectedRegion);
                });
            }
            if ($impact === 'direct') $rfoMonthlyQuery->whereNull('root_fault_id');
            elseif ($impact === 'pop') $rfoMonthlyQuery->whereNotNull('root_fault_id');
            $rfoMonthlyCounts[] = $rfoMonthlyQuery->count();
        }

        // Technician workload (open assignments)
        $workload = FaultAssignment::whereNull('resolved_at')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->groupBy('user_id')
            ->orderByDesc('c')
            ->limit(10)
            ->get();
        $workloadLabels = $workload->pluck('user_id')->map(fn($id) => 'User ' . $id)->toArray();
        $workloadValues = $workload->pluck('c')->map(fn($x) => (int) $x)->toArray();

        // Link inventory by type
        $linkInventory = Link::select('linkType_id', DB::raw('COUNT(*) as c'))
            ->groupBy('linkType_id')->get();
        $linkLabels = $linkInventory->map(function($row){ return 'Type ' . ($row->linkType_id ?? 'N/A'); })->toArray();
        $linkValues = $linkInventory->pluck('c')->map(fn($x) => (int) $x)->toArray();

        // Priority × FaultType heatmap
        $priorityHeatmapRaw = Fault::select('faultType','priorityLevel', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $priorityHeatmapRaw->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $priorityHeatmapRaw->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $priorityHeatmapRaw->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $priorityHeatmapRaw->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $priorityHeatmapRaw->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $priorityHeatmapRaw->whereNull('root_fault_id');
        elseif ($impact === 'pop') $priorityHeatmapRaw->whereNotNull('root_fault_id');
        $priorityHeatmapRaw = $priorityHeatmapRaw->groupBy('faultType','priorityLevel')->get();
        $faultTypeLabels = $priorityHeatmapRaw->pluck('faultType')->unique()->values()->all();
        $priorityLabelsHeat = $priorityHeatmapRaw->pluck('priorityLevel')->unique()->values()->all();
        $priorityMatrix = [];
        foreach ($priorityLabelsHeat as $p) {
            $rowData = [];
            foreach ($faultTypeLabels as $t) {
                $val = $priorityHeatmapRaw->firstWhere(fn($r) => ($r->faultType === $t) && ($r->priorityLevel === $p));
                $rowData[] = (int) ($val->c ?? 0);
            }
            $priorityMatrix[] = [ 'label' => $p ?? 'N/A', 'data' => $rowData ];
        }

        // Customer impact (count & duration)
        $customerImpactCountQuery = Fault::select('customer_id', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $customerImpactCountQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $customerImpactCountQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $customerImpactCountQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $customerImpactCountQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $customerImpactCountQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $customerImpactCountQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $customerImpactCountQuery->whereNotNull('root_fault_id');
        $customerImpactCountRaw = $customerImpactCountQuery->groupBy('customer_id')->orderByDesc('c')->limit(10)->get();
        $customerImpactCountLabels = [];$customerImpactCountValues = [];
        foreach ($customerImpactCountRaw as $row) {
            $cust = $row->customer_id ? Customer::find($row->customer_id) : null;
            $customerImpactCountLabels[] = $cust->customer ?? ('Customer ' . ($row->customer_id ?? 'N/A'));
            $customerImpactCountValues[] = (int) $row->c;
        }

        $customerImpactDurationQuery = Fault::whereIn('status_id', $resolvedStatusIds)
            ->select('customer_id', DB::raw('SUM(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as sec'));

        if ($hasDateRange || $hasQuarter) {
            $customerImpactDurationQuery->whereBetween('updated_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $customerImpactDurationQuery->whereMonth('updated_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $customerImpactDurationQuery->whereYear('updated_at', $selectedYear);
            if ($selectedMonth !== null) $customerImpactDurationQuery->whereMonth('updated_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $customerImpactDurationQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $customerImpactDurationQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $customerImpactDurationQuery->whereNotNull('root_fault_id');
        $customerImpactDurationRaw = $customerImpactDurationQuery->groupBy('customer_id')->orderByDesc('sec')->limit(10)->get();
        $customerImpactDurationLabels = [];$customerImpactDurationValues = [];
        foreach ($customerImpactDurationRaw as $row) {
            $cust = $row->customer_id ? Customer::find($row->customer_id) : null;
            $customerImpactDurationLabels[] = $cust->customer ?? ('Customer ' . ($row->customer_id ?? 'N/A'));
            $customerImpactDurationValues[] = (int) ($row->sec ?? 0);
        }

        // Service impact by type
        $serviceTypeBreakdownQuery = Fault::select('serviceType', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $serviceTypeBreakdownQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $serviceTypeBreakdownQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $serviceTypeBreakdownQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $serviceTypeBreakdownQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $serviceTypeBreakdownQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $serviceTypeBreakdownQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $serviceTypeBreakdownQuery->whereNotNull('root_fault_id');
        $serviceTypeBreakdown = $serviceTypeBreakdownQuery->groupBy('serviceType')->orderByDesc('c')->get();
        $serviceTypeLabels = $serviceTypeBreakdown->pluck('serviceType')->map(fn($x) => $x ?? 'N/A')->toArray();
        $serviceTypeValues = $serviceTypeBreakdown->pluck('c')->map(fn($x) => (int) $x)->toArray();

        // Geography: faults by REGION (instead of city)
        $regionFaultsQuery = Fault::join('cities', 'faults.city_id', '=', 'cities.id')
            ->select('cities.region', DB::raw('COUNT(*) as c'));

        if ($hasDateRange || $hasQuarter) {
            $regionFaultsQuery->whereBetween('faults.created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $regionFaultsQuery->whereMonth('faults.created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $regionFaultsQuery->whereYear('faults.created_at', $selectedYear);
            if ($selectedMonth !== null) $regionFaultsQuery->whereMonth('faults.created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $regionFaultsQuery->where('cities.region', $selectedRegion);
        }
        if ($impact === 'direct') $regionFaultsQuery->whereNull('faults.root_fault_id');
        elseif ($impact === 'pop') $regionFaultsQuery->whereNotNull('faults.root_fault_id');

        $regionFaultsRaw = $regionFaultsQuery->groupBy('cities.region')
            ->orderByDesc('c')
            ->limit(10)
            ->get();

        $cityFaultsLabels = []; // Keeping variable name same to avoid breaking frontend immediately, or rename it?
                                // Frontend expects 'cityFaultsLabels' and 'cityFaultsValues' in reports.js
                                // I will map regions to these variables.
        $cityFaultsValues = [];
        
        foreach ($regionFaultsRaw as $row) {
            $cityFaultsLabels[] = $row->region ?? 'Unknown Region';
            $cityFaultsValues[] = (int) $row->c;
        }

        // Account manager performance
        $amFaultsQuery = Fault::select('accountManager_id', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $amFaultsQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $amFaultsQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $amFaultsQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $amFaultsQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $amFaultsQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $amFaultsQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $amFaultsQuery->whereNotNull('root_fault_id');
        $amFaultsRaw = $amFaultsQuery->groupBy('accountManager_id')->orderByDesc('c')->limit(10)->get();
        $amLabels = [];$amFaultsValues = [];
        foreach ($amFaultsRaw as $row) {
            $name = DB::table('account_managers')->where('id',$row->accountManager_id)->value('accountManager');
            $amLabels[] = $name ?? ('AM ' . ($row->accountManager_id ?? 'N/A'));
            $amFaultsValues[] = (int) $row->c;
        }
        $amMttrQuery = Fault::whereIn('status_id', $resolvedStatusIds)
            ->select('accountManager_id', DB::raw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as mttr'));
        
        if ($hasDateRange || $hasQuarter) {
            $amMttrQuery->whereBetween('updated_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $amMttrQuery->whereMonth('updated_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $amMttrQuery->whereYear('updated_at', $selectedYear);
            if ($selectedMonth !== null) $amMttrQuery->whereMonth('updated_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $amMttrQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $amMttrQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $amMttrQuery->whereNotNull('root_fault_id');
        
        $amMttrRaw = $amMttrQuery->groupBy('accountManager_id')->get();
        $amMttrMap = [];
        foreach ($amMttrRaw as $r) { $amMttrMap[$r->accountManager_id ?? 0] = (int) ($r->mttr ?? 0); }
        $amMttrValues = [];
        foreach ($amFaultsRaw as $row) { $amMttrValues[] = $amMttrMap[$row->accountManager_id ?? 0] ?? 0; }

        $mttaBaseQuery = DB::table('fault_assignments')
            ->join('faults','fault_assignments.fault_id','=','faults.id');
        if ($selectedRegion) {
            $mttaBaseQuery->join('cities','faults.city_id','=','cities.id')
                ->where('cities.region', $selectedRegion);
        }
        if ($impact === 'direct') {
            $mttaBaseQuery->whereNull('faults.root_fault_id');
        } elseif ($impact === 'pop') {
            $mttaBaseQuery->whereNotNull('faults.root_fault_id');
        }

        if ($selectedMonth !== null && $selectedYear === null && !$hasQuarter && !$hasDateRange) {
            $mttaThisMonth = (clone $mttaBaseQuery)
                ->whereMonth('fault_assignments.assigned_at', $selectedMonth)
                ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
            $mttaLastMonth = (clone $mttaBaseQuery)
                ->whereMonth('fault_assignments.assigned_at', $prevMonthNum)
                ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
        } elseif (!$allTime) {
            $mttaThisMonth = (clone $mttaBaseQuery)
                ->whereBetween('fault_assignments.assigned_at', [$currentStart, $currentEnd])
                ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
            $mttaLastMonth = (clone $mttaBaseQuery)
                ->whereBetween('fault_assignments.assigned_at', [$prevStart, $prevEnd])
                ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
        } else {
            $mttaThisMonth = (clone $mttaBaseQuery)
                ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
            $mttaLastMonth = (clone $mttaBaseQuery)
                ->whereBetween('fault_assignments.assigned_at', [$lastMonthStart, $lastMonthEnd])
                ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
        }

        // SLA by priority
        $priorityTargets = [ 'P1' => 4*3600, 'P2' => 8*3600, 'P3' => 24*3600, 'P4' => 48*3600 ];
        
        $sumsQuery = Fault::whereIn('status_id', $resolvedStatusIds)
            ->select('priorityLevel', DB::raw('TIMESTAMPDIFF(SECOND, created_at, updated_at) as duration'));

        if ($hasDateRange || $hasQuarter) {
            $sumsQuery->whereBetween('updated_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $sumsQuery->whereMonth('updated_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $sumsQuery->whereYear('updated_at', $selectedYear);
            if ($selectedMonth !== null) $sumsQuery->whereMonth('updated_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $sumsQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $sumsQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $sumsQuery->whereNotNull('root_fault_id');
        
        $sums = $sumsQuery->get();
        $slaPriorityTotals = [];$slaPriorityMet = [];
        
        foreach ($sums as $s) {
            $p = $s->priorityLevel ?? 'N/A';
            $slaPriorityTotals[$p] = ($slaPriorityTotals[$p] ?? 0) + 1;
            $target = $priorityTargets[$p] ?? (24*3600);
            if (($s->duration ?? 0) <= $target) { $slaPriorityMet[$p] = ($slaPriorityMet[$p] ?? 0) + 1; }
        }
        
        $slaPriorityLabels = array_keys($slaPriorityTotals);
        $slaPriorityValues = [];
        foreach ($slaPriorityLabels as $p) {
            $tot = $slaPriorityTotals[$p] ?? 0; $met = $slaPriorityMet[$p] ?? 0;
            $slaPriorityValues[] = $tot > 0 ? round(($met / $tot) * 100, 1) : 0;
        }

        // Stage bottlenecks - DATA UNAVAILABLE WITHOUT FAULT STAGE LOGS
        // Returning empty data to prevent errors
        $stageBottlenecksLabels = [];
        $stageBottlenecksValues = [];

        // Reopen rate - DATA UNAVAILABLE WITHOUT HISTORY LOGS
        $reopenRate = 0;

        // Workload by section
        $sectionWorkloadRaw = FaultSection::select('section_id', DB::raw('COUNT(*) as c'))
            ->groupBy('section_id')->orderByDesc('c')->limit(10)->get();
        $sectionWorkloadLabels = [];$sectionWorkloadValues = [];
        foreach ($sectionWorkloadRaw as $row) {
            $sec = Section::find($row->section_id);
            $sectionWorkloadLabels[] = $sec->section ?? ('Section ' . ($row->section_id ?? 'N/A'));
            $sectionWorkloadValues[] = (int) $row->c;
        }

        // Technician load (open vs resolved)
        $techOpenRaw = FaultAssignment::whereNull('resolved_at')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->groupBy('user_id')->orderByDesc('c')->limit(10)->get();
        $techResolvedRaw = FaultAssignment::whereNotNull('resolved_at')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->groupBy('user_id')->get();
        $techOpenMap = [];$techResolvedMap = [];
        foreach ($techOpenRaw as $r) { $techOpenMap[$r->user_id ?? 0] = (int) $r->c; }
        foreach ($techResolvedRaw as $r) { $techResolvedMap[$r->user_id ?? 0] = (int) $r->c; }
        $techLoadLabels = [];$techLoadOpen = [];$techLoadResolved = [];
        foreach ($techOpenRaw as $row) {
            $user = $row->user_id ? User::find($row->user_id) : null;
            $techLoadLabels[] = $user->name ?? ('User ' . ($row->user_id ?? 'N/A'));
            $techLoadOpen[] = (int) $row->c;
            $techLoadResolved[] = $techResolvedMap[$row->user_id ?? 0] ?? 0;
        }

        // Standby effectiveness
        $standbyEffRaw = FaultAssignment::whereNotNull('duration_seconds')
            ->select('is_standby', DB::raw('AVG(duration_seconds) as avg_dur'))
            ->groupBy('is_standby')->get();
        $standbyLabels = $standbyEffRaw->pluck('is_standby')->map(fn($x) => $x ? 'Standby' : 'Non-Standby')->toArray();
        $standbyValues = $standbyEffRaw->pluck('avg_dur')->map(fn($x) => (int) $x)->toArray();

        // Regional performance by assignment.region
        $regionalPerfRaw = FaultAssignment::whereNotNull('resolved_at')
            ->whereNotNull('duration_seconds')
            ->select('region', DB::raw('AVG(duration_seconds) as avg_dur'))
            ->groupBy('region')->get();
        $regionalPerfLabels = $regionalPerfRaw->pluck('region')->map(fn($x) => $x ?? 'N/A')->toArray();
        $regionalPerfValues = $regionalPerfRaw->pluck('avg_dur')->map(fn($x) => (int) $x)->toArray();

        // Portfolio summary (top 10)
        $linksByCustomerQuery = Link::select('links.customer_id', DB::raw('COUNT(*) as c'));
        if ($selectedRegion) {
            $linksByCustomerQuery->join('cities', 'links.city_id', '=', 'cities.id')
                ->where('cities.region', $selectedRegion);
        }
        $linksByCustomer = $linksByCustomerQuery->groupBy('links.customer_id')->get()->keyBy('customer_id');

        $openFaultsByCustomerQuery = FaultAssignment::whereNull('fault_assignments.resolved_at')
            ->join('faults', 'fault_assignments.fault_id', '=', 'faults.id')
            ->select('faults.customer_id', DB::raw('COUNT(DISTINCT fault_assignments.fault_id) as c'));
        if (!$allTime) {
            $openFaultsByCustomerQuery->whereBetween('faults.created_at', [$currentStart, $currentEnd]);
        }
        if ($selectedRegion) {
            $openFaultsByCustomerQuery->join('cities', 'faults.city_id', '=', 'cities.id')
                ->where('cities.region', $selectedRegion);
        }
        if ($impact === 'direct') $openFaultsByCustomerQuery->whereNull('faults.root_fault_id');
        elseif ($impact === 'pop') $openFaultsByCustomerQuery->whereNotNull('faults.root_fault_id');
        $openFaultsByCustomer = $openFaultsByCustomerQuery->groupBy('faults.customer_id')->get()->keyBy('customer_id');

        $recentRfoByCustomerQuery = Fault::whereNotNull('confirmedRfo_id')
            ->select('customer_id', DB::raw('COUNT(*) as c'));
        if (!$allTime) {
            $recentRfoByCustomerQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } else {
            $recentRfoByCustomerQuery->where('created_at', '>=', $now->copy()->subDays(90));
        }
        if ($selectedRegion) {
            $recentRfoByCustomerQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $recentRfoByCustomerQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $recentRfoByCustomerQuery->whereNotNull('root_fault_id');
        $recentRfoByCustomer = $recentRfoByCustomerQuery->groupBy('customer_id')->get()->keyBy('customer_id');
        $portfolioRows = [];
        foreach ($linksByCustomer as $cid => $row) {
            $cust = $cid ? Customer::find($cid) : null;
            $portfolioRows[] = [
                'customer_id' => (int) ($cid ?? 0),
                'customer' => $cust->customer ?? ('Customer ' . ($cid ?? 'N/A')),
                'links' => (int) ($row->c ?? 0),
                'open_faults' => (int) ($openFaultsByCustomer[$cid]->c ?? 0),
                'recent_rfos' => (int) ($recentRfoByCustomer[$cid]->c ?? 0),
            ];
        }
        usort($portfolioRows, fn($a,$b) => ($b['open_faults'] <=> $a['open_faults']));
        $portfolioRows = array_slice($portfolioRows, 0, 10);

        // Churn risk (MoM increase)
        $custFaultsThisQuery = Fault::whereBetween('created_at', [$currentStart, $currentEnd])
            ->select('customer_id', DB::raw('COUNT(*) as c'));
        $custFaultsLastQuery = Fault::whereBetween('created_at', [$prevStart, $prevEnd])
            ->select('customer_id', DB::raw('COUNT(*) as c'));
        if ($selectedRegion) {
            $custFaultsThisQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
            $custFaultsLastQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') {
            $custFaultsThisQuery->whereNull('root_fault_id');
            $custFaultsLastQuery->whereNull('root_fault_id');
        } elseif ($impact === 'pop') {
            $custFaultsThisQuery->whereNotNull('root_fault_id');
            $custFaultsLastQuery->whereNotNull('root_fault_id');
        }
        $custFaultsThis = $custFaultsThisQuery->groupBy('customer_id')->get()->keyBy('customer_id');
        $custFaultsLast = $custFaultsLastQuery->groupBy('customer_id')->get()->keyBy('customer_id');
        $churnRows = [];
        foreach ($custFaultsThis as $cid => $r) {
            $diff = ((int) ($r->c ?? 0)) - ((int) ($custFaultsLast[$cid]->c ?? 0));
            if ($diff > 0) {
                $cust = $cid ? Customer::find($cid) : null;
                $churnRows[] = [ 'customer_id' => (int) ($cid ?? 0), 'customer' => $cust->customer ?? ('Customer ' . ($cid ?? 'N/A')), 'delta' => $diff ];
            }
        }
        usort($churnRows, fn($a,$b) => ($b['delta'] <=> $a['delta']));
        $churnRows = array_slice($churnRows, 0, 10);

        // Links & Inventory extras
        $linkStatusBreakdown = Link::select('link_status', DB::raw('COUNT(*) as c'))
            ->groupBy('link_status')->get();
        $linkStatusLabels = $linkStatusBreakdown->pluck('link_status')->map(fn($x) => $x ?? 'N/A')->toArray();
        $linkStatusValues = $linkStatusBreakdown->pluck('c')->map(fn($x) => (int) $x)->toArray();
        $linkServiceTypeBreakdown = Link::select('service_type', DB::raw('COUNT(*) as c'))
            ->groupBy('service_type')->get();
        $linkServiceTypeLabels = $linkServiceTypeBreakdown->pluck('service_type')->map(fn($x) => $x ?? 'N/A')->toArray();
        $linkServiceTypeValues = $linkServiceTypeBreakdown->pluck('c')->map(fn($x) => (int) $x)->toArray();
        $linkCapacityBreakdown = Link::select('capacity', DB::raw('COUNT(*) as c'))
            ->groupBy('capacity')->orderBy('capacity')->get();
        $linkCapacityLabels = $linkCapacityBreakdown->pluck('capacity')->map(fn($x) => $x ?? 'N/A')->toArray();
        $linkCapacityValues = $linkCapacityBreakdown->pluck('c')->map(fn($x) => (int) $x)->toArray();

        // Activation pipeline
        $linksMonthlyLabels = [];$linksMonthlyCreated = [];$linksMonthlyJcc = [];
        for ($i = 5; $i >= 0; $i--) {
            $from = $now->copy()->subMonths($i)->startOfMonth();
            $to = $now->copy()->subMonths($i)->endOfMonth();
            $linksMonthlyLabels[] = $from->format('M Y');
            $linksMonthlyCreated[] = Link::whereBetween('created_at', [$from,$to])->count();
            $linksMonthlyJcc[] = Link::whereBetween('created_at', [$from,$to])->whereNotNull('jcc_number')->count();
        }

        // Link health
        $linkHealthQuery = Fault::whereNotNull('link_id')
            ->select('link_id', DB::raw('COUNT(*) as c'));
        if ($hasDateRange || $hasQuarter) {
            $linkHealthQuery->whereBetween('created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $linkHealthQuery->whereMonth('created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $linkHealthQuery->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== null) $linkHealthQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $linkHealthQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $linkHealthQuery->whereNull('root_fault_id');
        elseif ($impact === 'pop') $linkHealthQuery->whereNotNull('root_fault_id');
        $linkHealthRaw = $linkHealthQuery->groupBy('link_id')->orderByDesc('c')->limit(10)->get();
        $linkHealthLabels = $linkHealthRaw->pluck('link_id')->map(fn($id) => 'Link ' . $id)->toArray();
        $linkHealthValues = $linkHealthRaw->pluck('c')->map(fn($x) => (int) $x)->toArray();

        // Geography: links per city & coverage gaps
        $linksPerCityRaw = Link::select('city_id', DB::raw('COUNT(*) as links'))
            ->groupBy('city_id')->get()->keyBy('city_id');
        $faultsPerCityRaw = Fault::select('city_id', DB::raw('COUNT(*) as faults'))
            ->groupBy('city_id')->get()->keyBy('city_id');
        $linksPerCityLabels = [];$linksPerCityValues = [];$coverageGapValues = [];
        foreach ($linksPerCityRaw as $cid => $row) {
            $city = $cid ? City::find($cid) : null;
            $linksPerCityLabels[] = $city->city ?? ('City ' . ($cid ?? 'N/A'));
            $linksPerCityValues[] = (int) ($row->links ?? 0);
            $faults = (int) ($faultsPerCityRaw[$cid]->faults ?? 0);
            $coverageGapValues[] = $row->links > 0 ? round($faults / $row->links, 2) : 0;
        }

        $recentFaultsQuery = Fault::with(['city','suburb'])
            ->leftJoin('links', 'faults.link_id', '=', 'links.id')
            ->leftJoin('customers', 'faults.customer_id', '=', 'customers.id')
            ->leftJoin('statuses','faults.status_id','=','statuses.id')
            ->leftjoin('users as assigned_users','faults.assignedTo','=','assigned_users.id')
            ->leftjoin('users as assessed_users','faults.assessed_by','=','assessed_users.id')
			->leftjoin('users as reported_users','faults.user_id','=','reported_users.id')
            ->select('faults.*',
             'links.service_type',
            'links.capacity', 
            'customers.customer',
            'statuses.status_code as status',
            'assigned_users.name as assignedTo',
            'reported_users.name as reportedBy',
            'assessed_users.name as assessedBy'
            )

            ->orderByDesc('created_at')
            ->limit(10)
            ;
        if ($hasDateRange || $hasQuarter) {
            $recentFaultsQuery->whereBetween('faults.created_at', [$currentStart, $currentEnd]);
        } elseif ($selectedMonth !== null && $selectedYear === null) {
            $recentFaultsQuery->whereMonth('faults.created_at', $selectedMonth);
        } elseif ($selectedYear !== null || $selectedMonth !== null) {
            if ($selectedYear !== null) $recentFaultsQuery->whereYear('faults.created_at', $selectedYear);
            if ($selectedMonth !== null) $recentFaultsQuery->whereMonth('faults.created_at', $selectedMonth);
        }
        if ($selectedRegion) {
            $recentFaultsQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }
        if ($impact === 'direct') $recentFaultsQuery->whereNull('faults.root_fault_id');
        elseif ($impact === 'pop') $recentFaultsQuery->whereNotNull('faults.root_fault_id');
        $recentFaults = $recentFaultsQuery->get();


        return response()
            ->view('dashboard.reports', [
            'period' => $period,
            'availableYears' => $availableYears,
            'availableRegions' => $availableRegions,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedRegion' => $selectedRegion,
            'selectedQuarter' => $selectedQuarter,
            'selectedImpact' => $impact,
            'startDate' => $startDateInput,
            'endDate' => $endDateInput,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'periodLabelText' => $periodLabelText,
            'faultsThisMonth' => $faultsThisMonth,
            'faultsLastMonth' => $faultsLastMonth,
            'customersThisMonth' => $customersThisMonth,
            'customersLastMonth' => $customersLastMonth,
            'mttrThisMonth' => (int) $mttrThisMonth,
            'mttrLastMonth' => (int) $mttrLastMonth,
            'slaCompliance' => $slaCompliance,
            'mttaThisMonth' => (int) $mttaThisMonth,
            'mttaLastMonth' => (int) $mttaLastMonth,
            'monthlyLabels' => $monthlyLabels,
            'monthlyCounts' => $monthlyCounts,
            'statusLabels' => $statusLabels,
            'statusValues' => $statusValues,
            'faultCategoryLabels' => $faultCategoryLabels,
            'faultCategoryValues' => $faultCategoryValues,
            'faultCategoryTotal' => (int) $faultCategoryTotal,
            'directFaultCount' => (int) $directFaultCount,
            'popImpactedCount' => (int) $popImpactedCount,
            'faultCategoryMonthlyLabels' => $faultCategoryMonthlyLabels,
            'faultCategoryMonthlyDirect' => $faultCategoryMonthlyDirect,
            'faultCategoryMonthlyPop' => $faultCategoryMonthlyPop,
            'rfoLabels' => $rfoLabels,
            'rfoValues' => $rfoValues,
            'suspectedRfoLabels' => $suspectedRfoLabels,
            'suspectedRfoValues' => $suspectedRfoValues,
            'rfoMonthlyLabels' => $rfoMonthlyLabels,
            'rfoMonthlyCounts' => $rfoMonthlyCounts,
            'faultTypeLabels' => $faultTypeLabels,
            'priorityMatrix' => $priorityMatrix,
            'serviceTypeLabels' => $serviceTypeLabels,
            'serviceTypeValues' => $serviceTypeValues,
            'cityFaultsLabels' => $cityFaultsLabels,
            'cityFaultsValues' => $cityFaultsValues,
            'amLabels' => $amLabels,
            'amFaultsValues' => $amFaultsValues,
            'amMttrValues' => $amMttrValues,
            'workloadLabels' => $workloadLabels,
            'workloadValues' => $workloadValues,
            'linkLabels' => $linkLabels,
            'linkValues' => $linkValues,
            'slaPriorityLabels' => $slaPriorityLabels,
            'slaPriorityValues' => $slaPriorityValues,
            'stageBottlenecksLabels' => $stageBottlenecksLabels,
            'stageBottlenecksValues' => $stageBottlenecksValues,
            'reopenRate' => $reopenRate,
            'sectionWorkloadLabels' => $sectionWorkloadLabels,
            'sectionWorkloadValues' => $sectionWorkloadValues,
            'techLoadLabels' => $techLoadLabels,
            'techLoadOpen' => $techLoadOpen,
            'techLoadResolved' => $techLoadResolved,
            'standbyLabels' => $standbyLabels,
            'standbyValues' => $standbyValues,
            'regionalPerfLabels' => $regionalPerfLabels,
            'regionalPerfValues' => $regionalPerfValues,
            'portfolioRows' => $portfolioRows,
            'churnRows' => $churnRows,
            'linkStatusLabels' => $linkStatusLabels,
            'linkStatusValues' => $linkStatusValues,
            'linkServiceTypeLabels' => $linkServiceTypeLabels,
            'linkServiceTypeValues' => $linkServiceTypeValues,
            'linkCapacityLabels' => $linkCapacityLabels,
            'linkCapacityValues' => $linkCapacityValues,
            'linksMonthlyLabels' => $linksMonthlyLabels,
            'linksMonthlyCreated' => $linksMonthlyCreated,
            'linksMonthlyJcc' => $linksMonthlyJcc,
            'linkHealthLabels' => $linkHealthLabels,
            'linkHealthValues' => $linkHealthValues,
            'linksPerCityLabels' => $linksPerCityLabels,
            'linksPerCityValues' => $linksPerCityValues,
            'coverageGapValues' => $coverageGapValues,
            'recentFaults' => $recentFaults,
        ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function customerRootCauses(Request $request)
    {
        $customerId = (int) $request->input('customer_id');
        if ($customerId <= 0) {
            return response()->json(['message' => 'customer_id is required'], 422);
        }

        $now = Carbon::now();
        $yearInput = $request->input('year');
        $monthInput = $request->input('month');
        $selectedYear = ($request->has('year') && $yearInput !== '' && strtolower((string) $yearInput) !== 'all') ? (int) $yearInput : null;
        $selectedMonth = ($request->has('month') && $monthInput !== '' && strtolower((string) $monthInput) !== 'all') ? (int) $monthInput : null;
        $selectedQuarterInput = $request->input('quarter');
        $selectedQuarter = $selectedQuarterInput !== null && $selectedQuarterInput !== '' ? (int) $selectedQuarterInput : null;
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $selectedRegionRaw = trim((string) $request->input('region', ''));
        $selectedRegion = $selectedRegionRaw === '' ? null : $selectedRegionRaw;

        $hasQuarter = $selectedQuarter !== null;
        $hasDateRange = ($startDateInput !== null && $startDateInput !== '') || ($endDateInput !== null && $endDateInput !== '');
        if ($selectedMonth !== null && $selectedYear === null && !$hasQuarter && !$hasDateRange) {
            $selectedYear = (int) $now->year;
        }
        if ($hasQuarter && $selectedYear === null && !$hasDateRange) {
            $selectedYear = (int) $now->year;
        }

        $allTime = ($selectedYear === null && $selectedMonth === null && !$hasQuarter && !$hasDateRange);
        $currentStart = $now->copy()->startOfMonth();
        $currentEnd = $now->copy()->endOfMonth();

        if (!$hasQuarter && !$hasDateRange && ($selectedYear !== null || $selectedMonth !== null)) {
            if ($selectedYear !== null && $selectedMonth !== null) {
                $currentStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
                $currentEnd = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            } elseif ($selectedYear !== null && $selectedMonth === null) {
                $currentStart = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $currentEnd = Carbon::create($selectedYear, 12, 31)->endOfYear();
            }
        }

        if ($hasQuarter) {
            $quarterYear = $selectedYear !== null ? $selectedYear : (int) $now->year;
            $startMonth = ($selectedQuarter - 1) * 3 + 1;
            $currentStart = Carbon::create($quarterYear, $startMonth, 1)->startOfQuarter();
            $currentEnd = Carbon::create($quarterYear, $startMonth, 1)->endOfQuarter();
        } elseif ($hasDateRange) {
            $rangeStart = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : Carbon::create(2000, 1, 1)->startOfDay();
            $rangeEnd = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : $now->copy()->endOfDay();
            if ($rangeEnd->lessThan($rangeStart)) {
                $tmp = $rangeStart;
                $rangeStart = $rangeEnd;
                $rangeEnd = $tmp;
            }
            $currentStart = $rangeStart;
            $currentEnd = $rangeEnd;
        }

        $baseQuery = Fault::query()->where('customer_id', $customerId);
        if (!$allTime) {
            $baseQuery->whereBetween('faults.created_at', [$currentStart, $currentEnd]);
        }
        if ($selectedRegion) {
            $baseQuery->whereHas('city', function ($q) use ($selectedRegion) {
                $q->where('region', $selectedRegion);
            });
        }

        $totalFaults = (clone $baseQuery)->count();

        $breakdown = (clone $baseQuery)
            ->leftJoin('reasons_for_outages as r', 'faults.confirmedRfo_id', '=', 'r.id')
            ->selectRaw('COALESCE(r.RFO, CASE WHEN faults.confirmedRfo_id IS NULL THEN "Unspecified" ELSE CONCAT("RFO ", faults.confirmedRfo_id) END) as label, COUNT(*) as c')
            ->groupBy('label')
            ->orderByDesc('c')
            ->get();

        $labels = $breakdown->pluck('label')->toArray();
        $values = $breakdown->pluck('c')->map(fn ($x) => (int) $x)->toArray();

        $customerName = Customer::find($customerId)?->customer ?? ('Customer ' . $customerId);

        return response()->json([
            'customer_id' => $customerId,
            'customer' => $customerName,
            'period' => [
                'start' => $allTime ? null : $currentStart->toDateString(),
                'end' => $allTime ? null : $currentEnd->toDateString(),
            ],
            'total_faults' => $totalFaults,
            'rfo_labels' => $labels,
            'rfo_values' => $values,
        ]);
    }
}
