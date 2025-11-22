@extends('layouts.admin')

@section('title')
Call Centre Reports
@endsection

@section('content')
<section class="content">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Call Centre Reports</h3>
      <div class="card-tools">
        <form method="get" action="{{ route('call_centre.reports') }}" class="d-flex align-items-end gap-2">
          <div>
            <label class="form-label">Filter</label>
            <select name="filter" class="form-select form-select-sm">
              <option value="month" {{ ($filter ?? 'month') === 'month' ? 'selected' : '' }}>Month</option>
              <option value="year" {{ ($filter ?? '') === 'year' ? 'selected' : '' }}>Year</option>
              <option value="weekly" {{ ($filter ?? '') === 'weekly' ? 'selected' : '' }}>Weekly Range</option>
              <option value="quarter" {{ ($filter ?? '') === 'quarter' ? 'selected' : '' }}>Quarter</option>
            </select>
          </div>
          <div>
            <label class="form-label">Month</label>
            <select name="month" class="form-select form-select-sm">
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ ($selectedMonth ?? 0) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
              @endfor
            </select>
          </div>
          <div>
            <label class="form-label">Year</label>
            <select name="year" class="form-select form-select-sm">
              @foreach(($availableYears ?? []) as $y)
                <option value="{{ $y }}" {{ ($selectedYear ?? 0) == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Quarter</label>
            <select name="quarter" class="form-select form-select-sm">
              <option value="1" {{ ($quarter ?? 1) == 1 ? 'selected' : '' }}>Q1</option>
              <option value="2" {{ ($quarter ?? 1) == 2 ? 'selected' : '' }}>Q2</option>
              <option value="3" {{ ($quarter ?? 1) == 3 ? 'selected' : '' }}>Q3</option>
              <option value="4" {{ ($quarter ?? 1) == 4 ? 'selected' : '' }}>Q4</option>
            </select>
          </div>
          <div>
            <label class="form-label">Start</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" />
          </div>
          <div>
            <label class="form-label">End</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" />
          </div>
          <div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Apply</button>
          </div>
        </form>
      </div>
    </div>
    <div class="card-body">
      <div class="row g-3 mb-3">
        <div class="col-md-3">
          <div class="p-3 border rounded h-100">
            <div class="text-muted">Period</div>
            <div class="fw-semibold">{{ ($periodStart ?? now())->format('d M Y') }} — {{ ($periodEnd ?? now())->format('d M Y') }}</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3 border rounded h-100">
            <div class="text-muted">New Faults</div>
            <div class="fs-4 fw-bold">{{ number_format($newFaultsTotal ?? 0) }}</div>
            <div class="small text-muted">Month total</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3 border rounded h-100">
            <div class="text-muted">Resolved</div>
            <div class="fs-4 fw-bold">{{ number_format($resolvedTotal ?? 0) }}</div>
            <div class="small text-muted">Month total</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3 border rounded h-100">
            <div class="text-muted">Resolved ≤ 3 days</div>
            <div class="fs-4 fw-bold">{{ number_format($within3DaysPercent ?? 0, 2) }}%</div>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100">
            <div class="fw-semibold mb-2">New Faults Received</div>
            <canvas id="chartWeeklyNewSingle"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100">
            <div class="fw-semibold mb-2">Faults Resolved</div>
            <canvas id="chartWeeklyResolvedSingle"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100">
            <div class="fw-semibold mb-2">Faults Resolved Within 3 Days</div>
            <canvas id="chartWeeklyResolved3Days"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100">
            <div class="fw-semibold mb-2">Total Outstanding Faults</div>
            <canvas id="chartWeeklyOutstandingSingle"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100">
            <div class="fw-semibold mb-2">Resolved Faults – Age Analysis</div>
            <canvas id="chartResolvedAge"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded h-100">
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
    weeklyLabels: @json($weeklyLabels ?? []),
    weeklyNewFaults: @json($weeklyNewFaults ?? []),
    weeklyResolved: @json($weeklyResolved ?? []),
    weeklyOutstanding: @json($weeklyOutstanding ?? []),
    weeklyResolved3DaysPerc: @json($weeklyResolved3DaysPerc ?? []),
    resolvedBins: @json($resolvedBins ?? []),
    outstandingBins: @json($outstandingBins ?? []),
  };
</script>
<script src="{{ asset('js/call_centre.js') }}"></script>
@endsection
@endsection