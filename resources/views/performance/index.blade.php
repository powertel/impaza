@extends('layouts.admin')

@section('title')
Performance Dashboard
@endsection

@section('content')
<style>
    @media print {
        .main-sidebar, .main-header, .content-header, .no-print {
            display: none !important;
        }
        .content-wrapper, .main-footer {
            margin-left: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
    }
</style>
<div class="container-fluid">
    <!-- Page Heading & Filters -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 no-print">
        <h1 class="h3 mb-0 text-gray-800">Performance Dashboard</h1>
        
        <div class="d-flex align-items-center">
            <!-- Date Filter Form -->
            <form action="{{ route('performance.index') }}" method="GET" class="form-inline mr-3">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" placeholder="Start Date">
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" placeholder="End Date">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>

            <!-- Export Button -->
            <button onclick="window.print()" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </button>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row">
        <!-- Total Users Assigned -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users Assigned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsersAssigned }}</div>
                            <div class="text-xs text-muted">Technicians with faults</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing User -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Top Performer (User)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $topUser ? $topUser->name : 'N/A' }}</div>
                            <div class="text-xs text-muted">
                                <span class="text-success mr-2"><i class="fas fa-check-circle"></i> {{ $topUser ? $topUser->resolution_rate . '%' : '0%' }}</span> Resolution Rate
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg User Resolution Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg User Resolution</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgUserRate }}%</div>
                            <div class="progress progress-sm mr-2 mt-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $avgUserRate }}%" aria-valuenow="{{ $avgUserRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg User Resolution Time -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg Resolution Time</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgUserTime }} Hours</div>
                            <div class="text-xs text-muted">Per resolved fault</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section & Department KPIs -->
    <div class="row">
        <!-- Top Performing Section -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Top Section</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $topSection ? $topSection->section : 'N/A' }}</div>
                            <div class="text-xs text-muted">
                                <span class="text-primary mr-2"><i class="fas fa-check-circle"></i> {{ $topSection ? $topSection->resolution_rate . '%' : '0%' }}</span> Resolution Rate
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Section Resolution Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Avg Section Resolution</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgSectionRate }}%</div>
                            <div class="progress progress-sm mr-2 mt-2">
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $avgSectionRate }}%" aria-valuenow="{{ $avgSectionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="userPerformanceChart"></canvas>
                    </div>
                    <hr>
                    <div class="text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Resolution Rate
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Section Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="sectionPerformanceChart"></canvas>
                    </div>
                    <hr>
                    <div class="text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Resolution Rate
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">Department Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="departmentPerformanceChart"></canvas>
                    </div>
                    <hr>
                    <div class="text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Resolution Rate
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables Row -->
    <div class="row">
        <!-- User Performance Table -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed User Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="userPerformanceTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Total Assigned</th>
                                    <th>Resolved</th>
                                    <th>Pending</th>
                                    <th>Avg Resolution Time</th>
                                    <th>Resolution Rate</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td class="font-weight-bold">{{ $user->name }}</td>
                                    <td>{{ $user->total_faults }}</td>
                                    <td>{{ $user->resolved_faults }}</td>
                                    <td>{{ $user->pending_faults }}</td>
                                    <td>{{ $user->avg_resolution_time }} Hours</td>
                                    <td style="width: 25%">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 font-weight-bold">{{ $user->resolution_rate }}%</span>
                                            <div class="progress flex-grow-1" style="height: 10px; border-radius: 5px;">
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
        </div>

        <!-- Section Performance Table -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Section Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="sectionPerformanceTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Section</th>
                                    <th>Total Faults</th>
                                    <th>Resolved</th>
                                    <th>Pending</th>
                                    <th>Avg Resolution Time</th>
                                    <th>Resolution Rate</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sections as $section)
                                <tr>
                                    <td class="font-weight-bold">{{ $section->section }}</td>
                                    <td>{{ $section->total_faults }}</td>
                                    <td>{{ $section->resolved_faults }}</td>
                                    <td>{{ $section->pending_faults }}</td>
                                    <td>{{ $section->avg_resolution_time }} Hours</td>
                                    <td style="width: 25%">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 font-weight-bold">{{ $section->resolution_rate }}%</span>
                                            <div class="progress flex-grow-1" style="height: 10px; border-radius: 5px;">
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
        </div>

        <!-- Department Performance Table -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Department Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="departmentPerformanceTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Department</th>
                                    <th>Total Faults</th>
                                    <th>Resolved</th>
                                    <th>Pending</th>
                                    <th>Avg Resolution Time</th>
                                    <th>Resolution Rate</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $dept)
                                <tr>
                                    <td class="font-weight-bold">{{ $dept->department }}</td>
                                    <td>{{ $dept->total_faults }}</td>
                                    <td>{{ $dept->resolved_faults }}</td>
                                    <td>{{ $dept->pending_faults }}</td>
                                    <td>{{ $dept->avg_resolution_time }} Hours</td>
                                    <td style="width: 25%">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 font-weight-bold">{{ $dept->resolution_rate }}%</span>
                                            <div class="progress flex-grow-1" style="height: 10px; border-radius: 5px;">
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
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
                labels: chartData.departmentLabels,
                datasets: [{
                    label: "Resolution Rate",
                    backgroundColor: "#36b9cc",
                    hoverBackgroundColor: "#2c9faf",
                    borderColor: "#36b9cc",
                    data: chartData.departmentRates,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }],
            },
            options: chartOptions
        });
    });
</script>
@endsection