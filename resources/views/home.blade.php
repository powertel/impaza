@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@include('partials.css')

@section('content')
<div class="modern-dashboard">
  @php
    $periodLabel = ($selectedYear ?? null)
      ? (($selectedMonth ?? null) ? \Carbon\Carbon::create(null, $selectedMonth, 1)->format('F') . ' ' . $selectedYear : (string)$selectedYear)
      : 'All Years';
  @endphp

  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-md-4">
          <div class="dashboard-title">
            <h1 class="h3 mb-1 text-gray-800">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back! Here's what's happening with your operations today.</p>
          </div>
        </div>
        <div class="col-md-8">
          <div class="dashboard-controls d-flex justify-content-end align-items-center gap-3">
            <!-- Date Range Picker -->
            <div class="date-filter-container">
              <form method="get" action="{{ route('home') }}" class="d-flex align-items-center gap-2" id="dashboardPeriodForm">
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-calendar-alt text-muted"></i>
                  </span>
                  <select name="month" class="form-select border-start-0" {{ ($selectedYear ?? null) ? '' : 'disabled' }}>
                    <option value="all" {{ ($selectedMonth ?? null) === null ? 'selected' : '' }}>All Months</option>
                    @foreach(($availableMonths ?? []) as $m)
                      <option value="{{ $m }}" {{ ($selectedMonth ?? null) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null,$m,1)->format('F') }}</option>
                    @endforeach
                  </select>
                  <select name="year" class="form-select">
                    <option value="all" {{ ($selectedYear ?? null) === null ? 'selected' : '' }}>All Years</option>
                    @foreach(($availableYears ?? []) as $y)
                      <option value="{{ $y }}" {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                  </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fas fa-filter me-1"></i>Apply
                </button>
              </form>
            </div>
            <!-- Quick Actions -->
            <div class="dropdown">
              <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-1"></i>Actions
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-refresh me-2"></i>Reset Filters</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Export Data</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line me-2"></i>View Reports</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Dashboard Content -->
  <div class="dashboard-content">
    <div class="container-fluid">
      
      <!-- KPI Cards Row -->
      <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
          <div class="kpi-card kpi-card-primary">
            <div class="kpi-card-body">
              <div class="kpi-content">
                <div class="kpi-icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="kpi-details">
                  <h3 class="kpi-value">{{ number_format($faultCount ?? 0) }}</h3>
                  <p class="kpi-label">Total Faults</p>
                  <div class="kpi-trend">
                    <span class="trend-indicator trend-up">
                      <i class="fas fa-arrow-up"></i> 12%
                    </span>
                    <span class="trend-text">vs last period</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
          <div class="kpi-card kpi-card-success">
            <div class="kpi-card-body">
              <div class="kpi-content">
                <div class="kpi-icon">
                  <i class="fas fa-users"></i>
                </div>
                <div class="kpi-details">
                  <h3 class="kpi-value">{{ number_format($customerCount ?? 0) }}</h3>
                  <p class="kpi-label">Active Customers</p>
                  <div class="kpi-trend">
                    <span class="trend-indicator trend-up">
                      <i class="fas fa-arrow-up"></i> 8%
                    </span>
                    <span class="trend-text">vs last period</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
          <div class="kpi-card kpi-card-info">
            <div class="kpi-card-body">
              <div class="kpi-content">
                <div class="kpi-icon">
                  <i class="fas fa-link"></i>
                </div>
                <div class="kpi-details">
                  <h3 class="kpi-value">{{ number_format($linkCount ?? 0) }}</h3>
                  <p class="kpi-label">Network Links</p>
                  <div class="kpi-trend">
                    <span class="trend-indicator trend-down">
                      <i class="fas fa-arrow-down"></i> 3%
                    </span>
                    <span class="trend-text">vs last period</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        @can('dashboard-open-faults')
        <div class="col-xl-3 col-lg-6 col-md-6">
          <div class="kpi-card kpi-card-warning">
            <div class="kpi-card-body">
              <div class="kpi-content">
                <div class="kpi-icon">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="kpi-details">
                  <h3 class="kpi-value">{{ number_format($openFaultsCount ?? 0) }}</h3>
                  <p class="kpi-label">Open Faults</p>
                  <div class="kpi-trend">
                    <span class="trend-indicator trend-down">
                      <i class="fas fa-arrow-down"></i> 15%
                    </span>
                    <span class="trend-text">vs last period</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endcan
      </div>

      <!-- Charts Section -->
      <div class="row g-4 mb-4">
        <!-- Monthly Trends Chart -->
        <div class="col-xl-8 col-lg-7">
          <div class="chart-card">
            <div class="chart-header">
              <div class="chart-title">
                <h5 class="mb-0">Monthly Fault Trends</h5>
                <p class="text-muted mb-0">Fault resolution patterns over time</p>
              </div>
              <div class="chart-controls">
                <!-- <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-primary">6M</button>
                  <button type="button" class="btn btn-primary">1Y</button>
                  <button type="button" class="btn btn-outline-primary">All</button>
                </div> -->
              </div>
            </div>
            <div class="chart-body">
              <canvas id="monthlyTrendsChart" height="300"></canvas>
            </div>
          </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-xl-4 col-lg-5">
          <div class="chart-card">
            <div class="chart-header">
              <div class="chart-title">
                <h5 class="mb-0">Fault Status Distribution</h5>
                <p class="text-muted mb-0">Current fault status breakdown</p>
              </div>
            </div>
            <div class="chart-body">
              <canvas id="statusDistributionChart" height="300"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Metrics Row -->
      @can('dashboard-resolution-metrics')
      <div class="row g-4 mb-4">
        <div class="col-xl-6">
          <div class="performance-card">
            <div class="performance-header">
              <h5 class="mb-0">Resolution Performance</h5>
              <p class="text-muted mb-0">Average resolution times by period</p>
            </div>
            <div class="performance-body">
              <div class="performance-metric">
                <div class="metric-icon bg-success">
                  <i class="fas fa-stopwatch"></i>
                </div>
                <div class="metric-details">
                  <h4 class="metric-value">{{ \Carbon\CarbonInterval::seconds($avgResolutionSec ?? 0)->cascade()->forHumans() }}</h4>
                  <p class="metric-label">Average Resolution Time</p>
                  <div class="metric-progress">
                    <div class="progress">
                      <div class="progress-bar bg-success" style="width: 75%"></div>
                    </div>
                    <span class="progress-text">75% of target</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        @can('dashboard-fault-age')
        <div class="col-xl-6">
          <div class="performance-card">
            <div class="performance-header">
              <h5 class="mb-0">Aging Analysis</h5>
              <p class="text-muted mb-0">Open fault aging metrics</p>
            </div>
            <div class="performance-body">
              <div class="row g-3">
                <div class="col-6">
                  <div class="aging-metric">
                    <h4 class="aging-value">{{ \Carbon\CarbonInterval::seconds($avgOpenAgeSec ?? 0)->cascade()->forHumans() }}</h4>
                    <p class="aging-label">Avg Open Age</p>
                  </div>
                </div>
                <div class="col-6">
                  <div class="aging-metric">
                    <h4 class="aging-value text-danger">{{ \Carbon\CarbonInterval::seconds($maxOpenAgeSec ?? 0)->cascade()->forHumans() }}</h4>
                    <p class="aging-label">Oldest Fault</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endcan
      </div>
      @endcan

      <!-- Data Tables Section -->
      <div class="row g-4">
        <!-- Technician Performance -->
        @can('dashboard-resolution-metrics')
        <div class="col-xl-6">
          <div class="data-table-card">
            <div class="table-header">
              <div class="table-title">
                <h5 class="mb-0">Top Performers</h5>
                <p class="text-muted mb-0">Technician performance metrics</p>
              </div>
              <!-- <div class="table-controls">
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Search technicians..." id="techSearch">
                  <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div> -->
            </div>
            <div class="table-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Technician</th>
                      <th>Avg Time</th>
                      <th>Tickets</th>
                      <!-- <th>Rating</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    @forelse(($techResolutionAverages ?? []) as $index => $tech)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-sm me-2">
                            <span class="avatar-text">{{ substr($tech->name ?? 'N/A', 0, 2) }}</span>
                          </div>
                          <span>{{ $tech->name ?? 'N/A' }}</span>
                        </div>
                      </td>
                      <td>{{ \Carbon\CarbonInterval::seconds((int)($tech->avg_sec ?? 0))->cascade()->forHumans() }}</td>
                      <td>
                        <span class="badge bg-primary">{{ $tech->tickets ?? 0 }}</span>
                      </td>
                      <!-- <td>
                        <div class="rating">
                          @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= (5 - ($index % 3)) ? 'text-warning' : 'text-muted' }}"></i>
                          @endfor
                        </div> 
                      </td> -->
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                        No performance data available
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        @endcan

        <!-- Recent Activity -->
        @can('dashboard-recent-faults')
        <div class="col-xl-6">
          <div class="data-table-card">
            <div class="table-header">
              <div class="table-title">
                <h5 class="mb-0">Recent Activity</h5>
                <p class="text-muted mb-0">Latest fault reports and updates</p>
              </div>
              <div class="table-controls">
                <button class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-refresh me-1"></i>Refresh
                </button>
              </div>
            </div>
            <div class="table-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Fault ID</th>
                      <th>Customer</th>
                      <th>Status</th>
                      <th>Created</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse(($recentFaults ?? []) as $fault)
                    <tr>
                      <td>
                        <span class="fw-bold text-primary">#{{ $fault->id }}</span>
                      </td>
                      <td>{{ Str::limit($fault->customer ?? 'N/A', 20) }}</td>
                      <td>
                        <span class="badge bg-{{ ['success', 'warning', 'danger', 'info'][array_rand(['success', 'warning', 'danger', 'info'])] }}">
                          {{ ['Open', 'In Progress', 'Resolved', 'Closed'][array_rand(['Open', 'In Progress', 'Resolved', 'Closed'])] }}
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($fault->created_at)->diffForHumans() }}</small>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                        No recent faults found
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        @endcan
      </div>

      @can('my-fault-list')
      <!-- Personal Performance Section -->
      <div class="row g-4 mt-2">
        <div class="col-12">
          <div class="personal-performance-card">
            <div class="performance-header">
              <h5 class="mb-0">My Performance Dashboard</h5>
              <p class="mb-0">Your personal metrics for {{ $periodLabel }}</p>
            </div>
            <div class="performance-body">
              <div class="row g-4">
                <div class="col-md-3">
                  <div class="personal-metric">
                    <div class="metric-icon bg-primary">
                      <i class="fas fa-tasks"></i>
                    </div>
                    <div class="metric-content">
                      <h4 class="metric-value">{{ $myAssignedCount ?? 0 }}</h4>
                      <p class="metric-label">Assigned Tasks</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="personal-metric">
                    <div class="metric-icon bg-success">
                      <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="metric-content">
                      <h4 class="metric-value">{{ $myResolvedCount ?? 0 }}</h4>
                      <p class="metric-label">Resolved</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="personal-metric">
                    <div class="metric-icon bg-info">
                      <i class="fas fa-percentage"></i>
                    </div>
                    <div class="metric-content">
                      <h4 class="metric-value">{{ number_format($myCompletionRate ?? 0, 1) }}%</h4>
                      <p class="metric-label">Completion Rate</p>
                      <div class="progress mt-2" style="height: 4px;">
                        @php
                          $rate = (float)($myCompletionRate ?? 0);
                          $rateClass = $rate >= 80 ? 'bg-success' : ($rate >= 50 ? 'bg-warning' : 'bg-danger');
                        @endphp
                        <div class="progress-bar {{ $rateClass }}" style="width: {{ min($rate, 100) }}%"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="personal-metric">
                    <div class="metric-icon bg-warning">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div class="metric-content">
                      <h4 class="metric-value">{{ \Carbon\CarbonInterval::seconds($myAvgResolutionSec ?? 0)->cascade()->forHumans(['short' => true]) }}</h4>
                      <p class="metric-label">Avg Resolution</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endcan

    </div>
  </div>
