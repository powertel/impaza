@extends('layouts.admin')

@section('content')
<link href="{{ asset('css/reports.css') }}" rel="stylesheet">
<link href="{{ asset('css/call_centre.css') }}" rel="stylesheet">

<div class="modern-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header bg-gradient-to-r from-gray-50 to-white">
        <div class="dashboard-title-section">
            <h1 class="dashboard-title me-auto">Operations Analytics</h1>
            <p class="dashboard-subtitle">Comprehensive fault management and performance insights</p>
        </div>
        
        <div class="dashboard-controls">
            <form method="get" action="{{ route('dashboard.reports') }}" class="filter-form" id="reportsFilterForm">
                <div class="filter-group">
                    <label for="month">Period</label>
                    <select name="month" id="month" class="filter-select">
                        <option value="all" {{ ($selectedMonth ?? null) === null ? 'selected' : '' }}>All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($selectedMonth ?? null) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="filter-select">
                        <option value="all" {{ ($selectedYear ?? null) === null ? 'selected' : '' }}>All Years</option>
                        @foreach(($availableYears ?? []) as $y)
                            <option value="{{ $y }}" {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </form>
            
            <div class="action-dropdown">
                <button class="btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('dashboard.reports') }}"><i class="fas fa-refresh"></i> Reset Filters</a></li>
                    <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print"></i> Export Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-download"></i> Download PDF</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Primary KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value">{{ number_format($faultsThisMonth) }}</div>
                <div class="kpi-label">Total Faults</div>
                <div class="kpi-trend">
                    @php
                        $faultsDeltaRaw = ($faultsLastMonth > 0) ? (($faultsThisMonth - $faultsLastMonth) / $faultsLastMonth) * 100 : 0;
                        $faultsDelta = round($faultsDeltaRaw, 1);
                        $faultsDirection = $faultsDelta >= 0 ? 'up' : 'down';
                    @endphp
                    <span class="trend-{{ $faultsDirection }}">
                        <i class="fas fa-arrow-{{ $faultsDirection }}"></i>
                        {{ abs($faultsDelta) }}%
                    </span>
                    <span class="trend-period">vs last month</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-success">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value">{{ number_format($customersThisMonth) }}</div>
                <div class="kpi-label">Active Customers</div>
                <div class="kpi-trend">
                    @php
                        $customersDeltaRaw = ($customersLastMonth > 0) ? (($customersThisMonth - $customersLastMonth) / $customersLastMonth) * 100 : 0;
                        $customersDelta = round($customersDeltaRaw, 1);
                        $customersDirection = $customersDelta >= 0 ? 'up' : 'down';
                    @endphp
                    <span class="trend-{{ $customersDirection }}">
                        <i class="fas fa-arrow-{{ $customersDirection }}"></i>
                        {{ abs($customersDelta) }}%
                    </span>
                    <span class="trend-period">vs last month</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-warning">
            <div class="kpi-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value">{{ gmdate('H\h i\m', $mttrThisMonth) }}</div>
                <div class="kpi-label">Avg MTTR</div>
                <div class="kpi-trend">
                    @php
                        $mttrDeltaRaw = ($mttrLastMonth > 0) ? (($mttrThisMonth - $mttrLastMonth) / $mttrLastMonth) * 100 : 0;
                        $mttrDelta = round($mttrDeltaRaw, 1);
                        $mttrDirection = $mttrDeltaRaw <= 0 ? 'up' : 'down';
                    @endphp
                    <span class="trend-{{ $mttrDirection }}">
                        <i class="fas fa-arrow-{{ $mttrDirection == 'up' ? 'down' : 'up' }}"></i>
                        {{ abs($mttrDelta) }}%
                    </span>
                    <span class="trend-period">vs last month</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-info">
            <div class="kpi-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $slaCompliance }}%</div>
                <div class="kpi-label">SLA Compliance</div>
                <div class="kpi-trend">
                    <span class="trend-neutral">
                        <i class="fas fa-target"></i>
                        Target: &lt; 24h
                    </span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-secondary">
            <div class="kpi-icon">
                <i class="fas fa-stopwatch"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value">{{ gmdate('H\h i\m', $mttaThisMonth) }}</div>
                <div class="kpi-label">Avg MTTA</div>
                <div class="kpi-trend">
                    @php
                        $mttaDeltaRaw = ($mttaLastMonth > 0) ? (($mttaThisMonth - $mttaLastMonth) / $mttaLastMonth) * 100 : 0;
                        $mttaDelta = round($mttaDeltaRaw, 1);
                        $mttaDirection = $mttaDeltaRaw <= 0 ? 'up' : 'down';
                    @endphp
                    <span class="trend-{{ $mttaDirection }}">
                        <i class="fas fa-arrow-{{ $mttaDirection == 'up' ? 'down' : 'up' }}"></i>
                        {{ abs($mttaDelta) }}%
                    </span>
                    <span class="trend-period">vs last month</span>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-danger">
            <div class="kpi-icon">
                <i class="fas fa-redo"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $reopenRate }}%</div>
                <div class="kpi-label">Reopen Rate</div>
                <div class="kpi-trend">
                    <span class="trend-neutral">
                        <i class="fas fa-info-circle"></i>
                        This month
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary Charts Section -->
    <div class="charts-grid">
        <div class="chart-card chart-large cc-chart-card">
            <div class="chart-header">
                <h3>Performance Overview</h3>
                <div class="chart-actions">
                    <button class="chart-action" title="Refresh"><i class="fas fa-sync-alt"></i></button>
                    <button class="chart-action" title="Fullscreen"><i class="fas fa-expand"></i></button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="chartMonthlyFaults"></canvas>
            </div>
        </div>

        <div class="chart-card cc-chart-card">
            <div class="chart-header">
                <h3>SLA Overview</h3>
            </div>
            <div class="chart-body">
                <canvas id="chartSLA"></canvas>
                <div class="chart-stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Stages</span>
                        <span class="stat-value">{{ number_format($faultsThisMonth) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Avg MTTR</span>
                        <span class="stat-value">{{ gmdate('H\h i\m', $mttrThisMonth) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Grid -->
    <div class="charts-grid-secondary">
        <div class="chart-card cc-chart-card">
            <div class="chart-header">
                <h3>Fault Status Distribution</h3>
            </div>
            <div class="chart-body">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>

        <div class="chart-card cc-chart-card">
            <div class="chart-header">
                <h3>RFO Analysis</h3>
            </div>
            <div class="chart-body">
                <canvas id="chartRFO"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>Suspected RFO</h3>
            </div>
            <div class="chart-body">
                <canvas id="chartSuspectedRFO"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>RFO Trends</h3>
            </div>
            <div class="chart-body">
                <canvas id="chartRFOMonthly"></canvas>
            </div>
        </div>
    </div>

    <!-- Advanced Analytics Section -->
    <div class="analytics-section">
        <h2 class="section-title">Advanced Analytics</h2>
        
        <div class="charts-grid-advanced">
        <div class="chart-card chart-wide cc-chart-card">
                <div class="chart-header">
                    <h3>Priority × Fault Type Matrix</h3>
                </div>
                <div class="chart-body">
                    <canvas id="chartPriorityHeat"></canvas>
                </div>
            </div>

            <div class="chart-card cc-chart-card">
                <div class="chart-header">
                    <h3>Customer Impact (Count)</h3>
                </div>
                <div class="chart-body">
                    <canvas id="chartCustomerCount"></canvas>
                </div>
            </div>

            <div class="chart-card cc-chart-card">
                <div class="chart-header">
                    <h3>Customer Impact (Duration)</h3>
                </div>
                <div class="chart-body">
                    <canvas id="chartCustomerDuration"></canvas>
                </div>
            </div>

            <div class="chart-card cc-chart-card">
                <div class="chart-header">
                    <h3>Service Impact by Type</h3>
                </div>
                <div class="chart-body">
                    <canvas id="chartServiceType"></canvas>
                </div>
            </div>

            <div class="chart-card cc-chart-card">
                <div class="chart-header">
                    <h3>Geographic Distribution</h3>
                </div>
                <div class="chart-body">
                    <canvas id="chartCityFaults"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Account Manager Performance</h3>
                </div>
                <div class="chart-body">
                    <canvas id="chartAMFaults"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Section -->
    <div class="performance-section">
        <h2 class="section-title">Performance Metrics</h2>
        
        <div class="performance-grid">
            <div class="performance-card">
                <div class="performance-header">
                    <h3>SLA by Priority</h3>
                    <div class="performance-score">
                        <span class="score-value">{{ $slaCompliance }}%</span>
                        <span class="score-label">Compliance</span>
                    </div>
                </div>
                <div class="performance-body">
                    <canvas id="chartSLAPriority"></canvas>
                </div>
            </div>

            <div class="performance-card">
                <div class="performance-header">
                    <h3>Stage Bottlenecks</h3>
                    <div class="performance-indicator">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="performance-body">
                    <canvas id="chartStageBottlenecks"></canvas>
                </div>
            </div>

            <div class="performance-card">
                <div class="performance-header">
                    <h3>Workload Distribution</h3>
                    <div class="performance-indicator">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
                <div class="performance-body">
                    <canvas id="chartSectionWorkload"></canvas>
                </div>
            </div>

            <div class="performance-card">
                <div class="performance-header">
                    <h3>Technician Load</h3>
                    <div class="performance-indicator">
                        <i class="fas fa-user-cog"></i>
                    </div>
                </div>
                <div class="performance-body">
                    <canvas id="chartTechLoad"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="tables-section">
        <h2 class="section-title">Detailed Analysis</h2>
        
        <div class="tables-grid">
            <div class="data-table-card">
                <div class="table-header">
                    <h3>Portfolio Summary</h3>
                    <div class="table-actions">
                        <!-- <button class="table-action" title="Export"><i class="fas fa-download"></i></button>
                        <button class="table-action" title="Refresh"><i class="fas fa-sync-alt"></i></button> -->
                    </div>
                </div>
                <div class="table-body">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
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
                                        <td>
                                            <div class="customer-info">
                                                <div class="customer-avatar">{{ substr($row['customer'], 0, 2) }}</div>
                                                <span>{{ $row['customer'] }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-info">{{ $row['links'] }}</span></td>
                                        <td><span class="badge badge-warning">{{ $row['open_faults'] }}</span></td>
                                        <td><span class="badge badge-danger">{{ $row['recent_rfos'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="no-data">No portfolio data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="data-table-card">
                <div class="table-header">
                    <h3>Churn Risk Analysis</h3>
                    <div class="risk-indicator high">
                        <i class="fas fa-exclamation-triangle"></i>
                        High Risk
                    </div>
                </div>
                <div class="table-body">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Fault Increase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($churnRows as $row)
                                    <tr>
                                        <td>
                                            <div class="customer-info">
                                                <div class="customer-avatar">{{ substr($row['customer'], 0, 2) }}</div>
                                                <span>{{ $row['customer'] }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-danger">{{ $row['delta'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="no-data">No churn signals detected</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="data-table-card table-full-width">
            <div class="table-header">
                <h3>Recent Fault Activity</h3>
                <!-- <div class="table-search">
                    <input type="text" placeholder="Search faults..." class="search-input">
                    <i class="fas fa-search"></i>
                </div> -->
            </div>
            <div class="table-body">
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Fault Reference</th>
                                <th>Date Created</th>
                                <th>Customer</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFaults as $f)
                                <tr>
                                    <td>
                                        <span class="fault-ref">{{ $f->fault_ref_number }}</span>
                                    </td>
                                    <td>{{ $f->created_at?->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-avatar">{{ substr($f->customer, 0, 2) }}</div>
                                            <span>{{ $f->customer }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $f->city?->city ?? '—' }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($f->status) }}">
                                            {{ $f->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="priority-badge priority-{{ strtolower($f->priorityLevel ?? 'normal') }}">
                                            {{ $f->priorityLevel ?? 'Normal' }}
                                        </span>
                                    </td>


                                </tr>
                            @empty
                                <tr><td colspan="6" class="no-data">No recent faults found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data payload for charts -->
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

<script>
// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('reportsFilterForm');
    const selects = filterForm.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.modern-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // KPI card animations
    const kpiCards = document.querySelectorAll('.kpi-card');
    kpiCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-in');
    });
});
</script>

<script src="{{ asset('js/reports.js') }}"></script>
@endsection