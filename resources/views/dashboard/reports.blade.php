@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="mb-0">Operations Overview</h2>
            <small class="text-muted">Current fault summary and activity</small>
        </div>
        <div class="d-flex gap-2">
            <form method="get" action="{{ route('dashboard.reports') }}" class="d-inline-flex align-items-center">
                <select name="period" class="form-select form-select-sm me-2" style="width:auto">
                    <option value="this_month" {{ ($period ?? 'this_month')==='this_month'?'selected':'' }}>This Month</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
            </form>
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">Export</button>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm card-hover kpi-card h-100">
                <div class="card-body">
                    @php
                        $faultsDeltaRaw = ($faultsLastMonth > 0) ? (($faultsThisMonth - $faultsLastMonth) / $faultsLastMonth) * 100 : 0;
                        $faultsDelta = round($faultsDeltaRaw, 1);
                        $faultsDirection = $faultsDelta >= 0 ? 'up' : 'down';
                    @endphp
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Total Faults</div>
                            <div class="stat-value">{{ number_format($faultsThisMonth) }}</div>
                            <div class="stat-sub">Last month: {{ number_format($faultsLastMonth) }}</div>
                        </div>
                        <div class="stat-right">
                            <div class="stat-icon"><i class="fas fa-bug"></i></div>
                            <div class="stat-delta {{ $faultsDirection }}">{{ $faultsDelta >= 0 ? '+' : '' }}{{ $faultsDelta }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm card-hover kpi-card h-100">
                <div class="card-body">
                    @php
                        $customersDeltaRaw = ($customersLastMonth > 0) ? (($customersThisMonth - $customersLastMonth) / $customersLastMonth) * 100 : 0;
                        $customersDelta = round($customersDeltaRaw, 1);
                        $customersDirection = $customersDelta >= 0 ? 'up' : 'down';
                    @endphp
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">New Customers</div>
                            <div class="stat-value">{{ number_format($customersThisMonth) }}</div>
                            <div class="stat-sub">Last month: {{ number_format($customersLastMonth) }}</div>
                        </div>
                        <div class="stat-right">
                            <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                            <div class="stat-delta {{ $customersDirection }}">{{ $customersDelta >= 0 ? '+' : '' }}{{ $customersDelta }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm card-hover kpi-card h-100">
                <div class="card-body">
                    @php
                        $mttrDeltaRaw = ($mttrLastMonth > 0) ? (($mttrThisMonth - $mttrLastMonth) / $mttrLastMonth) * 100 : 0;
                        $mttrDelta = round($mttrDeltaRaw, 1);
                        // For MTTR, a negative delta is improvement (green)
                        $mttrDirection = $mttrDeltaRaw <= 0 ? 'up' : 'down';
                    @endphp
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Avg MTTR</div>
                            <div class="stat-value">{{ gmdate('H\h i\m', $mttrThisMonth) }}</div>
                            <div class="stat-sub">Last month: {{ gmdate('H\h i\m', $mttrLastMonth) }}</div>
                        </div>
                        <div class="stat-right">
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                            <div class="stat-delta {{ $mttrDirection }}">{{ $mttrDelta >= 0 ? '+' : '' }}{{ $mttrDelta }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm card-hover kpi-card h-100">
                <div class="card-body">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">SLA Compliance</div>
                            <div class="stat-value">{{ $slaCompliance }}%</div>
                            <div class="stat-sub">Target: &lt; 24h</div>
                        </div>
                        <div class="stat-right">
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-delta neutral">—</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm card-hover kpi-card h-100">
                <div class="card-body">
                    @php
                        $mttaDeltaRaw = ($mttaLastMonth > 0) ? (($mttaThisMonth - $mttaLastMonth) / $mttaLastMonth) * 100 : 0;
                        $mttaDelta = round($mttaDeltaRaw, 1);
                        // MTTA improvement is negative delta
                        $mttaDirection = $mttaDeltaRaw <= 0 ? 'up' : 'down';
                    @endphp
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Avg MTTA</div>
                            <div class="stat-value">{{ gmdate('H\h i\m', $mttaThisMonth) }}</div>
                            <div class="stat-sub">Last month: {{ gmdate('H\h i\m', $mttaLastMonth) }}</div>
                        </div>
                        <div class="stat-right">
                            <div class="stat-icon"><i class="fas fa-stopwatch"></i></div>
                            <div class="stat-delta {{ $mttaDirection }}">{{ $mttaDelta >= 0 ? '+' : '' }}{{ $mttaDelta }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm card-hover kpi-card h-100">
                <div class="card-body">
                    <div class="stat-card">
                        <div>
                            <div class="stat-title">Reopen Rate</div>
                            <div class="stat-value">{{ $reopenRate }}%</div>
                            <div class="stat-sub">Reopened vs created (this month)</div>
                        </div>
                        <div class="stat-right">
                            <div class="stat-icon"><i class="fas fa-redo"></i></div>
                            <div class="stat-delta neutral">—</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Performance Overview</div>
                <div class="card-body">
                    <canvas id="chartMonthlyFaults" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">SLA Overview</div>
                <div class="card-body">
                    <canvas id="chartSLA" height="180"></canvas>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <div class="text-muted">Number of Stages</div>
                            <div class="h6 mb-0">{{ number_format($faultsThisMonth) }}</div>
                        </div>
                        <div>
                            <div class="text-muted">Avg MTTR</div>
                            <div class="h6 mb-0">{{ gmdate('H\h i\m', $mttrThisMonth) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Faults by Status</div>
                <div class="card-body">
                    <canvas id="chartStatus" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">RFO Distribution</div>
                <div class="card-body">
                    <canvas id="chartRFO" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Suspected RFO Distribution</div>
                <div class="card-body">
                    <canvas id="chartSuspectedRFO" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">RFO Trend (Monthly)</div>
                <div class="card-body">
                    <canvas id="chartRFOMonthly" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Priority × Fault Type</div>
                <div class="card-body">
                    <canvas id="chartPriorityHeat" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Customer Impact (Count)</div>
                <div class="card-body">
                    <canvas id="chartCustomerCount" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Customer Impact (Duration)</div>
                <div class="card-body">
                    <canvas id="chartCustomerDuration" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Service Impact by Type</div>
                <div class="card-body">
                    <canvas id="chartServiceType" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Faults by City</div>
                <div class="card-body">
                    <canvas id="chartCityFaults" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Account Manager Faults</div>
                <div class="card-body">
                    <canvas id="chartAMFaults" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Account Manager MTTR</div>
                <div class="card-body">
                    <canvas id="chartAMMttr" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">SLA by Priority</div>
                <div class="card-body">
                    <canvas id="chartSLAPriority" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Stage Bottlenecks</div>
                <div class="card-body">
                    <canvas id="chartStageBottlenecks" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Workload by Section</div>
                <div class="card-body">
                    <canvas id="chartSectionWorkload" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Technician Load (Open vs Resolved)</div>
                <div class="card-body">
                    <canvas id="chartTechLoad" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Standby Effectiveness</div>
                <div class="card-body">
                    <canvas id="chartStandby" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Regional Performance</div>
                <div class="card-body">
                    <canvas id="chartRegionalPerf" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio & Churn tables -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Portfolio Summary</div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Links</th>
                                <th>Open Faults</th>
                                <th>Recent RFOs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($portfolioRows as $row)
                                <tr>
                                    <td>{{ $row['customer'] }}</td>
                                    <td>{{ $row['links'] }}</td>
                                    <td>{{ $row['open_faults'] }}</td>
                                    <td>{{ $row['recent_rfos'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No portfolio data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Churn Risk (MoM Increase)</div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Faults Δ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($churnRows as $row)
                                <tr>
                                    <td>{{ $row['customer'] }}</td>
                                    <td>{{ $row['delta'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">No churn signals</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm ">
                <div class="card-header bg-white">Link Status</div>
                <div class="card-body">
                    <canvas id="chartLinkStatus" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Link Service Type</div>
                <div class="card-body">
                    <canvas id="chartLinkServiceType" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Link Capacity</div>
                <div class="card-body">
                    <canvas id="chartLinkCapacity" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Activation Pipeline</div>
                <div class="card-body">
                    <canvas id="chartActivation" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Link Health (Repeated Faults)</div>
                <div class="card-body">
                    <canvas id="chartLinkHealth" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Links per City</div>
                <div class="card-body">
                    <canvas id="chartLinksPerCity" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Coverage Gap (Faults per Link)</div>
                <div class="card-body">
                    <canvas id="chartCoverageGap" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Technician Workload (Open)</div>
                <div class="card-body">
                    <canvas id="chartWorkload" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Link Inventory by Type</div>
                <div class="card-body">
                    <canvas id="chartLinkInventory" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent faults table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">Recent Faults</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fault Ref</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentFaults as $f)
                        <tr>
                            <td>{{ $f->fault_ref_number }}</td>
                            <td>{{ $f->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $f->customer }}</td>
                            <td>{{ $f->city?->city ?? '—' }}</td>
                            <td>{{ $f->status_id }}</td>
                            <td>{{ $f->priorityLevel ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No recent faults</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="reportsData" style="display:none"
     data-monthly-labels='@json($monthlyLabels)'
     data-monthly-counts='@json($monthlyCounts)'
     data-status-labels='@json($statusLabels)'
     data-status-values='@json($statusValues)'
     data-rfo-labels='@json($rfoLabels)'
     data-rfo-values='@json($rfoValues)'
     data-suspected-rfo-labels='@json($suspectedRfoLabels)'
     data-suspected-rfo-values='@json($suspectedRfoValues)'
     data-rfo-monthly-labels='@json($rfoMonthlyLabels)'
     data-rfo-monthly-counts='@json($rfoMonthlyCounts)'
     data-workload-labels='@json($workloadLabels)'
     data-workload-values='@json($workloadValues)'
     data-link-labels='@json($linkLabels)'
     data-link-values='@json($linkValues)'
     data-sla-compliance='@json($slaCompliance)'
     data-fault-type-labels='@json($faultTypeLabels)'
     data-priority-matrix='@json($priorityMatrix)'
     data-customer-impact-count-labels='@json($customerImpactCountLabels ?? [])'
     data-customer-impact-count-values='@json($customerImpactCountValues ?? [])'
     data-customer-impact-duration-labels='@json($customerImpactDurationLabels ?? [])'
     data-customer-impact-duration-values='@json($customerImpactDurationValues ?? [])'
     data-service-type-labels='@json($serviceTypeLabels)'
     data-service-type-values='@json($serviceTypeValues)'
     data-city-faults-labels='@json($cityFaultsLabels)'
     data-city-faults-values='@json($cityFaultsValues)'
     data-am-labels='@json($amLabels)'
     data-am-faults-values='@json($amFaultsValues)'
     data-am-mttr-values='@json($amMttrValues)'
     data-sla-priority-labels='@json($slaPriorityLabels)'
     data-sla-priority-values='@json($slaPriorityValues)'
     data-stage-bottlenecks-labels='@json($stageBottlenecksLabels)'
     data-stage-bottlenecks-values='@json($stageBottlenecksValues)'
     data-section-workload-labels='@json($sectionWorkloadLabels)'
     data-section-workload-values='@json($sectionWorkloadValues)'
     data-tech-load-labels='@json($techLoadLabels)'
     data-tech-load-open='@json($techLoadOpen)'
     data-tech-load-resolved='@json($techLoadResolved)'
     data-standby-labels='@json($standbyLabels)'
     data-standby-values='@json($standbyValues)'
     data-regional-perf-labels='@json($regionalPerfLabels)'
     data-regional-perf-values='@json($regionalPerfValues)'
     data-link-status-labels='@json($linkStatusLabels)'
     data-link-status-values='@json($linkStatusValues)'
     data-link-service-type-labels='@json($linkServiceTypeLabels)'
     data-link-service-type-values='@json($linkServiceTypeValues)'
     data-link-capacity-labels='@json($linkCapacityLabels)'
     data-link-capacity-values='@json($linkCapacityValues)'
     data-links-monthly-labels='@json($linksMonthlyLabels)'
     data-links-monthly-created='@json($linksMonthlyCreated)'
     data-links-monthly-jcc='@json($linksMonthlyJcc)'
     data-link-health-labels='@json($linkHealthLabels)'
     data-link-health-values='@json($linkHealthValues)'
     data-links-per-city-labels='@json($linksPerCityLabels)'
     data-links-per-city-values='@json($linksPerCityValues)'
     data-coverage-gap-values='@json($coverageGapValues)'
     data-mtta-this-month='@json($mttaThisMonth)'
     data-mtta-last-month='@json($mttaLastMonth)'
     data-reopen-rate='@json($reopenRate)'></div>
@endsection