@extends('layouts.admin')

@section('title')
Performance Dashboard
@endsection

@section('content')
<link href="{{ asset('css/call_centre.css') }}" rel="stylesheet">
<section class="content">
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-white border-0 py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
                        <i class="fas fa-tachometer-alt text-primary me-3"></i>
                        Performance Dashboard
                    </h3>
                    <p class="text-sm text-gray-600 mb-0 mt-1 me-3">User, Section, and Department Performance Metrics</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="tooltip" title="Export Report">
                        <i class="fas fa-download me-1"></i>
                        Export Report
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Filter Section -->
            <div class="bg-gray-50 px-4 py-3 border-bottom no-print">
                <form action="{{ route('performance.index') }}" method="GET" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-start gap-3">
                    <div class="cc-field">
                        <label class="form-label"><i class="fas fa-sliders-h me-1"></i>Time Period</label>
                        <select name="filter" id="filterType" class="form-control form-control-sm" style="min-width: 120px;">
                            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Yearly</option>
                            <option value="quarter" {{ $filter == 'quarter' ? 'selected' : '' }}>Quarterly</option>
                            <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <div class="cc-field filter-group" id="monthFilter">
                        <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
                        <select name="month" class="form-control form-control-sm">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="cc-field filter-group" id="quarterFilter" style="display:none;">
                        <label class="form-label"><i class="far fa-clock me-1"></i>Quarter</label>
                        <select name="quarter" class="form-control form-control-sm">
                            <option value="1" {{ $selectedQuarter == 1 ? 'selected' : '' }}>Q1</option>
                            <option value="2" {{ $selectedQuarter == 2 ? 'selected' : '' }}>Q2</option>
                            <option value="3" {{ $selectedQuarter == 3 ? 'selected' : '' }}>Q3</option>
                            <option value="4" {{ $selectedQuarter == 4 ? 'selected' : '' }}>Q4</option>
                        </select>
                    </div>

                    <div class="cc-field filter-group" id="yearFilter">
                        <label class="form-label"><i class="far fa-calendar me-1"></i>Year</label>
                        <select name="year" class="form-control form-control-sm">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="cc-field">
                        <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Region</label>
                        <select name="region" class="form-control form-control-sm" style="min-width: 140px;">
                            <option value="">All Regions</option>
                            @foreach($availableRegions as $region)
                                <option value="{{ $region }}" {{ $selectedRegion == $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="cc-field filter-group" id="dateRangeFilter" style="display:none;">
                        <label class="form-label"><i class="far fa-play-circle me-1"></i>Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="cc-field filter-group" id="dateRangeFilterEnd" style="display:none;">
                        <label class="form-label"><i class="far fa-stop-circle me-1"></i>End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>

                    <div class="cc-filter-actions ms-auto">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="fas fa-filter me-1"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fas fa-undo me-1"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- KPI Cards -->
            <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
                <div class="row g-4 mb-4">
                    <!-- Total Users Assigned -->
                    <div class="col-md-3">
                        <div class="cc-kpi cc-kpi--slate h-100">
                            <div class="cc-kpi-head">
                                <div class="cc-kpi-icon"><i class="fas fa-users"></i></div>
                                <div class="cc-kpi-title">Total Users Assigned</div>
                            </div>
                            <div class="cc-kpi-value">{{ $totalUsersAssigned }}</div>
                            <div class="cc-kpi-sub">Technicians with faults</div>
                        </div>
                    </div>

                    <!-- Top Performing User -->
                    <div class="col-md-3">
                        <div class="cc-kpi cc-kpi--green h-100">
                            <div class="cc-kpi-head">
                                <div class="cc-kpi-icon"><i class="fas fa-user-check"></i></div>
                                <div class="cc-kpi-title">Top Performer (User)</div>
                            </div>
                            <div class="cc-kpi-value">{{ $topUser ? $topUser->name : 'N/A' }}</div>
                            <div class="cc-kpi-sub">
                                <span class="text-success mr-2"><i class="fas fa-check-circle"></i> {{ $topUser ? $topUser->resolution_rate . '%' : '0%' }}</span> Resolution Rate
                            </div>
                        </div>
                    </div>

                    <!-- Avg User Resolution Rate -->
                    <div class="col-md-3">
                        <div class="cc-kpi cc-kpi--blue h-100">
                            <div class="cc-kpi-head">
                                <div class="cc-kpi-icon"><i class="fas fa-chart-line"></i></div>
                                <div class="cc-kpi-title">Avg User Resolution</div>
                            </div>
                            <div class="cc-kpi-value">{{ $avgUserRate }}%</div>
                            <div class="progress progress-sm mt-2" style="height: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $avgUserRate }}%" aria-valuenow="{{ $avgUserRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Avg Resolution Time -->
                    <div class="col-md-3">
                        <div class="cc-kpi cc-kpi--indigo h-100">
                            <div class="cc-kpi-head">
                                <div class="cc-kpi-icon"><i class="fas fa-clock"></i></div>
                                <div class="cc-kpi-title">Avg Resolution Time</div>
                            </div>
                            <div class="cc-kpi-value">{{ $avgUserTime }} Hours</div>
                            <div class="cc-kpi-sub">Per resolved fault</div>
                        </div>
                    </div>
                </div>

                <!-- Section & Department KPIs Row 2 -->
                <div class="row g-4 mb-4">
                    <!-- Top Performing Section -->
                    <div class="col-md-6">
                        <div class="cc-kpi cc-kpi--purple h-100" style="border-left: 4px solid #6f42c1;">
                            <div class="cc-kpi-head">
                                <div class="cc-kpi-icon"><i class="fas fa-building"></i></div>
                                <div class="cc-kpi-title">Top Section</div>
                            </div>
                            <div class="cc-kpi-value">{{ $topSection ? $topSection->section : 'N/A' }}</div>
                            <div class="cc-kpi-sub">
                                <span class="text-primary mr-2"><i class="fas fa-check-circle"></i> {{ $topSection ? $topSection->resolution_rate . '%' : '0%' }}</span> Resolution Rate
                            </div>
                        </div>
                    </div>

                    <!-- Avg Section Resolution Rate -->
                    <div class="col-md-6">
                        <div class="cc-kpi cc-kpi--teal h-100" style="border-left: 4px solid #20c997;">
                            <div class="cc-kpi-head">
                                <div class="cc-kpi-icon"><i class="fas fa-chart-pie"></i></div>
                                <div class="cc-kpi-title">Avg Section Resolution</div>
                            </div>
                            <div class="cc-kpi-value">{{ $avgSectionRate }}%</div>
                            <div class="progress progress-sm mt-2" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $avgSectionRate }}%" aria-valuenow="{{ $avgSectionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="px-4 pb-4">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="cc-chart-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="fw-semibold text-primary">User Performance Overview</div>
                            </div>
                            <div class="chart-bar">
                                <canvas id="userPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="cc-chart-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="fw-semibold text-success">Section Performance Overview</div>
                            </div>
                            <div class="chart-bar">
                                <canvas id="sectionPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="cc-chart-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="fw-semibold text-info">Department Performance Overview</div>
                            </div>
                            <div class="chart-bar">
                                <canvas id="departmentPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Tables -->
            <div class="px-4 pb-4">
                <!-- User Performance Table -->
                <div class="card border-0 shadow-sm cc-analysis-card mb-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <div class="fw-semibold text-primary">Detailed User Performance</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 cc-analysis-table" id="userPerformanceTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>User</th>
                                        <th class="text-center">Total Assigned</th>
                                        <th class="text-center">Resolved</th>
                                        <th class="text-center">Pending</th>
                                        <th class="text-center">Avg Resolution Time</th>
                                        <th>Resolution Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="font-weight-bold">{{ $user->name }}</td>
                                        <td class="text-center"><span class="text-primary font-weight-bold" style="font-size: 1.1em;">{{ $user->total_faults }}</span></td>
                                        <td class="text-center"><span class="text-success font-weight-bold" style="font-size: 1.1em;">{{ $user->resolved_faults }}</span></td>
                                        <td class="text-center"><span class="text-danger font-weight-bold" style="font-size: 1.1em;">{{ $user->pending_faults }}</span></td>
                                        <td class="text-center">{{ $user->avg_resolution_time }} Hours</td>
                                        <td style="width: 25%">
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2 font-weight-bold">{{ $user->resolution_rate }}%</span>
                                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                                                    <div class="progress-bar bg-{{ $user->resolution_rate >= 80 ? 'success' : ($user->resolution_rate >= 50 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $user->resolution_rate }}%" aria-valuenow="{{ $user->resolution_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->resolution_rate >= 80)
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-star"></i> Excellent</span>
                                            @elseif($user->resolution_rate >= 50)
                                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-check"></i> Good</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle"></i> Poor</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section Performance Table -->
                <div class="card border-0 shadow-sm cc-analysis-card mb-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <div class="fw-semibold text-success">Detailed Section Performance</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 cc-analysis-table" id="sectionPerformanceTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Section</th>
                                        <th class="text-center">Total Faults</th>
                                        <th class="text-center">Resolved</th>
                                        <th class="text-center">Pending</th>
                                        <th class="text-center">Avg Resolution Time</th>
                                        <th>Resolution Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sections as $section)
                                    <tr>
                                        <td class="font-weight-bold">{{ $section->section }}</td>
                                        <td class="text-center"><span class="text-primary font-weight-bold" style="font-size: 1.1em;">{{ $section->total_faults }}</span></td>
                                        <td class="text-center"><span class="text-success font-weight-bold" style="font-size: 1.1em;">{{ $section->resolved_faults }}</span></td>
                                        <td class="text-center"><span class="text-danger font-weight-bold" style="font-size: 1.1em;">{{ $section->pending_faults }}</span></td>
                                        <td class="text-center">{{ $section->avg_resolution_time }} Hours</td>
                                        <td style="width: 25%">
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2 font-weight-bold">{{ $section->resolution_rate }}%</span>
                                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                                                    <div class="progress-bar bg-{{ $section->resolution_rate >= 80 ? 'success' : ($section->resolution_rate >= 50 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $section->resolution_rate }}%" aria-valuenow="{{ $section->resolution_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($section->resolution_rate >= 80)
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-star"></i> Excellent</span>
                                            @elseif($section->resolution_rate >= 50)
                                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-check"></i> Good</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle"></i> Poor</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Department Performance Table -->
                <div class="card border-0 shadow-sm cc-analysis-card">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <div class="fw-semibold text-info">Detailed Department Performance</div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 cc-analysis-table" id="departmentPerformanceTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Department</th>
                                        <th class="text-center">Total Faults</th>
                                        <th class="text-center">Resolved</th>
                                        <th class="text-center">Pending</th>
                                        <th class="text-center">Avg Resolution Time</th>
                                        <th>Resolution Rate</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($departments as $dept)
                                    <tr>
                                        <td class="font-weight-bold">{{ $dept->department }}</td>
                                        <td class="text-center"><span class="text-primary font-weight-bold" style="font-size: 1.1em;">{{ $dept->total_faults }}</span></td>
                                        <td class="text-center"><span class="text-success font-weight-bold" style="font-size: 1.1em;">{{ $dept->resolved_faults }}</span></td>
                                        <td class="text-center"><span class="text-danger font-weight-bold" style="font-size: 1.1em;">{{ $dept->pending_faults }}</span></td>
                                        <td class="text-center">{{ $dept->avg_resolution_time }} Hours</td>
                                        <td style="width: 25%">
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2 font-weight-bold">{{ $dept->resolution_rate }}%</span>
                                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                                                    <div class="progress-bar bg-{{ $dept->resolution_rate >= 80 ? 'success' : ($dept->resolution_rate >= 50 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $dept->resolution_rate }}%" aria-valuenow="{{ $dept->resolution_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($dept->resolution_rate >= 80)
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-star"></i> Excellent</span>
                                            @elseif($dept->resolution_rate >= 50)
                                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-check"></i> Good</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle"></i> Poor</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @media print {
        .main-sidebar, .main-header, .content-header, .no-print, .cc-filter-bar, .btn {
            display: none !important;
        }
        .content-wrapper, .main-footer {
            margin-left: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .container-fluid, .content {
            padding: 0 !important;
        }
    }
    /* Additional custom styles to match call_centre if needed */
    .cc-chart-card {
        height: 100%;
        background: #fff;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Filter Logic
        function toggleFilters() {
            var filter = $('#filterType').val();
            
            // Hide all first
            $('#monthFilter, #quarterFilter, #yearFilter, #dateRangeFilter, #dateRangeFilterEnd').hide();
            
            if (filter === 'month') {
                $('#monthFilter, #yearFilter').show();
            } else if (filter === 'year') {
                $('#yearFilter').show();
            } else if (filter === 'quarter') {
                $('#quarterFilter, #yearFilter').show();
            } else if (filter === 'weekly') {
                $('#dateRangeFilter, #dateRangeFilterEnd').show();
            }
        }

        $('#filterType').change(toggleFilters);
        toggleFilters(); // Run on load

        // Initialize DataTables with specific settings for modern look
        var tableOptions = {
            "responsive": true,
            "autoWidth": false,
            "order": [[ 4, "desc" ]],
            "pageLength": 10,
            "language": {
                "search": "Quick Search:",
                "lengthMenu": "Show _MENU_ entries"
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        };

        $('#userPerformanceTable').DataTable(tableOptions);
        $('#sectionPerformanceTable').DataTable(tableOptions);
        $('#departmentPerformanceTable').DataTable(tableOptions);

        // Initialize Charts
        var chartData = @json($chartData);

        // Common Chart Options for clean look
        var chartOptions = {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6,
                        fontFamily: "Nunito, -apple-system,system-ui,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif"
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: 100,
                        maxTicksLimit: 5,
                        padding: 10,
                        fontFamily: "Nunito, -apple-system,system-ui,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif",
                        callback: function(value, index, values) {
                            return value + '%';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel + '%';
                    }
                }
            }
        };

        // User Performance Chart
        var ctxUser = document.getElementById("userPerformanceChart");
        var userPerformanceChart = new Chart(ctxUser, {
            type: 'bar',
            data: {
                labels: chartData.userLabels,
                datasets: [{
                    label: "Resolution Rate",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: chartData.userRates,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }],
            },
            options: chartOptions
        });

        // Section Performance Chart
        var ctxSection = document.getElementById("sectionPerformanceChart");
        var sectionPerformanceChart = new Chart(ctxSection, {
            type: 'bar',
            data: {
                labels: chartData.sectionLabels,
                datasets: [{
                    label: "Resolution Rate",
                    backgroundColor: "#1cc88a",
                    hoverBackgroundColor: "#17a673",
                    borderColor: "#1cc88a",
                    data: chartData.sectionRates,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }],
            },
            options: chartOptions
        });

        // Department Performance Chart
        var ctxDept = document.getElementById("departmentPerformanceChart");
        var departmentPerformanceChart = new Chart(ctxDept, {
            type: 'bar',
            data: {
                labels: chartData.deptLabels,
                datasets: [{
                    label: "Resolution Rate",
                    backgroundColor: "#36b9cc",
                    hoverBackgroundColor: "#2c9faf",
                    borderColor: "#36b9cc",
                    data: chartData.deptRates,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }],
            },
            options: chartOptions
        });
    });
</script>
@endsection