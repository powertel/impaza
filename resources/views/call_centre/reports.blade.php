@extends('layouts.admin')

@section('title')
Call Centre Reports
@endsection

@section('content')
<link href="{{ asset('css/call_centre.css') }}" rel="stylesheet">
<section class="content">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Call Centre Reports</h3>
      <div class="card-tools">
        <form method="get" action="{{ route('call_centre.reports') }}" class="cc-filter-bar d-flex flex-wrap align-items-end gap-3">
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-sliders-h me-1"></i>Filter</label>
            <select name="filter" class="form-select form-select-sm" title="Select filter type">
              <option value="month" {{ ($filter ?? 'month') === 'month' ? 'selected' : '' }}>Month</option>
              <option value="year" {{ ($filter ?? '') === 'year' ? 'selected' : '' }}>Year</option>
              <option value="weekly" {{ ($filter ?? '') === 'weekly' ? 'selected' : '' }}>Weekly Range</option>
              <option value="quarter" {{ ($filter ?? '') === 'quarter' ? 'selected' : '' }}>Quarter</option>
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
            <label class="form-label"><i class="far fa-play-circle me-1"></i>Start</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" title="Start date for weekly range" />
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-stop-circle me-1"></i>End</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" title="End date for weekly range" />
          </div>
          <div class="cc-filter-actions">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Apply</button>
            <button type="button" class="btn btn-light btn-sm ms-2" data-cc-reset><i class="fas fa-undo me-1"></i>Reset</button>
          </div>
        </form>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-3 mb-3">
        <div class="col-md-3">
          <div class="cc-kpi cc-kpi--slate h-100">
            <div class="cc-kpi-head">
              <div class="cc-kpi-icon"><i class="far fa-calendar-alt"></i></div>
              <div class="cc-kpi-title">Period</div>
            </div>
            <div class="cc-kpi-value">{{ ($periodStart ?? now())->format('d M Y') }} — {{ ($periodEnd ?? now())->format('d M Y') }}</div>
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
              <div class="cc-kpi-title">Resolved</div>
            </div>
            <div class="cc-kpi-value">{{ number_format($resolvedTotal ?? 0) }}</div>
            <div class="cc-kpi-sub">{{ $periodLabelText ?? 'Period total' }}</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="cc-kpi cc-kpi--indigo h-100">
            <div class="cc-kpi-head">
              <div class="cc-kpi-icon"><i class="fas fa-stopwatch"></i></div>
              <div class="cc-kpi-title"></div>
            </div>
            <div class="cc-kpi-value">{{ number_format($within3DaysPercent ?? 0, 2) }}%</div>
            <div class="cc-kpi-sub">{{ $periodLabelText ?? 'Period total' }}</div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100 cc-chart-card">
            <div class="fw-semibold mb-2">New Faults Received</div>
            <canvas id="chartWeeklyNewSingle"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100 cc-chart-card">
            <div class="fw-semibold mb-2">Faults Resolved</div>
            <canvas id="chartWeeklyResolvedSingle"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100 cc-chart-card">
            <div class="fw-semibold mb-2">Faults Resolved Within 3 Days</div>
            <canvas id="chartWeeklyResolved3Days"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100 cc-chart-card">
            <div class="fw-semibold mb-2">Total Outstanding Faults</div>
            <canvas id="chartWeeklyOutstandingSingle"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100 cc-chart-card">
            <div class="fw-semibold mb-2">Resolved Faults – Age Analysis</div>
            <canvas id="chartResolvedAge"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100 cc-chart-card">
            <div class="fw-semibold mb-2">Outstanding Faults – Age Analysis</div>
            <canvas id="chartOutstandingAge"></canvas>
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
    weeklyLabels: @json($weeklyLabels ?? []),
    weeklyNewFaults: @json($weeklyNewFaults ?? []),
    weeklyResolved: @json($weeklyResolved ?? []),
    weeklyOutstanding: @json($weeklyOutstanding ?? []),
    weeklyResolved3DaysPerc: @json($weeklyResolved3DaysPerc ?? []),
    resolvedBins: @json($resolvedBins ?? []),
    outstandingBins: @json($outstandingBins ?? []),
    dailyLabels: @json($dailyLabels ?? []),
    dailyNewFaults: @json($dailyNewFaults ?? []),
    dailyResolved: @json($dailyResolved ?? []),
    dailyOutstanding: @json($dailyOutstanding ?? []),
    dailyResolved3DaysPerc: @json($dailyResolved3DaysPerc ?? []),
  };
</script>
<script src="{{ asset('js/call_centre.js') }}"></script>
@endsection
@endsection