<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Fault;
use App\Models\FaultStageLog;
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

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        // KPI: Faults
        $faultsThisMonth = Fault::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $faultsLastMonth = Fault::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        // KPI: New Customers
        $customersThisMonth = Customer::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $customersLastMonth = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        // KPI: Avg MTTR from stage logs (fallback to 0)
        $mttrThisMonth = FaultStageLog::whereBetween('started_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds') ?? 0;
        $mttrLastMonth = FaultStageLog::whereBetween('started_at', [$lastMonthStart, $lastMonthEnd])
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds') ?? 0;

        // SLA compliance (duration < 24h in stage logs)
        $slaThreshold = 24 * 3600; // 24 hours
        $slaCount = FaultStageLog::whereBetween('started_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('duration_seconds')->count();
        $slaMetCount = FaultStageLog::whereBetween('started_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('duration_seconds')->where('duration_seconds', '<=', $slaThreshold)->count();
        $slaCompliance = $slaCount > 0 ? round(($slaMetCount / $slaCount) * 100, 1) : 0;

        // Faults per past 12 months (labels and counts)
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $from = $now->copy()->subMonths($i)->startOfMonth();
            $to = $now->copy()->subMonths($i)->endOfMonth();
            $label = $from->format('M Y');
            $count = Fault::whereBetween('created_at', [$from, $to])->count();
            $monthlyLabels[] = $label;
            $monthlyCounts[] = $count;
        }

        // Status distribution (join statuses for labels if available)
        $statusBreakdown = Fault::select('status_id', DB::raw('COUNT(*) as c'))
            ->groupBy('status_id')->get();
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

        // RFO distribution (confirmed)
        $rfoBreakdown = Fault::select('confirmedRfo_id', DB::raw('COUNT(*) as c'))
            ->groupBy('confirmedRfo_id')->get();
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
        $suspectedRfoBreakdown = Fault::select('suspectedRfo_id', DB::raw('COUNT(*) as c'))
            ->groupBy('suspectedRfo_id')->get();
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
            $rfoMonthlyCounts[] = Fault::whereBetween('created_at', [$from,$to])
                ->whereNotNull('confirmedRfo_id')->count();
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
        $priorityHeatmapRaw = Fault::select('faultType','priorityLevel', DB::raw('COUNT(*) as c'))
            ->groupBy('faultType','priorityLevel')->get();
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
        $customerImpactCountRaw = Fault::select('customer_id', DB::raw('COUNT(*) as c'))
            ->groupBy('customer_id')->orderByDesc('c')->limit(10)->get();
        $customerImpactCountLabels = [];$customerImpactCountValues = [];
        foreach ($customerImpactCountRaw as $row) {
            $cust = $row->customer_id ? Customer::find($row->customer_id) : null;
            $customerImpactCountLabels[] = $cust->customer ?? ('Customer ' . ($row->customer_id ?? 'N/A'));
            $customerImpactCountValues[] = (int) $row->c;
        }
        $customerImpactDurationRaw = FaultStageLog::join('faults','fault_stage_logs.fault_id','=','faults.id')
            ->select('faults.customer_id', DB::raw('SUM(fault_stage_logs.duration_seconds) as sec'))
            ->groupBy('faults.customer_id')->orderByDesc('sec')->limit(10)->get();
        $customerImpactDurationLabels = [];$customerImpactDurationValues = [];
        foreach ($customerImpactDurationRaw as $row) {
            $cust = $row->customer_id ? Customer::find($row->customer_id) : null;
            $customerImpactDurationLabels[] = $cust->customer ?? ('Customer ' . ($row->customer_id ?? 'N/A'));
            $customerImpactDurationValues[] = (int) ($row->sec ?? 0);
        }

        // Service impact by type
        $serviceTypeBreakdown = Fault::select('serviceType', DB::raw('COUNT(*) as c'))
            ->groupBy('serviceType')->orderByDesc('c')->get();
        $serviceTypeLabels = $serviceTypeBreakdown->pluck('serviceType')->map(fn($x) => $x ?? 'N/A')->toArray();
        $serviceTypeValues = $serviceTypeBreakdown->pluck('c')->map(fn($x) => (int) $x)->toArray();

        // Geography: faults by city
        $cityFaultsRaw = Fault::select('city_id', DB::raw('COUNT(*) as c'))
            ->groupBy('city_id')->orderByDesc('c')->limit(10)->get();
        $cityFaultsLabels = [];$cityFaultsValues = [];
        foreach ($cityFaultsRaw as $row) {
            $city = $row->city_id ? City::find($row->city_id) : null;
            $cityFaultsLabels[] = $city->city ?? ('City ' . ($row->city_id ?? 'N/A'));
            $cityFaultsValues[] = (int) $row->c;
        }

        // Account manager performance
        $amFaultsRaw = Fault::select('accountManager_id', DB::raw('COUNT(*) as c'))
            ->groupBy('accountManager_id')->orderByDesc('c')->limit(10)->get();
        $amLabels = [];$amFaultsValues = [];
        foreach ($amFaultsRaw as $row) {
            $name = DB::table('account_managers')->where('id',$row->accountManager_id)->value('accountManager');
            $amLabels[] = $name ?? ('AM ' . ($row->accountManager_id ?? 'N/A'));
            $amFaultsValues[] = (int) $row->c;
        }
        $amMttrRaw = FaultStageLog::join('faults','fault_stage_logs.fault_id','=','faults.id')
            ->select('faults.accountManager_id', DB::raw('AVG(fault_stage_logs.duration_seconds) as mttr'))
            ->groupBy('faults.accountManager_id')->get();
        $amMttrMap = [];
        foreach ($amMttrRaw as $r) { $amMttrMap[$r->accountManager_id ?? 0] = (int) ($r->mttr ?? 0); }
        $amMttrValues = [];
        foreach ($amFaultsRaw as $row) { $amMttrValues[] = $amMttrMap[$row->accountManager_id ?? 0] ?? 0; }

        // MTTA
        $mttaThisMonth = DB::table('fault_assignments')
            ->join('faults','fault_assignments.fault_id','=','faults.id')
            ->whereBetween('fault_assignments.assigned_at', [$startOfMonth, $endOfMonth])
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;
        $mttaLastMonth = DB::table('fault_assignments')
            ->join('faults','fault_assignments.fault_id','=','faults.id')
            ->whereBetween('fault_assignments.assigned_at', [$lastMonthStart, $lastMonthEnd])
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, faults.created_at, fault_assignments.assigned_at)')) ?? 0;

        // SLA by priority
        $priorityTargets = [ 'P1' => 4*3600, 'P2' => 8*3600, 'P3' => 24*3600, 'P4' => 48*3600 ];
        $sums = FaultStageLog::whereBetween('started_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('duration_seconds')
            ->select('fault_id', DB::raw('SUM(duration_seconds) as total'))
            ->groupBy('fault_id')->get();
        $slaPriorityTotals = [];$slaPriorityMet = [];
        foreach ($sums as $s) {
            $fault = Fault::find($s->fault_id);
            if (!$fault) continue;
            $p = $fault->priorityLevel ?? 'N/A';
            $slaPriorityTotals[$p] = ($slaPriorityTotals[$p] ?? 0) + 1;
            $target = $priorityTargets[$p] ?? (24*3600);
            if (($s->total ?? 0) <= $target) { $slaPriorityMet[$p] = ($slaPriorityMet[$p] ?? 0) + 1; }
        }
        $slaPriorityLabels = array_keys($slaPriorityTotals);
        $slaPriorityValues = [];
        foreach ($slaPriorityLabels as $p) {
            $tot = $slaPriorityTotals[$p] ?? 0; $met = $slaPriorityMet[$p] ?? 0;
            $slaPriorityValues[] = $tot > 0 ? round(($met / $tot) * 100, 1) : 0;
        }

        // Stage bottlenecks
        $stageBottlenecksRaw = FaultStageLog::select('status_id', DB::raw('AVG(duration_seconds) as avg_dur'))
            ->whereNotNull('duration_seconds')->groupBy('status_id')->get();
        $stageBottlenecksLabels = [];$stageBottlenecksValues = [];
        foreach ($stageBottlenecksRaw as $row) {
            $label = 'Status ' . ($row->status_id ?? 'N/A');
            if ($row->status_id) { $s = Status::find($row->status_id); if ($s) $label = $s->description ?? $s->status_code ?? $label; }
            $stageBottlenecksLabels[] = $label;
            $stageBottlenecksValues[] = (int) ($row->avg_dur ?? 0);
        }

        // Reopen rate
        $reopenedFaultIds = FaultStageLog::whereBetween('started_at', [$startOfMonth, $endOfMonth])
            ->select('fault_id','status_id', DB::raw('COUNT(*) as c'))
            ->groupBy('fault_id','status_id')->havingRaw('COUNT(*) > 1')->pluck('fault_id')->unique();
        $reopenRate = $faultsThisMonth > 0 ? round(($reopenedFaultIds->count() / $faultsThisMonth) * 100, 1) : 0;

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
        $linksByCustomer = Link::select('customer_id', DB::raw('COUNT(*) as c'))
            ->groupBy('customer_id')->get()->keyBy('customer_id');
        $openFaultsByCustomer = FaultAssignment::whereNull('resolved_at')
            ->join('faults','fault_assignments.fault_id','=','faults.id')
            ->select('faults.customer_id', DB::raw('COUNT(DISTINCT fault_assignments.fault_id) as c'))
            ->groupBy('faults.customer_id')->get()->keyBy('customer_id');
        $recentRfoByCustomer = Fault::whereNotNull('confirmedRfo_id')
            ->where('created_at','>=',$now->copy()->subDays(90))
            ->select('customer_id', DB::raw('COUNT(*) as c'))
            ->groupBy('customer_id')->get()->keyBy('customer_id');
        $portfolioRows = [];
        foreach ($linksByCustomer as $cid => $row) {
            $cust = $cid ? Customer::find($cid) : null;
            $portfolioRows[] = [
                'customer' => $cust->customer ?? ('Customer ' . ($cid ?? 'N/A')),
                'links' => (int) ($row->c ?? 0),
                'open_faults' => (int) ($openFaultsByCustomer[$cid]->c ?? 0),
                'recent_rfos' => (int) ($recentRfoByCustomer[$cid]->c ?? 0),
            ];
        }
        usort($portfolioRows, fn($a,$b) => ($b['open_faults'] <=> $a['open_faults']));
        $portfolioRows = array_slice($portfolioRows, 0, 10);

        // Churn risk (MoM increase)
        $custFaultsThis = Fault::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select('customer_id', DB::raw('COUNT(*) as c'))->groupBy('customer_id')->get()->keyBy('customer_id');
        $custFaultsLast = Fault::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->select('customer_id', DB::raw('COUNT(*) as c'))->groupBy('customer_id')->get()->keyBy('customer_id');
        $churnRows = [];
        foreach ($custFaultsThis as $cid => $r) {
            $diff = ((int) ($r->c ?? 0)) - ((int) ($custFaultsLast[$cid]->c ?? 0));
            if ($diff > 0) {
                $cust = $cid ? Customer::find($cid) : null;
                $churnRows[] = [ 'customer' => $cust->customer ?? ('Customer ' . ($cid ?? 'N/A')), 'delta' => $diff ];
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
        $linkHealthRaw = Fault::whereNotNull('link_id')
            ->select('link_id', DB::raw('COUNT(*) as c'))
            ->groupBy('link_id')->orderByDesc('c')->limit(10)->get();
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

        // Recent faults
        $recentFaults = Fault::with(['city','suburb'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.reports', [
            'period' => $period,
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
        ]);
    }
}