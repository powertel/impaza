@extends('layouts.admin')

@section('title')
Faults Analytics Dashboard
@endsection

@section('content')
<link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
<section class="content">
  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
            <i class="fas fa-chart-line text-primary me-3"></i>
            Faults Analytics Dashboard
          </h3>
          <p class="text-sm text-gray-600 mb-0 mt-1 me-3">Real-time insights and performance metrics</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span class="badge bg-primary-subtle text-primary fs-7 px-3 py-2 rounded-pill">
            <!-- <i class="fas fa-sync-alt me-1"></i> -->
            <!-- Live Data -->
          </span>
          <!-- <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="tooltip" title="Export Report">
            <i class="fas fa-download me-1"></i>
            Export
          </button> -->
        </div>
      </div>
      </div>
    
    <div class="card-body p-0">
      <!-- Filter Section -->
      <div class="bg-gray-50 px-4 py-3 border-bottom">
        <form method="get" action="{{ route('call_centre.reports') }}" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-between gap-3">
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-sliders-h me-1"></i>Time Period</label>
            <select name="filter" class="form-select form-select-sm" title="Select filter type">
              <option value="month" {{ ($filter ?? 'month') === 'month' ? 'selected' : '' }}>Monthly</option>
              <option value="year" {{ ($filter ?? '') === 'year' ? 'selected' : '' }}>Yearly</option>
              <option value="weekly" {{ ($filter ?? '') === 'weekly' ? 'selected' : '' }}>Weekly Range</option>
              <option value="quarter" {{ ($filter ?? '') === 'quarter' ? 'selected' : '' }}>Quarterly</option>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
            <select name="month" class="form-select form-select-sm" title="Choose month">
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ ($selectedMonth ?? 0) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
              @endfor
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-calendar me-1"></i>Year</label>
            <select name="year" class="form-select form-select-sm" title="Choose year">
              <option value="all" {{ (($filter ?? '') === 'year' && strtolower((string)request('year')) === 'all') ? 'selected' : '' }}>All Years</option>
              @foreach(($availableYears ?? []) as $y)
                <option value="{{ $y }}" {{ ($selectedYear ?? 0) == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-clock me-1"></i>Quarter</label>
            <select name="quarter" class="form-select form-select-sm" title="Choose quarter">
              <option value="1" {{ ($quarter ?? 1) == 1 ? 'selected' : '' }}>Q1</option>
              <option value="2" {{ ($quarter ?? 1) == 2 ? 'selected' : '' }}>Q2</option>
              <option value="3" {{ ($quarter ?? 1) == 3 ? 'selected' : '' }}>Q3</option>
              <option value="4" {{ ($quarter ?? 1) == 4 ? 'selected' : '' }}>Q4</option>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Region</label>
            <select name="region" class="form-select form-select-sm" title="Choose region">
              <option value="" {{ empty($selectedRegion ?? '') ? 'selected' : '' }}>All Regions</option>
              @foreach(($availableRegions ?? []) as $r)
                <option value="{{ $r }}" {{ (($selectedRegion ?? '') === $r) ? 'selected' : '' }}>{{ $r }}</option>
              @endforeach
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-play-circle me-1"></i>Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" title="Start date for weekly range" />
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-stop-circle me-1"></i>End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" title="End date for weekly range" />
          </div>
          <div class="cc-filter-actions ms-auto">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
              <i class="fas fa-filter me-1"></i>
              Apply Filters
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-cc-reset>
              <i class="fas fa-undo me-1"></i>
              Reset
            </button>
            <button type="button" id="callCentreHardRefresh" class="btn btn-outline-dark btn-sm rounded-pill px-3">
              <i class="fas fa-sync-alt me-1"></i>
              Hard Refresh
            </button>
          </div>
        </form>
      </div>

      <!-- KPI Cards -->
      <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--slate h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="far fa-calendar-alt"></i></div>
                <div class="cc-kpi-title">Reporting Period</div>
              </div>
              <div class="cc-kpi-value">{{ ($periodStart ?? now())->format('d M Y') }} — {{ ($periodEnd ?? now())->format('d M Y') }}</div>
              <div class="cc-kpi-sub">{{ $periodLabelText ?? 'Selected period' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--blue h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-bolt"></i></div>
                <div class="cc-kpi-title">New Faults</div>
              </div>
              <div class="cc-kpi-value">{{ number_format($newFaultsTotal ?? 0) }}</div>
              <div class="cc-kpi-sub">{{ $periodLabelText ?? 'Period total' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--green h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="cc-kpi-title">Resolved Faults</div>
              </div>
              <div class="cc-kpi-value">{{ number_format($resolvedTotal ?? 0) }}</div>
              <div class="cc-kpi-sub">{{ $periodLabelText ?? 'Period total' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--indigo h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-stopwatch"></i></div>
                <div class="cc-kpi-title">Resolved in ≤72h</div>
              </div>
              <div class="cc-kpi-value">{{ number_format($within3DaysPercent ?? 0, 2) }}%</div>
              <div class="cc-kpi-sub">{{ $periodLabelText ?? 'Period total' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="px-4 pb-4">
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Direct vs POP Impacted (Logged Faults)</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge rounded-pill bg-primary-subtle text-primary">Total: {{ number_format($newFaultsTotal ?? 0) }}</span>
                <span class="badge rounded-pill bg-primary">Direct: {{ number_format($directFaultsTotal ?? 0) }}</span>
                <span class="badge rounded-pill bg-success">POP Impacted: {{ number_format($popImpactedFaultsTotal ?? 0) }}</span>
              </div>
              <canvas id="chartCategorySplit"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Category Trend (Logged Faults)</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartCategoryTrend"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">New Faults Received</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartWeeklyNewSingle"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Faults Resolved</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartWeeklyResolvedSingle"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Faults Resolved Within 72 Hours</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartWeeklyResolved3Days"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Total Outstanding Faults</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartWeeklyOutstandingSingle"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Resolved Faults – Age Analysis</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartResolvedAge"></canvas>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Outstanding Faults – Age Analysis</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartOutstandingAge"></canvas>
            </div>
          </div>
        </div>
      </div>
      <!-- Traffic By Shift -->
      <div class="px-4 pb-4">
        <div class="row g-4">
          <div class="col-12">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Traffic By Shift (Logged Faults)</div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="View details">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
              <canvas id="chartShiftTraffic"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="px-4 pb-4">
        <div class="row g-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm cc-analysis-card">
              <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                @php $ff = $filter ?? 'month'; @endphp
                <div class="fw-semibold">{{ $ff === 'year' ? 'Monthly Analysis' : ($ff === 'weekly' ? 'Daily Analysis' : 'Weekly Analysis') }}</div>
                <div class="text-muted small">{{ $ff === 'year' ? 'Balances and performance by month' : ($ff === 'weekly' ? 'Balances and performance by day' : 'Balances and performance by week') }}</div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  @php $ff = $filter ?? 'month'; @endphp
                  <table class="table align-middle mb-0 cc-analysis-table">
                    <thead>
                      <tr>
                        <th>{{ $ff === 'year' ? 'Month' : ($ff === 'weekly' ? 'Day' : 'Week') }}</th>
                        <th>Opening Balance</th>
                        <th>New Faults Received</th>
                        <th>Total Faults</th>
                        <th>Resolved Faults</th>
                        <th>Closing Balance – Pending Faults</th>
                        <th>Resolved Within 72hrs</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($ff === 'year')
                        @php $labels = ($monthlyActiveLabels ?? $monthlyLabels ?? []); @endphp
                        @foreach(($labels) as $i => $ml)
                          @php
                            $opening = (int)(($monthlyOpeningActive[$i] ?? null) ?? ($monthlyOpening[$i] ?? 0));
                            $new = (int)(($monthlyNewFaultsActive[$i] ?? null) ?? ($monthlyNewFaults[$i] ?? 0));
                            $resolved = (int)(($monthlyResolvedActive[$i] ?? null) ?? ($monthlyResolved[$i] ?? 0));
                            $closing = (int)(($monthlyOutstandingActive[$i] ?? null) ?? ($monthlyOutstanding[$i] ?? 0));
                            $totalMonth = (int)(($monthlyTotalsActive[$i] ?? null) ?? ($monthlyTotals[$i] ?? ($opening + $new)));
                            $perc = (int) round((($monthlyResolved3DaysPercActive[$i] ?? null) ?? ($monthlyResolved3DaysPerc[$i] ?? 0)));
                          @endphp
                          <tr>
                            <td><div class="fw-semibold text-gray-800">{{ $ml }}</div></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format($opening) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format($new) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format($totalMonth) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format($resolved) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format($closing) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ $perc }}%</span></td>
                          </tr>
                        @endforeach
                      @elseif($ff === 'weekly')
                        @foreach(($dailyLabels ?? []) as $i => $day)
                          @php($perc = (int) round(($dailyResolved3DaysPerc[$i] ?? 0)))
                          <tr>
                            <td><div class="fw-semibold text-gray-800">{{ $day }}</div></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($dailyOpening[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($dailyNewFaults[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($dailyTotals[$i] ?? (($dailyOpening[$i] ?? 0) + ($dailyNewFaults[$i] ?? 0)))) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($dailyResolved[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($dailyOutstanding[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ $perc }}%</span></td>
                          </tr>
                        @endforeach
                      @else
                        @foreach(($weeklyLabels ?? []) as $i => $wk)
                          @php($perc = (int) round(($weeklyResolved3DaysPerc[$i] ?? 0)))
                          <tr>
                            <td><div class="fw-semibold text-gray-800">{{ $wk }}</div></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($weeklyOpening[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($weeklyNewFaults[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($weeklyTotals[$i] ?? (($weeklyOpening[$i] ?? 0) + ($weeklyNewFaults[$i] ?? 0)))) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($weeklyResolved[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ number_format(($weeklyOutstanding[$i] ?? 0)) }}</span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark">{{ $perc }}%</span></td>
                          </tr>
                        @endforeach
                      @endif
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
@section('scripts')
<script>
  window.callCentreData = {
    filter: @json($filter ?? 'month'),
    categoryLabels: @json(['Direct Faults', 'POP Impacted Faults']),
    categoryValues: @json([(int)($directFaultsTotal ?? 0), (int)($popImpactedFaultsTotal ?? 0)]),
    weeklyLabels: @json($weeklyLabels ?? []),
    weeklyNewFaults: @json($weeklyNewFaults ?? []),
    weeklyNewFaultsDirect: @json($weeklyNewFaultsDirect ?? []),
    weeklyNewFaultsPop: @json($weeklyNewFaultsPop ?? []),
    weeklyResolved: @json($weeklyResolved ?? []),
    weeklyOutstanding: @json($weeklyOutstanding ?? []),
    weeklyResolved3DaysPerc: @json($weeklyResolved3DaysPerc ?? []),
    resolvedBins: @json($resolvedBins ?? []),
    outstandingBins: @json($outstandingBins ?? []),
    dailyLabels: @json($dailyLabels ?? []),
    dailyNewFaults: @json($dailyNewFaults ?? []),
    dailyNewFaultsDirect: @json($dailyNewFaultsDirect ?? []),
    dailyNewFaultsPop: @json($dailyNewFaultsPop ?? []),
    dailyResolved: @json($dailyResolved ?? []),
    dailyOutstanding: @json($dailyOutstanding ?? []),
    dailyResolved3DaysPerc: @json($dailyResolved3DaysPerc ?? []),
    dailyShiftMorning: @json($dailyShiftMorning ?? []),
    dailyShiftAfternoon: @json($dailyShiftAfternoon ?? []),
    dailyShiftNight: @json($dailyShiftNight ?? []),
    weeklyShiftMorning: @json($weeklyShiftMorning ?? []),
    weeklyShiftAfternoon: @json($weeklyShiftAfternoon ?? []),
    weeklyShiftNight: @json($weeklyShiftNight ?? []),
    weeklyRangeStarts: @json($weeklyRangeStarts ?? []),
    weeklyRangeEnds: @json($weeklyRangeEnds ?? []),
    monthlyLabels: @json(($monthlyActiveLabels ?? $monthlyLabels) ?? []),
    monthlyOpening: @json(($monthlyOpeningActive ?? $monthlyOpening) ?? []),
    monthlyNewFaults: @json(($monthlyNewFaultsActive ?? $monthlyNewFaults) ?? []),
    monthlyNewFaultsDirect: @json(($monthlyNewFaultsDirectActive ?? $monthlyNewFaultsDirect) ?? []),
    monthlyNewFaultsPop: @json(($monthlyNewFaultsPopActive ?? $monthlyNewFaultsPop) ?? []),
    monthlyResolved: @json(($monthlyResolvedActive ?? $monthlyResolved) ?? []),
    monthlyOutstanding: @json(($monthlyOutstandingActive ?? $monthlyOutstanding) ?? []),
    monthlyTotals: @json(($monthlyTotalsActive ?? $monthlyTotals) ?? []),
    monthlyResolved3DaysPerc: @json(($monthlyResolved3DaysPercActive ?? $monthlyResolved3DaysPerc) ?? []),
    monthlyShiftMorning: @json(($monthlyShiftMorningActive ?? $monthlyShiftMorning) ?? []),
    monthlyShiftAfternoon: @json(($monthlyShiftAfternoonActive ?? $monthlyShiftAfternoon) ?? []),
    monthlyShiftNight: @json(($monthlyShiftNightActive ?? $monthlyShiftNight) ?? []),
  };
</script>
<script>
  (function () {
    var btn = document.getElementById('callCentreHardRefresh');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var url = new URL(window.location.href);
      url.searchParams.set('_hr', String(Date.now()));
      window.location.replace(url.toString());
    });
  })();
</script>
<script src="{{ asset('js/call_centre.js') }}?v={{ file_exists(public_path('js/call_centre.js')) ? filemtime(public_path('js/call_centre.js')) : time() }}"></script>
@endsection
@endsection