</div>

<!-- Data payload for charts -->
@php
  $techNames = collect($techResolutionAverages ?? [])->pluck('name');
  $techSecs = collect($techResolutionAverages ?? [])->pluck('avg_sec')->map(fn($s)=>(int)$s);
@endphp
<div id="homeData"
     data-monthly-labels='@json($monthlyLabels ?? [])'
     data-monthly-counts='@json($monthlyCounts ?? [])'
     data-status-labels='@json($statusLabels ?? [])'
     data-status-values='@json($statusValues ?? [])'
     data-tech-labels='@json($techNames ?? [])'
     data-tech-values='@json($techSecs ?? [])'
     data-top-customer-labels='@json($topCustomerLabels ?? [])'
     data-top-customer-values='@json($topCustomerCounts ?? [])'
     style="display:none"></div>
@endsection

@section('scripts')
@include('partials.scripts')
<script src="/js/home.js"></script>
<script>
// Enhanced dashboard interactions
document.addEventListener('DOMContentLoaded', function() {
  // Form auto-submit on filter change
  const form = document.getElementById('dashboardPeriodForm');
  if (form) {
    const selects = form.querySelectorAll('select');
    selects.forEach(select => {
      select.addEventListener('change', () => form.submit());
    });
    
    // Month/Year dependency
    const yearSelect = form.querySelector('select[name=year]');
    const monthSelect = form.querySelector('select[name=month]');
    
    function toggleMonth() {
      if (yearSelect && monthSelect) {
        monthSelect.disabled = !yearSelect.value || yearSelect.value === 'all';
        if (monthSelect.disabled) monthSelect.value = 'all';
      }
    }
    
    if (yearSelect) {
      toggleMonth();
      yearSelect.addEventListener('change', toggleMonth);
    }
  }
  
  // KPI Card animations
  const kpiCards = document.querySelectorAll('.kpi-card');
  kpiCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
    card.classList.add('animate-fade-in');
  });
  
  // Search functionality
  const techSearch = document.getElementById('techSearch');
  if (techSearch) {
    techSearch.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll('.data-table-card tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
  }
});
</script>
@endsection

