@extends('layouts.admin')

@section('title')
LTE Site Surveys Reports
@endsection

@section('content')
<link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
<section class="content ux-unified">
  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
            <i class="fas fa-chart-line text-primary me-3"></i>
            LTE Site Survey Reports
          </h3>
          <p class="text-sm text-gray-600 mb-0 mt-1 me-3">Drill-down analytics for survey capture and completeness</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('lte-site-surveys.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Surveys
          </a>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="bg-gray-50 px-4 py-3 border-bottom">
        <form method="get" action="{{ route('lte-site-surveys.reports') }}" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-between gap-3">
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-sliders-h me-1"></i>Time Period</label>
            <select name="filter" class="form-select form-select-sm">
              <option value="month" {{ ($filter ?? 'month') === 'month' ? 'selected' : '' }}>Monthly</option>
              <option value="year" {{ ($filter ?? '') === 'year' ? 'selected' : '' }}>Yearly</option>
              <option value="weekly" {{ ($filter ?? '') === 'weekly' ? 'selected' : '' }}>Custom Range</option>
              <option value="quarter" {{ ($filter ?? '') === 'quarter' ? 'selected' : '' }}>Quarterly</option>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
            <select name="month" class="form-select form-select-sm">
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ ((int)($selectedMonth ?? 0)) === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
              @endfor
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-calendar me-1"></i>Year</label>
            <select name="year" class="form-select form-select-sm">
              <option value="all" {{ (($filter ?? '') === 'year' && strtolower((string)request('year')) === 'all') ? 'selected' : '' }}>All Years</option>
              @foreach(($availableYears ?? []) as $y)
                <option value="{{ $y }}" {{ ((int)($selectedYear ?? 0)) === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-clock me-1"></i>Quarter</label>
            <select name="quarter" class="form-select form-select-sm">
              <option value="1" {{ ((int)($quarter ?? 1)) === 1 ? 'selected' : '' }}>Q1</option>
              <option value="2" {{ ((int)($quarter ?? 1)) === 2 ? 'selected' : '' }}>Q2</option>
              <option value="3" {{ ((int)($quarter ?? 1)) === 3 ? 'selected' : '' }}>Q3</option>
              <option value="4" {{ ((int)($quarter ?? 1)) === 4 ? 'selected' : '' }}>Q4</option>
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
            <label class="form-label"><i class="fas fa-flag me-1"></i>Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="" {{ empty($selectedStatus ?? '') ? 'selected' : '' }}>All Status</option>
              <option value="draft" {{ ($selectedStatus ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="submitted" {{ ($selectedStatus ?? '') === 'submitted' ? 'selected' : '' }}>Submitted</option>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-user me-1"></i>Captured By</label>
            <select name="captured_by" class="form-select form-select-sm">
              <option value="0" {{ ((int)($selectedCapturedBy ?? 0)) === 0 ? 'selected' : '' }}>All</option>
              @foreach(($users ?? []) as $u)
                <option value="{{ $u->id }}" {{ ((int)($selectedCapturedBy ?? 0)) === (int)$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-user-check me-1"></i>Performed By</label>
            <select name="performed_by" class="form-select form-select-sm">
              <option value="" {{ empty($selectedPerformedBy ?? '') ? 'selected' : '' }}>All</option>
              @foreach(($availablePerformedBy ?? []) as $pb)
                <option value="{{ $pb }}" {{ (($selectedPerformedBy ?? '') === $pb) ? 'selected' : '' }}>{{ $pb }}</option>
              @endforeach
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-play-circle me-1"></i>Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" />
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-stop-circle me-1"></i>End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" />
          </div>
          <div class="cc-filter-actions ms-auto">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
              <i class="fas fa-filter me-1"></i> Apply Filters
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-cc-reset>
              <i class="fas fa-undo me-1"></i> Reset
            </button>
          </div>
        </form>
      </div>

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
            <a class="text-decoration-none" href="{{ route('lte-site-surveys.reports', array_merge(request()->query(), ['status' => ''])) }}">
              <div class="cc-kpi cc-kpi--blue h-100">
                <div class="cc-kpi-head">
                  <div class="cc-kpi-icon"><i class="fas fa-clipboard-list"></i></div>
                  <div class="cc-kpi-title">Total Surveys</div>
                </div>
                <div class="cc-kpi-value">{{ number_format((int)($total ?? 0)) }}</div>
                <div class="cc-kpi-sub">Click to clear status filter</div>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('lte-site-surveys.reports', array_merge(request()->query(), ['status' => 'submitted'])) }}">
              <div class="cc-kpi cc-kpi--green h-100">
                <div class="cc-kpi-head">
                  <div class="cc-kpi-icon"><i class="fas fa-check-circle"></i></div>
                  <div class="cc-kpi-title">Submitted</div>
                </div>
                <div class="cc-kpi-value">{{ number_format((int)($submitted ?? 0)) }}</div>
                <div class="cc-kpi-sub">Click to drill down</div>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a class="text-decoration-none" href="{{ route('lte-site-surveys.reports', array_merge(request()->query(), ['status' => 'draft'])) }}">
              <div class="cc-kpi cc-kpi--indigo h-100">
                <div class="cc-kpi-head">
                  <div class="cc-kpi-icon"><i class="fas fa-pen"></i></div>
                  <div class="cc-kpi-title">Draft</div>
                </div>
                <div class="cc-kpi-value">{{ number_format((int)($draft ?? 0)) }}</div>
                <div class="cc-kpi-sub">Click to drill down</div>
              </div>
            </a>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-md-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Completeness</div>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge rounded-pill bg-primary-subtle text-primary">With Photos: {{ number_format((int)($withPhotos ?? 0)) }}</span>
                <span class="badge rounded-pill bg-success-subtle text-success">With Remarks: {{ number_format((int)($withRemarks ?? 0)) }}</span>
              </div>
              <div class="text-muted small mt-2">Use drill-down below to filter by region, performed by, backhaul and power.</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Quick Links</div>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary btn-sm rounded-pill" href="{{ route('lte-site-surveys.index') }}"><i class="fas fa-list me-1"></i> Survey List</a>
                <a class="btn btn-outline-secondary btn-sm rounded-pill" href="{{ route('lte-site-surveys.index', ['status' => 'draft']) }}"><i class="fas fa-pen me-1"></i> Draft Surveys</a>
                <a class="btn btn-outline-secondary btn-sm rounded-pill" href="{{ route('lte-site-surveys.index', ['status' => 'submitted']) }}"><i class="fas fa-check me-1"></i> Submitted Surveys</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="px-4 pb-4">
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3">By Region</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th>Region</th>
                      <th class="text-end">Surveys</th>
                      <th class="text-end">Drill</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse(($regionBreakdown ?? collect()) as $row)
                      <tr>
                        <td>{{ $row->k }}</td>
                        <td class="text-end">{{ (int)$row->c }}</td>
                        <td class="text-end">
                          <a class="btn btn-outline-primary btn-sm rounded-pill" href="{{ route('lte-site-surveys.reports', array_merge(request()->query(), ['region' => $row->k])) }}">View</a>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3">By Survey Performed By</div>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th>User</th>
                      <th class="text-end">Surveys</th>
                      <th class="text-end">Drill</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse(($performedByBreakdown ?? collect()) as $row)
                      <tr>
                        <td>{{ $row->k }}</td>
                        <td class="text-end">{{ (int)$row->c }}</td>
                        <td class="text-end">
                          <a class="btn btn-outline-primary btn-sm rounded-pill" href="{{ route('lte-site-surveys.reports', array_merge(request()->query(), ['performed_by' => $row->k])) }}">View</a>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3">Backhaul Type</div>
              <div style="height: 320px;">
                <canvas id="lteChartBackhaul"></canvas>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cc-chart-card">
              <div class="fw-semibold mb-3">Power Source</div>
              <div style="height: 320px;">
                <canvas id="lteChartPower"></canvas>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="cc-chart-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">Survey List (Filtered)</div>
              </div>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Site</th>
                      <th>JC</th>
                      <th>Region</th>
                      <th>Status</th>
                      <th>Performed By</th>
                      <th>Captured By</th>
                      <th>Created</th>
                      <th class="text-end">Open</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse(($surveys ?? collect()) as $s)
                      <tr>
                        <td class="fw-semibold">{{ $s->site_name ?: 'Untitled' }}</td>
                        <td class="text-muted">{{ $s->jc_number ?: '-' }}</td>
                        <td class="text-muted">{{ $s->province_region ?: '-' }}</td>
                        <td class="text-nowrap">
                          @if(($s->status ?? '') === 'submitted')
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Submitted</span>
                          @else
                            <span class="badge bg-warning text-dark"><i class="fas fa-pen me-1"></i>Draft</span>
                          @endif
                        </td>
                        <td class="text-muted">{{ $s->survey_performed_by ?: '-' }}</td>
                        <td class="text-muted">{{ optional($s->user)->name ?: '-' }}</td>
                        <td class="text-muted text-nowrap">{{ optional($s->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                          <a class="btn btn-outline-primary btn-sm rounded-pill" href="{{ route('lte-site-surveys.index', ['q' => $s->site_name]) }}">
                            <i class="fas fa-external-link-alt me-1"></i> Open
                          </a>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="8" class="text-center text-muted py-4">No surveys found for the selected filters.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="d-flex justify-content-end">
                {{ $surveys->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function(){
    var reset = document.querySelector('[data-cc-reset]');
    if (!reset) return;
    reset.addEventListener('click', function(){
      window.location.href = "{{ route('lte-site-surveys.reports') }}";
    });
  })();
</script>
<script>
  (function () {
    if (typeof Chart === 'undefined') return;

    function normLabel(v) {
      var s = (v == null) ? 'unknown' : String(v);
      s = s.trim();
      if (s === '' || s.toLowerCase() === 'null') s = 'unknown';
      s = s.replace(/_/g, ' ');
      return s.charAt(0).toUpperCase() + s.slice(1);
    }

    function colors(n) {
      var palette = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#64748b', '#14b8a6', '#f97316', '#a3e635', '#60a5fa', '#fb7185'];
      var out = [];
      for (var i = 0; i < n; i++) out.push(palette[i % palette.length]);
      return out;
    }

    function buildRows(raw) {
      var rows = Array.isArray(raw) ? raw : [];
      return rows
        .map(function (r) { return { k: normLabel(r.k), c: parseInt(r.c || 0, 10) || 0 }; })
        .filter(function (r) { return r.c > 0; });
    }

    function renderPie(canvasId, rawRows) {
      var rows = buildRows(rawRows);
      var el = document.getElementById(canvasId);
      if (!el) return;
      if (!rows.length) return;
      var legendPos = rows.length > 5 ? 'right' : 'bottom';
      new Chart(el, {
        type: 'pie',
        data: {
          labels: rows.map(function (r) { return r.k; }),
          datasets: [{
            data: rows.map(function (r) { return r.c; }),
            backgroundColor: colors(rows.length),
            borderColor: '#ffffff',
            borderWidth: 2,
            
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: { padding: { top: 8, bottom: 26, left: 8, right: 8 } },
          plugins: {
            legend: { position: legendPos, labels: { usePointStyle: true, boxWidth: 10, padding: 14 } },
            tooltip: {
              backgroundColor: 'rgba(31,41,55,0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 10,
              callbacks: {
                label: function (ctx) {
                  var label = (ctx.label || '') + ': ' + (ctx.parsed || 0);
                  return label;
                }
              }
            }
          }
        }
      });
    }

    renderPie('lteChartBackhaul', @json(($backhaulBreakdown ?? collect())->values()));
    renderPie('lteChartPower', @json(($powerBreakdown ?? collect())->values()));
  })();
</script>
@endsection
