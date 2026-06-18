@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@include('partials.css')

@section('content')
<link href="{{ asset('css/dashboard.css') }}?v={{ @filemtime(public_path('css/dashboard.css')) }}" rel="stylesheet">
<link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
<style>
  .home-modern {
    --hm-text: #0f172a;
    --hm-muted: #64748b;
    --hm-border: #e6ebf2;
    --hm-shadow-sm: 0 8px 24px rgba(15, 23, 42, 0.08);
    --hm-shadow-md: 0 14px 34px rgba(15, 23, 42, 0.12);
    --hm-shadow-lg: 0 24px 56px rgba(15, 23, 42, 0.16);
  }
  .home-modern .card {
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
  }
  .home-modern .card-header {
    border-bottom: 1px solid var(--hm-border) !important;
    background: linear-gradient(180deg, rgba(255,255,255,.95) 0%, rgba(249,251,255,.95) 100%);
    backdrop-filter: blur(8px);
  }
  .zoom-card { transition: transform .22s cubic-bezier(0.4,0,0.2,1), box-shadow .22s cubic-bezier(0.4,0,0.2,1); }
  .zoom-card:hover { transform: translateY(-3px); box-shadow: var(--hm-shadow-md); }
  .cc-kpi.zoom-card:hover { transform: translateY(-3px); }
  .home-modern .cc-filter-bar { position: sticky; top: 6px; z-index: 20; }
  .home-modern .btn.rounded-pill { border-radius: 999px !important; }
  .home-modern .data-table-card table thead th {
    position: sticky;
    top: 0;
    background: #f8fafc;
    z-index: 2;
  }
  .home-modern .data-table-card .table-body { max-height: 430px; overflow: auto; }
  .home-modern .cc-chart-card,
  .home-modern .personal-performance-card {
    border: 1px solid var(--hm-border);
    box-shadow: var(--hm-shadow-sm);
  }
  .home-modern .cc-chart-card:hover,
  .home-modern .personal-performance-card:hover { box-shadow: var(--hm-shadow-md); }
  .home-modern .table-controls .form-control,
  .home-modern .table-controls .form-select { border-color: #dce3ee; }
  .home-modern .table-controls .form-control:focus,
  .home-modern .table-controls .form-select:focus { box-shadow: 0 0 0 4px rgba(59,130,246,.15); border-color: #93c5fd; }
  .home-modern .badge { letter-spacing: .01em; }
  .home-modern .animate-fade-in { animation: hmFadeUp .45s ease-out both; }
  @keyframes hmFadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  .avatar.avatar-sm .avatar-text { display:inline-block; width:28px; height:28px; border-radius:50%; background:#eef2ff; color:#4f46e5; font-weight:700; line-height:28px; text-align:center; }
  .personal-performance-card { background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 12px 32px rgba(16,24,40,.12); overflow:hidden; color:#111827; }
  .personal-performance-card .performance-header { border-bottom: 1px solid #f1f5f9; }
  .personal-performance-card .performance-header h5 { color:#111827; }
  .personal-performance-card .performance-header p { color:#6b7280; }
  .personal-performance-card .metric-value { color:#111827; }
  .personal-performance-card .metric-label { color:#6b7280; }
  .personal-performance-card .personal-metric { background: transparent; backdrop-filter: none; }
  .personal-performance-card .metric-icon { color:#111827; }
</style>
 
  @php
    $periodLabel = ($selectedYear ?? null)
      ? (($selectedMonth ?? null) ? \Carbon\Carbon::create(null, $selectedMonth, 1)->format('F') . ' ' . $selectedYear : (string)$selectedYear)
      : 'All Years';
  @endphp

  <section class="content ux-unified home-modern">
    <div class="card border-0 shadow-lg">
      <div class="card-header bg-white border-0 py-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
              <i class="fas fa-chart-line text-primary me-2"></i>
              Dashboard
            </h3>
            <!-- <p class="text-sm text-gray-600 mb-0 mt-1">Welcome back! Here's what's happening with your operations today.</p> -->
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="bg-gray-50 px-4 py-3 border-bottom">
          <form method="get" action="{{ route('home') }}" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-between gap-3" id="dashboardPeriodForm">
            <div class="cc-field">
              <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
              <select name="month" class="form-select form-select-sm" {{ ($selectedYear ?? null) ? '' : 'disabled' }}>
                <option value="all" {{ ($selectedMonth ?? null) === null ? 'selected' : '' }}>All Months</option>
                @foreach(($availableMonths ?? []) as $m)
                  <option value="{{ $m }}" {{ ($selectedMonth ?? null) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null,$m,1)->format('F') }}</option>
                @endforeach
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
              <label class="form-label"><i class="fas fa-globe-africa me-1"></i>Region</label>
              <select name="region" class="form-select form-select-sm">
                <option value="all" {{ ($selectedRegion ?? null) === null ? 'selected' : '' }}>All Regions</option>
                @foreach(($availableRegions ?? []) as $r)
                  <option value="{{ $r }}" {{ ($selectedRegion ?? null) === $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
              </select>
            </div>
            <div class="cc-filter-actions ms-auto">
              <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                <i class="fas fa-filter me-1"></i>
                Apply
              </button>
              <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-undo me-1"></i>
                Reset
              </a>
              <button type="button" id="homeHardRefresh" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="fas fa-sync-alt me-1"></i>
                Hard Refresh
              </button>
            </div>
          </form>
        </div>

  <!-- KPI Cards -->
  <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
    <div class="row g-4 mb-4">
      <div class="col-xxl-3 col-lg-3 col-md-3 col-sm-6 col-6">
        <a href="{{ route('faults.index') }}" class="text-decoration-none">
        <div class="cc-kpi cc-kpi--blue cc-kpi--compact h-100 zoom-card">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="cc-kpi-title">Real Faults</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($faultCount ?? 0) }}</div>
          <div class="cc-kpi-sub">Current overview</div>
        </div>
        </a>
      </div>
      <div class="col-xxl-3 col-lg-3 col-md-3 col-sm-6 col-6">
        <a href="{{ route('faults.index', ['status' => (int)($rectificationId ?? 3)]) }}" class="text-decoration-none">
        <div class="cc-kpi cc-kpi--green cc-kpi--compact h-100 zoom-card">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-tasks"></i></div>
            <div class="cc-kpi-title">Under Rectification</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($inProgressFaultsCount ?? 0) }}</div>
          <div class="cc-kpi-sub">Current overview</div>
        </div>
        </a>
      </div>
      <div class="col-xxl-3 col-lg-3 col-md-3 col-sm-6 col-6">
        <a href="{{ route('faults.index', ['status' => (int)($nocClearedId ?? 6)]) }}" class="text-decoration-none">
        <div class="cc-kpi cc-kpi--indigo cc-kpi--compact h-100 zoom-card">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-check-circle"></i></div>
            <div class="cc-kpi-title">Resolved Faults</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($resolvedFaultsCount ?? 0) }}</div>
          <div class="cc-kpi-sub">Current overview</div>
        </div>
        </a>
      </div>
      
      <div class="col-xxl-3 col-lg-3 col-md-3 col-sm-6 col-6">
        <a href="{{ route('faults.index', ['age' => 'today']) }}" class="text-decoration-none">
        <div class="cc-kpi cc-kpi--slate cc-kpi--compact h-100 zoom-card">
          <div class="cc-kpi-head">
            <div class="cc-kpi-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="cc-kpi-title">Real Faults Today</div>
          </div>
          <div class="cc-kpi-value">{{ number_format($todayFaultsCount ?? 0) }}</div>
          <div class="cc-kpi-sub">Current overview</div>
        </div>
        </a>
      </div>
     
    </div>
  </div>

  <div class="px-4 pb-4">
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('faults.index', ['status' => 'lt4']) }}" class="btn btn-sm btn-outline-dark rounded-pill">
        Open <span class="badge bg-dark ms-1">{{ (int)($openFaultsCount ?? 0) }}</span>
      </a>
      <a href="{{ route('faults.index', ['status' => (int)($waitingAssessmentId ?? 1)]) }}" class="btn btn-sm btn-outline-danger rounded-pill">
        Waiting Assessment <span class="badge bg-danger ms-1">{{ (int)($waitingAssessmentCount ?? 0) }}</span>
      </a>
      <a href="{{ route('faults.index', ['status' => (int)($rectificationId ?? 3)]) }}" class="btn btn-sm btn-outline-warning rounded-pill">
        Under Rectification <span class="badge bg-warning text-dark ms-1">{{ (int)($inProgressFaultsCount ?? 0) }}</span>
      </a>
      <a href="{{ route('faults.index', ['status' => (int)($nocClearedId ?? 6)]) }}" class="btn btn-sm btn-outline-success rounded-pill">
        Resolved <span class="badge bg-success ms-1">{{ (int)($resolvedFaultsCount ?? 0) }}</span>
      </a>
    </div>
  </div>

  <!-- Charts Grid -->
  <div class="px-4 pb-4">
    <div class="row g-4 mb-4">
      <div class="col-xl-8 col-lg-7">
        <div class="chart-card cc-chart-card zoom-card h-100">
          <div class="chart-header">
            <div class="chart-title">
              <h5 class="mb-0">Monthly Fault Trends</h5>
              <p class="text-muted mb-0">Fault resolution patterns over time</p>
            </div>
          </div>
          <div class="chart-body">
            <canvas id="monthlyTrendsChart" height="300"></canvas>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-lg-5">
        <div class="chart-card cc-chart-card zoom-card h-100">
          <div class="chart-header">
            <div class="chart-title">
              <h5 class="mb-0">Fault Status Distribution</h5>
              <p class="text-muted mb-0">Current fault status breakdown</p>
            </div>
          </div>
          <div class="chart-body">
            <canvas id="statusDistributionChart" height="300"></canvas>
            <div id="statusDistributionLegend" class="mt-3"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Performance Metrics Row -->
  @can('dashboard-resolution-metrics')
  <div class="px-4 pb-4">
    <div class="row g-4 mb-4">
      <div class="col-xl-6">
        <div class="performance-card cc-chart-card zoom-card">
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
        <div class="performance-card cc-chart-card zoom-card">
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
  </div>
  @endcan

  <!-- Data Tables Section -->
  <div class="px-4 pb-4">
    <div class="row g-4">
      
<!--       <div class="col-xl-6">
        <div class="data-table-card cc-chart-card">
          <div class="table-header">
            <div class="table-title">
              <h5 class="mb-0">Top Performers</h5>
              <p class="text-muted mb-0">Technician performance metrics</p>
            </div>
          </div>
          <div class="table-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Technician</th>
                    <th>Avg Time</th>
                    <th>Tickets</th>
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
                  </tr>
                  @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-4">
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
       -->
     
      <div class="col-xl-12">
        <div class="data-table-card cc-chart-card">
          <div class="table-header">
            <div class="table-title">
              <h5 class="mb-0">Recent Activity</h5>
              <p class="text-muted mb-0">Latest fault reports and updates</p>
            </div>
            <div class="table-controls">

              <!-- <button class="btn btn-sm btn-outline-primary">
                <i class="fas fa-refresh me-1"></i>Refresh
              </button> -->
            </div>
          </div>
          <div class="table-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Ref. No.</th>
                    <th>Customer</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Updated</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse(($recentFaults ?? []) as $fault)
                  @php
                    $rowStatusId = (int)($fault->status_id ?? 0);
                    $rowStatus = trim((string)($fault->status_description ?? ''));
                  @endphp
                  <tr>
                    <td>
                      <a href="{{ route('faults.index', ['q' => $fault->fault_ref_number]) }}" class="fw-bold text-primary text-decoration-none">
                        {{ $fault->fault_ref_number }}
                      </a>
                    </td>
                    <td>{{ Str::limit($fault->customer ?? 'N/A', 20) }}</td>
                    <td class="{{ $fault->assignedTo ? 'fw-bold' : 'text-muted' }}">{{ $fault->assignedTo ?: 'Not yet assigned' }}</td>
                    <td>
                      @php
                        $desc = trim((string)($fault->status_description ?? ''));
                        $statusLabel = $desc !== '' ? $desc : 'Unknown';
                        $color = \App\Models\Status::STATUS_COLOR[$statusLabel] ?? '#6c757d';
                      @endphp
                      <a href="{{ route('faults.index', ['status' => $rowStatusId]) }}" class="text-decoration-none">
                        <span class="badge rounded-pill recent-fault-status" data-status-id="{{ $rowStatusId }}" data-status="{{ $rowStatus }}" style="background-color: {{ $color }}; color: black; padding: 0.4rem 0.65rem; font-weight: 600;">
                          {{ $statusLabel }}
                        </span>
                      </a>
                    </td>
                    <td>
                      <small class="text-muted">{{ \Carbon\Carbon::parse($fault->updated_at)->diffForHumans() }}</small>
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
     
    </div>
  </div>

  <script>
    (function () {
      function norm(v) {
        return String(v || '').toLowerCase().trim();
      }
      function applyRecentFilters() {
        var q = norm(document.getElementById('recentFaultsSearch')?.value);
        var statusVal = String(document.getElementById('recentFaultsStatusFilter')?.value || 'all');
        var rows = document.querySelectorAll('.data-table-card table tbody tr');
        rows.forEach(function (row) {
          var cells = row.querySelectorAll('td');
          if (!cells || cells.length < 5) return;
          var hay = norm(row.innerText);
          var badge = row.querySelector('.recent-fault-status');
          var sid = badge ? String(badge.getAttribute('data-status-id') || '') : '';
          var okQ = q === '' || hay.indexOf(q) !== -1;
          var okS = statusVal === 'all' || sid === statusVal;
          row.style.display = (okQ && okS) ? '' : 'none';
        });
      }
      document.getElementById('recentFaultsSearch')?.addEventListener('input', applyRecentFilters);
      document.getElementById('recentFaultsStatusFilter')?.addEventListener('change', applyRecentFilters);
    })();
  </script>

  @can('my-fault-list')
  <div class="px-4 pb-4">
    <div class="row g-4 mt-2">
      <div class="col-12">
        <div class="personal-performance-card zoom-card">
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
  </div>
  @endcan

  </div>
  </div>
  </section>

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
     data-status-options='@json(($allStatuses ?? collect())->map(fn($s)=>["id"=>$s->id,"description"=>$s->description])->values())'
     data-selected-year='@json($selectedYear ?? null)'
     data-selected-month='@json($selectedMonth ?? null)'
     data-faults-url='{{ route('faults.index') }}'
     data-reports-url='{{ route('dashboard.reports') }}'
     data-tech-labels='@json($techNames ?? [])'
     data-tech-values='@json($techSecs ?? [])'
     data-top-customer-labels='@json($topCustomerLabels ?? [])'
     data-top-customer-values='@json($topCustomerCounts ?? [])'
     style="display:none"></div>
@endsection

@section('scripts')
@include('partials.scripts')
<script src="{{ asset('js/home.js') }}?v={{ file_exists(public_path('js/home.js')) ? filemtime(public_path('js/home.js')) : time() }}"></script>
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
  const kpiCards = document.querySelectorAll('.cc-kpi, .cc-chart-card, .personal-performance-card');
  kpiCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
    card.classList.add('animate-fade-in');
  });

  const hardRefreshBtn = document.getElementById('homeHardRefresh');
  if (hardRefreshBtn) {
    hardRefreshBtn.addEventListener('click', function () {
      const url = new URL(window.location.href);
      url.searchParams.set('_hr', String(Date.now()));
      window.location.replace(url.toString());
    });
  }
  
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

