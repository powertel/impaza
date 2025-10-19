@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@include('partials.css')

@section('content')
<section class="content dashboard-page">
  @php
    $periodLabel = ($selectedYear ?? null)
      ? (($selectedMonth ?? null) ? \Carbon\Carbon::create(null, $selectedMonth, 1)->format('F') . ' ' . $selectedYear : (string)$selectedYear)
      : 'All Years';
  @endphp

  <!-- Top filter toolbar -->
  <div class="row mb-3">
    <div class="col">
      <div class="card toolbar-card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <form method="GET" action="{{ route('home') }}" class="d-flex align-items-center" id="dashboardPeriodForm">
            <div class="input-group input-group-sm me-2" style="width:auto;">
              <div class="input-group-prepend"><span class="input-group-text">Month</span></div>
              <select name="month" class="form-select form-select-sm" style="width:auto;" {{ ($selectedYear ?? null) ? '' : 'disabled' }}>
                <option value="">All</option>
                @foreach(($availableMonths ?? []) as $m)
                  <option value="{{ $m }}" {{ ($selectedMonth ?? null) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null,$m,1)->format('F') }}</option>
                @endforeach
              </select>
            </div>
            <div class="input-group input-group-sm me-2" style="width:auto;">
              <div class="input-group-prepend"><span class="input-group-text">Year</span></div>
              <select name="year" class="form-select form-select-sm" style="width:auto;">
                <option value="">All</option>
                @foreach(($availableYears ?? []) as $y)
                  <option value="{{ $y }}" {{ ($selectedYear ?? null) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-filter"></i> Apply</button>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i> Reset</a>
          </form>
          <div class="text-muted small">Showing period: <strong>{{ $periodLabel }}</strong></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Stat cards -->
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Faults</div>
              <div class="h4 mb-0 stat-value">{{ $faultCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-faults">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Customers</div>
              <div class="h4 mb-0 stat-value">{{ $customerCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-customers">
              <i class="fas fa-address-card"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Links</div>
              <div class="h4 mb-0 stat-value">{{ $linkCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-links">
              <i class="fas fa-link"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Period</div>
              <div class="h6 mb-0">{{ $periodLabel }}</div>
            </div>
            <div class="metric-icon icon-open">
              <i class="fas fa-calendar-alt"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- New Metrics Row -->
  <div class="row">
    @can('dashboard-open-faults')
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Open Faults</div>
              <div class="h4 mb-0 stat-value">{{ $openFaultsCount ?? 0 }}</div>
            </div>
            <div class="metric-icon icon-open">
              <i class="fas fa-exclamation-circle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endcan

    @can('dashboard-fault-age')
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Avg Open Age</div>
              <div class="h6 mb-0 stat-value">{{ \Carbon\CarbonInterval::seconds($avgOpenAgeSec ?? 0)->cascade()->forHumans() }}</div>
            </div>
            <div class="metric-icon">
              <i class="fas fa-hourglass-half"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Oldest Open Age</div>
              <div class="h6 mb-0 stat-value">{{ \Carbon\CarbonInterval::seconds($maxOpenAgeSec ?? 0)->cascade()->forHumans() }}</div>
            </div>
            <div class="metric-icon">
              <i class="fas fa-hourglass-end"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endcan

    @can('dashboard-resolution-metrics')
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card stat-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted stat-title">Avg Resolution ({{ $periodLabel }})</div>
              <div class="h6 mb-0 stat-value">{{ \Carbon\CarbonInterval::seconds($avgResolutionSec ?? 0)->cascade()->forHumans() }}</div>
            </div>
            <div class="metric-icon">
              <i class="fas fa-stopwatch"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endcan
  </div>

  <!-- Technician Performance -->
  @can('dashboard-resolution-metrics')
  <div class="row mb-3">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Technician Performance ({{ $periodLabel }})</h3>
        </div>
        <div class="card-body p-2">
          <div class="table-responsive">
            <table class="table table-hover table-sm">
              <thead>
                <tr>
                  <th>Technician</th>
                  <th>Avg Resolution Time</th>
                  <th>Tickets</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($techResolutionAverages ?? []) as $t)
                <tr>
                  <td>{{ $t->name ?? '—' }}</td>
                  <td>{{ \Carbon\CarbonInterval::seconds((int)($t->avg_sec ?? 0))->cascade()->forHumans() }}</td>
                  <td>{{ $t->tickets }}</td>
                </tr>
                @empty
                <tr class="no-data">
                  <td class="text-muted" colspan="3">No resolution data</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endcan

  <!-- Table -->
  @can('dashboard-recent-faults')
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Recent Faults ({{ $periodLabel }})</h3>
         <div class="card-tools">
           <input id="dashboardRecentSearch" type="text" class="form-control form-control-sm rounded-pill d-inline-block w-auto" placeholder="Search">
           <select id="dashboardRecentPageSize" class="form-control form-control-sm rounded-pill d-inline-block w-auto">
            <option value="10">10</option>
           <option value="20" selected>20</option>
           <option value="50">50</option>
             <option value="100">100</option>
          </select>
        </div>
        </div>
        <div class="card-body p-2">
          <div class="table-responsive">

           <table class="table table-hover table-sm js-paginated-table" id="dashboard-recent-faults" data-page-size="10" data-page-size-control="#dashboardRecentPageSize" data-pager="#dashboardRecentPager" data-search="#dashboardRecentSearch">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer</th>
                  <th>Link</th>
                  <th>Created At</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($recentFaults ?? []) as $f)
                  <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->customer }}</td>
                    <td>{{ $f->link }}</td>
                    <td>{{ \Carbon\Carbon::parse($f->created_at)->format('d M Y, h:i A') }}</td>
                  </tr>
                @empty
                  <tr class="no-data">
                    <td class="text-muted" colspan="4">No recent faults</td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                @endforelse
              </tbody>
            </table>
           <div id="dashboardRecentPager" class="mt-2"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endcan
</section>
@endsection



@section('scripts')
@include('partials.scripts')
<script>
  (function(){
    var form = document.getElementById('dashboardPeriodForm');
    if(!form) return;
    var selects = form.querySelectorAll('select');
    selects.forEach(function(sel){
      sel.addEventListener('change', function(){
        form.submit();
      });
    });
    var yearSel = form.querySelector('select[name=year]');
    var monthSel = form.querySelector('select[name=month]');
    function toggleMonth(){
      if(!yearSel || !monthSel) return;
      if(!yearSel.value){
        monthSel.disabled = true;
        monthSel.value = '';
      } else {
        monthSel.disabled = false;
      }
    }
    toggleMonth();
    if(yearSel){ yearSel.addEventListener('change', toggleMonth); }
  })();
</script>
@endsection

