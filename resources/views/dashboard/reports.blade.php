@extends('layouts.admin')

@section('content')
<link href="{{ asset('css/reports.css') }}" rel="stylesheet">
<link href="{{ asset('css/call_centre.css') }}" rel="stylesheet">

<section class="content">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-white border-0 py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
                            <i class="fas fa-chart-line text-primary me-3"></i>
                            Operations Analytics
                        </h3>
                       <!--  <div>
                            <p class="text-sm text-gray-600 mb-0 mt-1">Comprehensive fault management and performance insights</p>
                        </div> -->
                    </div>
                    <!-- <div class="action-dropdown">
                        <button class="btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard.reports') }}"><i class="fas fa-refresh"></i> Reset Filters</a></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print"></i> Export Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download"></i> Download PDF</a></li>
                        </ul>
                    </div> -->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="bg-gray-50 px-4 py-3 border-bottom">
                    <form method="get" action="{{ route('dashboard.reports') }}" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-between gap-3" id="reportsFilterForm">
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
                            <select name="month" class="form-select form-select-sm">
                                <option value="all" {{ ($selectedMonth ?? null) === null ? 'selected' : '' }}>All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ ($selectedMonth ?? null) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-calendar me-1"></i>Year</label>
                            <select name="year" class="form-select form-select-sm">
                                <option value="all" {{ ($selectedYear ?? null) === null ? 'selected' : '' }}>All Years</option>
                                @foreach(($availableYears ?? []) as $y)
                                    <option value="{{ $y }}" {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-clock me-1"></i>Quarter</label>
                            <select name="quarter" class="form-select form-select-sm">
                                <option value="" {{ empty($selectedQuarter ?? '') ? 'selected' : '' }}>All Quarters</option>
                                <option value="1" {{ ($selectedQuarter ?? null) == 1 ? 'selected' : '' }}>Q1</option>
                                <option value="2" {{ ($selectedQuarter ?? null) == 2 ? 'selected' : '' }}>Q2</option>
                                <option value="3" {{ ($selectedQuarter ?? null) == 3 ? 'selected' : '' }}>Q3</option>
                                <option value="4" {{ ($selectedQuarter ?? null) == 4 ? 'selected' : '' }}>Q4</option>
                            </select>
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Region</label>
                            <select name="region" class="form-select form-select-sm">
                                <option value="" {{ empty($selectedRegion ?? '') ? 'selected' : '' }}>All Regions</option>
                                @foreach(($availableRegions ?? []) as $r)
                                    <option value="{{ $r }}" {{ (($selectedRegion ?? '') === $r) ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-play-circle me-1"></i>Start Date</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate ?? request('start_date') }}">
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-stop-circle me-1"></i>End Date</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate ?? request('end_date') }}">
                        </div>
                        <div class="cc-filter-actions ms-auto">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                                <i class="fas fa-filter me-1"></i>
                                Apply
                            </button>
                            <a href="{{ route('dashboard.reports') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="fas fa-undo me-1"></i>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
                    <div class="kpi-grid">
                        <div class="kpi-card kpi-secondary">
                            <div class="kpi-icon">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-label">Reporting Period</div>
                                <div class="kpi-value">
                                    {{ ($periodStart ?? now())->format('d M Y') }} — {{ ($periodEnd ?? now())->format('d M Y') }}
                                </div>
                                <div class="kpi-trend">
                                    <span class="trend-period">
                                        {{ $periodLabelText ?? 'Current period' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="kpi-card kpi-primary">
                            <div class="kpi-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value">{{ number_format($faultsThisMonth) }}</div>
                                <div class="kpi-label">New Faults</div>
                                <div class="kpi-trend">
                                    <span class="trend-period">Current period</span>
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
                                    <span class="trend-period">Current period</span>
                                </div>
                            </div>
                        </div>
                
                        <div class="kpi-card kpi-warning">
                            <div class="kpi-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value">{{ number_format($mttrThisMonth / 3600, 1) }}h</div>
                                <div class="kpi-label">Avg MTTR</div>
                                <div class="kpi-trend">
                                    <span class="trend-period">Current period</span>
                                </div>
                            </div>
                        </div>
                
                        <!-- <div class="kpi-card kpi-danger">
                            <div class="kpi-icon">
                                <i class="fas fa-redo"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value">{{ $reopenRate }}%</div>
                                <div class="kpi-label">Reopen Rate</div>
                                <div class="kpi-trend">
                                    <span class="trend-neutral">
                                        <i class="fas fa-info-circle"></i>
                                        Quality Metric
                                    </span>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Service Reliability Trends</h2>
                    <div class="w-full mb-8">
                        <div class="chart-card chart-large cc-chart-card">
                            <div class="chart-header">
                                <h3>Fault Volume Trend (12 Months)</h3>
                            </div>
                            <div class="chart-body">
                                <canvas id="chartMonthlyFaults"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Root Cause & Impact Analysis</h2>
                    <div class="charts-grid-secondary">
                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <h3>Top Root Causes (Confirmed RFO)</h3>
                            </div>
                            <div class="chart-body chart-body-lg">
                                <canvas id="chartRFO"></canvas>
                            </div>
                        </div>

                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <h3>Geographic Distribution (By Region)</h3>
                            </div>
                            <div class="chart-body">
                                <canvas id="chartCityFaults"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <div class="tables-section">
                        <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Key Accounts Status</h2>
                        
                        <div class="tables-grid">
                            <div class="data-table-card">
                                <div class="table-header">
                                    <h3>Top Impacted Customers</h3>
                                    <div class="table-actions"></div>
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
                                                    <tr class="clickable-row js-customer-rootcause" data-customer-id="{{ $row['customer_id'] ?? 0 }}">
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
                                                    <tr><td colspan="4" class="no-data">No data available</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="data-table-card">
                                <div class="table-header">
                                    <h3>Churn Risk (Increasing Faults)</h3>
                                    <div class="risk-indicator high">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Attention Needed
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
                                                    <tr class="clickable-row js-customer-rootcause" data-customer-id="{{ $row['customer_id'] ?? 0 }}">
                                                        <td>
                                                            <div class="customer-info">
                                                                <div class="customer-avatar">{{ substr($row['customer'], 0, 2) }}</div>
                                                                <span>{{ $row['customer'] }}</span>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-danger">+{{ $row['delta'] }} Faults</span></td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="2" class="no-data">No high-risk customers detected</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<div class="modal fade" id="customerRootCauseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerRootCauseTitle">Root Causes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="customerRootCauseMeta" class="text-muted small mb-2"></div>
                <div id="customerRootCauseLoading" class="py-3">Loading...</div>
                <div id="customerRootCauseBody" style="display:none;">
                    <div style="height: 420px;">
                        <canvas id="customerRootCauseChart"></canvas>
                    </div>
                    <div id="customerRootCauseList" class="mt-3"></div>
                </div>
                <div id="customerRootCauseError" class="text-danger py-2" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data payload for charts -->
<div id="reportsData" style="display:none"
     data-monthly-labels='@json($monthlyLabels)'
     data-monthly-counts='@json($monthlyCounts)'
     data-rfo-labels='@json($rfoLabels)'
     data-rfo-values='@json($rfoValues)'
     data-city-faults-labels='@json($cityFaultsLabels)'
     data-city-faults-values='@json($cityFaultsValues)'></div>

<div id="reportsMeta" style="display:none" data-customer-rootcause-url="{{ route('dashboard.reports.customer-root-causes') }}"></div>

@push('scripts')
<script src="{{ asset('js/reports.js') }}"></script>
@endpush
@endsection




