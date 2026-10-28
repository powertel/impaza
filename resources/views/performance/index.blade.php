@extends('layouts.admin')

@section('title')
Performance Dashboard
@endsection

@section('styles')
<link href="{{ asset('css/call_centre.css') }}?v={{ @filemtime(public_path('css/call_centre.css')) }}" rel="stylesheet">
<link href="{{ asset('css/fault-drill.css') }}?v={{ @filemtime(public_path('css/fault-drill.css')) }}" rel="stylesheet">
<style>
  .perf-filter { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px 16px; box-shadow: 0 8px 20px rgba(15, 23, 42, .05); }
  .perf-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; }
  .perf-presets { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
  .perf-presets-label { font-size: .75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .03em; margin-right: 4px; }
  .perf-preset { font-size: .8rem; padding: 4px 12px; border-radius: 999px; border: 1px solid #dce3ee; color: #374151; background: #fff; text-decoration: none; white-space: nowrap; transition: all .15s ease; }
  .perf-preset:hover { border-color: #93c5fd; color: #1d4ed8; background: #eff6ff; }
  .perf-preset.active { background: #2563eb; border-color: #2563eb; color: #fff; box-shadow: 0 4px 10px rgba(37, 99, 235, .25); }
  .perf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; align-items: end; }
  .perf-grid .form-label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: 4px; }
  .perf-grid .form-select, .perf-grid .form-control { border-radius: 8px; font-size: .85rem; }
  .perf-range { grid-column: span 2; }
  .perf-range .input-group-text { font-size: .78rem; background: transparent; }
  .perf-chip { display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: #ecfdf5; color: #047857; }

  .perf-kpis { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
  @media (max-width: 991.98px) { .perf-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 575.98px) { .perf-kpis { grid-template-columns: 1fr; } }
  .perf-kpi { position: relative; border-radius: 14px; padding: 14px 16px; background: #fff; border: 1px solid #e5e7eb; border-top: 4px solid var(--k, #2563eb); box-shadow: 0 6px 16px rgba(15, 23, 42, .05); min-height: 118px; }
  .perf-kpi-title { font-size: .75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .03em; display: flex; align-items: center; gap: 8px; }
  .perf-kpi-title i { width: 26px; height: 26px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: #fff; background: var(--k, #2563eb); font-size: .75rem; }
  .perf-kpi-value { font-size: 1.6rem; font-weight: 800; color: #111827; line-height: 1.15; margin-top: 8px; word-break: break-word; }
  .perf-kpi-value.is-name { font-size: 1.15rem; }
  .perf-kpi-sub { font-size: .78rem; color: #6b7280; margin-top: 4px; }
  .perf-kpi .perf-bar { height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden; margin-top: 8px; }
  .perf-kpi .perf-bar span { display: block; height: 100%; background: var(--k, #2563eb); border-radius: 999px; }
  .perf-kpi-go { position: absolute; right: 12px; bottom: 10px; font-size: .7rem; color: #9ca3af; }

  .perf-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px 18px; box-shadow: 0 6px 16px rgba(15, 23, 42, .05); }
  .perf-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
  .perf-card-title { font-weight: 700; color: #111827; font-size: 1rem; }
  .perf-card-sub { font-size: .75rem; color: #6b7280; margin-top: 2px; }
  .perf-chart { position: relative; height: 280px; }
  .perf-legend { display: flex; gap: 14px; font-size: .75rem; color: #4b5563; }
  .perf-legend span::before { content: ''; display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px; vertical-align: -1px; background: var(--c); }

  .perf-table { font-size: .85rem; }
  .perf-table thead th { font-size: .72rem; text-transform: uppercase; letter-spacing: .03em; color: #6b7280; background: #f8fafc; white-space: nowrap; }
  .perf-table td { vertical-align: middle; }
  .perf-link { cursor: pointer; font-weight: 700; color: #1d4ed8; border-bottom: 1px dashed #93c5fd; }
  .perf-num { cursor: pointer; display: inline-block; min-width: 34px; padding: 2px 8px; border-radius: 999px; font-weight: 700; text-align: center; }
  .perf-num:hover { filter: brightness(.95); box-shadow: 0 0 0 2px rgba(37, 99, 235, .25); }
  .perf-num.is-zero { cursor: default; opacity: .45; box-shadow: none; }
  .n-total { background: #eff6ff; color: #1d4ed8; }
  .n-resolved { background: #ecfdf5; color: #047857; }
  .n-pending { background: #fffbeb; color: #b45309; }
  .n-overdue { background: #fef2f2; color: #b91c1c; }
  .perf-rate { display: flex; align-items: center; gap: 8px; min-width: 140px; }
  .perf-rate .bar { flex: 1; height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
  .perf-rate .bar span { display: block; height: 100%; border-radius: 999px; }
  .perf-rank { display: inline-flex; width: 22px; height: 22px; border-radius: 999px; align-items: center; justify-content: center; font-size: .72rem; font-weight: 800; background: #f1f5f9; color: #475569; margin-right: 8px; }
  .perf-rank.r1 { background: #fef3c7; color: #b45309; }
  .perf-rank.r2 { background: #e5e7eb; color: #374151; }
  .perf-rank.r3 { background: #fde7d9; color: #9a3412; }
  .perf-section-title { font-size: 1.05rem; font-weight: 700; color: #374151; margin: 6px 4px 12px; }
  .perf-def { font-size: .75rem; color: #6b7280; }

  html[data-theme="dark"] .perf-filter, html[data-theme="dark"] .perf-kpi, html[data-theme="dark"] .perf-card { background: #0f172a; border-color: #334155; }
  html[data-theme="dark"] .perf-kpi-value, html[data-theme="dark"] .perf-card-title { color: #e2e8f0; }
  html[data-theme="dark"] .perf-preset { background: #0f172a; border-color: #334155; color: #cbd5e1; }
  html[data-theme="dark"] .perf-preset.active { background: #2563eb; color: #fff; }
  html[data-theme="dark"] .perf-table thead th { background: #1e293b; color: #94a3b8; }

  @media print {
    .main-sidebar, .main-header, .no-print, .btn, .perf-filter, .perf-kpi-go, .drill-hint { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .perf-card, .perf-kpi { box-shadow: none !important; break-inside: avoid; }
  }
</style>
@endsection

@section('content')
@php
  $nowP = now();
  $regionKeep = $selectedRegion ? ['region' => $selectedRegion] : [];
  $lm = $nowP->copy()->subMonthNoOverflow();
  $lq = $nowP->copy()->subQuarterNoOverflow();
  $presets = [
    'this_month' => ['This Month', ['filter' => 'month', 'month' => $nowP->month, 'year' => $nowP->year]],
    'last_month' => ['Last Month', ['filter' => 'month', 'month' => $lm->month, 'year' => $lm->year]],
    'this_quarter' => ['This Quarter', ['filter' => 'quarter', 'quarter' => $nowP->quarter, 'year' => $nowP->year]],
    'last_quarter' => ['Last Quarter', ['filter' => 'quarter', 'quarter' => $lq->quarter, 'year' => $lq->year]],
    'last_3m' => ['Last 3 Months', ['filter' => 'weekly', 'start_date' => $nowP->copy()->subMonthsNoOverflow(3)->toDateString(), 'end_date' => $nowP->toDateString()]],
    'last_6m' => ['Last 6 Months', ['filter' => 'weekly', 'start_date' => $nowP->copy()->subMonthsNoOverflow(6)->toDateString(), 'end_date' => $nowP->toDateString()]],
    'this_year' => ['This Year', ['filter' => 'year', 'year' => $nowP->year]],
    'last_year' => ['Last Year', ['filter' => 'year', 'year' => $nowP->year - 1]],
  ];
  $activePreset = null;
  foreach ($presets as $key => [$lbl, $params]) {
    $match = true;
    foreach ($params as $k => $v) {
      $cur = $k === 'filter' ? $filter : ($k === 'start_date' ? $startDate : ($k === 'end_date' ? $endDate : ($k === 'month' ? $selectedMonth : ($k === 'quarter' ? $selectedQuarter : $selectedYear))));
      if ((string) $cur !== (string) $v) { $match = false; break; }
    }
    if ($match) { $activePreset = $key; break; }
  }
  $rateColor = fn ($r) => $r >= 80 ? '#10b981' : ($r >= 50 ? '#f59e0b' : '#ef4444');
  $techCount = $users->count();
  $ao = $assignedOverall;
  $fmt = fn ($n) => number_format((int) $n);
  $perfTables = [
    ['title' => 'Technician performance', 'rows' => $users, 'kind' => 'assigned_to', 'label' => 'Technician', 'icon' => 'fa-user-cog'],
    ['title' => 'Section performance', 'rows' => $sections, 'kind' => 'section_id', 'label' => 'Section', 'icon' => 'fa-sitemap'],
    ['title' => 'Department performance', 'rows' => $departments, 'kind' => 'department_id', 'label' => 'Department', 'icon' => 'fa-building'],
  ];
@endphp

<div id="drillConfig" style="display:none"
     data-drill-url="{{ route('dashboard.reports.drilldown') }}"
     data-drill-context='@json($drillContext)'
     data-user-name="{{ optional(auth()->user())->name }}"></div>
<div id="perfData" style="display:none" data-chart='@json($chartData)'></div>

<section class="content">
  <div class="card border-0 shadow-lg">
    <div class="card-header bg-white border-0 py-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
            <i class="fas fa-tachometer-alt text-primary me-2"></i> Performance Dashboard
          </h3>
          <p class="text-sm text-gray-600 mb-0 mt-1">How technicians, sections and departments are resolving faults. Click any number, bar or name to see the faults behind it.</p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
          <span class="perf-chip"><i class="fas fa-calendar-check"></i> {{ $periodLabel }}@if($selectedRegion) · {{ $selectedRegion }}@endif</span>
          <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fas fa-print me-1"></i> Print</button>
        </div>
      </div>
    </div>

    <div class="card-body px-4 pt-0 pb-4">
      {{-- Filters --}}
      <form action="{{ route('performance.index') }}" method="GET" class="perf-filter no-print mb-4" id="perfFilterForm">
        <div class="perf-top">
          <div class="perf-presets">
            <span class="perf-presets-label"><i class="fas fa-bolt me-1"></i>Quick period</span>
            @foreach($presets as $key => [$lbl, $params])
              <a class="perf-preset {{ $activePreset === $key ? 'active' : '' }}" href="{{ route('performance.index', array_merge($params, $regionKeep)) }}">{{ $lbl }}</a>
            @endforeach
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4"><i class="fas fa-filter me-1"></i> Apply</button>
            <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fas fa-undo me-1"></i> Reset</a>
          </div>
        </div>
        <div class="perf-grid">
          <div>
            <label class="form-label"><i class="fas fa-sliders-h me-1"></i>Period type</label>
            <select name="filter" id="filterType" class="form-select form-select-sm">
              <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Monthly</option>
              <option value="quarter" {{ $filter == 'quarter' ? 'selected' : '' }}>Quarterly</option>
              <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Yearly</option>
              <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>Custom range</option>
            </select>
          </div>
          <div class="perf-f" data-for="month">
            <label class="form-label"><i class="fas fa-calendar-alt me-1"></i>Month</label>
            <select name="month" class="form-select form-select-sm">
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ (int) $selectedMonth === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
              @endfor
            </select>
          </div>
          <div class="perf-f" data-for="quarter">
            <label class="form-label"><i class="fas fa-th-large me-1"></i>Quarter</label>
            <select name="quarter" class="form-select form-select-sm">
              @for($qq = 1; $qq <= 4; $qq++)
                <option value="{{ $qq }}" {{ (int) $selectedQuarter === $qq ? 'selected' : '' }}>Q{{ $qq }}</option>
              @endfor
            </select>
          </div>
          <div class="perf-f" data-for="month quarter year">
            <label class="form-label"><i class="fas fa-calendar me-1"></i>Year</label>
            <select name="year" class="form-select form-select-sm">
              @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
              <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }} data-only="year">All years</option>
            </select>
          </div>
          <div class="perf-f perf-range" data-for="weekly">
            <label class="form-label"><i class="fas fa-calendar-week me-1"></i>Date range</label>
            <div class="input-group input-group-sm">
              <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" max="{{ now()->toDateString() }}">
              <span class="input-group-text">to</span>
              <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
          </div>
          <div>
            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Region</label>
            <select name="region" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">All Regions</option>
              @foreach($availableRegions as $region)
                <option value="{{ $region }}" {{ $selectedRegion == $region ? 'selected' : '' }}>{{ $region }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </form>

      {{-- KPIs --}}
      <div class="perf-kpis mb-4">
        <div class="perf-kpi" style="--k:#2563eb" data-drill='{"assigned_only":1}' data-drill-label="Assigned faults">
          <div class="perf-kpi-title"><i class="fas fa-clipboard-list"></i> Faults assigned</div>
          <div class="perf-kpi-value">{{ $fmt($ao->total ?? 0) }}</div>
          <div class="perf-kpi-sub">across {{ $techCount }} technician{{ $techCount === 1 ? '' : 's' }} · {{ $fmt(($overall->total ?? 0) - ($ao->total ?? 0)) }} not yet assigned</div>
          <span class="perf-kpi-go"><i class="fas fa-arrow-right"></i></span>
        </div>
        <div class="perf-kpi" style="--k:#10b981" data-drill='{"assigned_only":1,"status_group":"resolved"}' data-drill-label="Resolved faults">
          <div class="perf-kpi-title"><i class="fas fa-check-double"></i> Resolution rate</div>
          <div class="perf-kpi-value">{{ $ao->resolution_rate ?? 0 }}%</div>
          <div class="perf-kpi-sub">{{ $fmt($ao->resolved ?? 0) }} resolved of {{ $fmt($ao->total ?? 0) }} assigned</div>
          <div class="perf-bar"><span style="width: {{ $ao->resolution_rate ?? 0 }}%"></span></div>
        </div>
        <div class="perf-kpi" style="--k:#8b5cf6" data-drill='{"assigned_only":1,"status_group":"resolved"}' data-drill-label="Resolved faults">
          <div class="perf-kpi-title"><i class="fas fa-stopwatch"></i> Avg resolution time</div>
          <div class="perf-kpi-value">{{ $ao->avg_label ?? '—' }}</div>
          <div class="perf-kpi-sub">
            @if(($ao->sla_rate ?? null) !== null)
              {{ $ao->sla_rate }}% resolved within {{ $slaHours }}h
            @else
              No resolved faults yet
            @endif
          </div>
        </div>
        <div class="perf-kpi" style="--k:#ef4444" data-drill='{"assigned_only":1,"status_group":"overdue"}' data-drill-label="Overdue faults (open &gt; {{ $overdueHours }}h)">
          <div class="perf-kpi-title"><i class="fas fa-exclamation-triangle"></i> Overdue</div>
          <div class="perf-kpi-value">{{ $fmt($ao->overdue ?? 0) }}</div>
          <div class="perf-kpi-sub">unresolved for more than {{ $overdueHours }} hours · {{ $fmt($ao->pending ?? 0) }} pending in total</div>
          <span class="perf-kpi-go"><i class="fas fa-arrow-right"></i></span>
        </div>
        <div class="perf-kpi" style="--k:#f59e0b" @if($topUser) data-drill='{"assigned_to":{{ $topUser->id }}}' data-drill-label="Technician: {{ $topUser->name }}" @endif>
          <div class="perf-kpi-title"><i class="fas fa-trophy"></i> Top technician</div>
          <div class="perf-kpi-value is-name">{{ $topUser->name ?? '—' }}</div>
          <div class="perf-kpi-sub">
            @if($topUser) {{ $fmt($topUser->resolved) }} resolved · {{ $topUser->resolution_rate }}% · avg {{ $topUser->avg_label }} @else No resolved faults in this period @endif
          </div>
        </div>
        <div class="perf-kpi" style="--k:#0ea5e9" @if($topSection) data-drill='{"section_id":{{ $topSection->id }}}' data-drill-label="Section: {{ $topSection->name }}" @endif>
          <div class="perf-kpi-title"><i class="fas fa-sitemap"></i> Top section</div>
          <div class="perf-kpi-value is-name">{{ $topSection->name ?? '—' }}</div>
          <div class="perf-kpi-sub">
            @if($topSection) {{ $fmt($topSection->resolved) }} resolved · {{ $topSection->resolution_rate }}% · avg {{ $topSection->avg_label }} @else — @endif
          </div>
        </div>
      </div>

      @if($attentionUser || $fastestUser)
        <div class="d-flex flex-wrap gap-2 mb-4">
          @if($fastestUser)
            <span class="perf-chip is-drillable" style="background:#eef2ff;color:#4338ca" data-drill='{"assigned_to":{{ $fastestUser->id }},"status_group":"resolved"}' data-drill-label="Technician: {{ $fastestUser->name }} (resolved)">
              <i class="fas fa-bolt"></i> Fastest: {{ $fastestUser->name }} — avg {{ $fastestUser->avg_label }} over {{ $fastestUser->resolved }} faults
            </span>
          @endif
          @if($attentionUser)
            <span class="perf-chip is-drillable" style="background:#fef2f2;color:#b91c1c" data-drill='{"assigned_to":{{ $attentionUser->id }},"status_group":"overdue"}' data-drill-label="Technician: {{ $attentionUser->name }} (overdue)">
              <i class="fas fa-user-clock"></i> Needs attention: {{ $attentionUser->name }} — {{ $attentionUser->overdue }} overdue
            </span>
          @endif
        </div>
      @endif

      {{-- Trend --}}
      <div class="perf-card mb-4">
        <div class="perf-card-head">
          <div>
            <div class="perf-card-title">Logged vs resolved</div>
            <div class="perf-card-sub">Faults logged each {{ $chartData['trend']['bucket'] }} and how many of them are resolved</div>
          </div>
          <div class="d-flex align-items-center gap-3">
            <div class="perf-legend"><span style="--c:#93c5fd">Logged</span><span style="--c:#10b981">Resolved</span></div>
            <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click to drill down</span>
          </div>
        </div>
        <div class="perf-chart" style="height:260px"><canvas id="perfTrendChart"></canvas></div>
      </div>

      {{-- Rankings --}}
      <div class="row g-4 mb-4 align-items-start">
        <div class="col-xl-7">
          <div class="perf-card">
            <div class="perf-card-head">
              <div>
                <div class="perf-card-title">Technicians</div>
                <div class="perf-card-sub">Ranked by faults resolved · label shows resolution rate</div>
              </div>
              <div class="perf-legend"><span style="--c:#10b981">Resolved</span><span style="--c:#f59e0b">Pending</span></div>
            </div>
            <div class="perf-chart" id="perfUsersWrap"><canvas id="perfUsersChart"></canvas></div>
          </div>
        </div>
        <div class="col-xl-5">
          <div class="perf-card">
            <div class="perf-card-head">
              <div>
                <div class="perf-card-title">Sections</div>
                <div class="perf-card-sub">Ranked by faults resolved</div>
              </div>
              <div class="perf-legend"><span style="--c:#10b981">Resolved</span><span style="--c:#f59e0b">Pending</span></div>
            </div>
            <div class="perf-chart" id="perfSectionsWrap"><canvas id="perfSectionsChart"></canvas></div>
          </div>
        </div>
        <div class="col-12">
          <div class="perf-card">
            <div class="perf-card-head">
              <div>
                <div class="perf-card-title">Departments</div>
                <div class="perf-card-sub">Each fault counted once per department</div>
              </div>
              <div class="perf-legend"><span style="--c:#10b981">Resolved</span><span style="--c:#f59e0b">Pending</span></div>
            </div>
            <div class="perf-chart" id="perfDeptsWrap"><canvas id="perfDeptsChart"></canvas></div>
          </div>
        </div>
      </div>

      {{-- Tables --}}
      @foreach($perfTables as $tbl)
        <div class="perf-section-title"><i class="fas {{ $tbl['icon'] }} me-2 text-primary"></i>{{ $tbl['title'] }}</div>
        <div class="perf-card mb-4 p-0" style="overflow:hidden">
          <div class="table-responsive">
            <table class="table align-middle mb-0 perf-table perf-dt">
              <thead>
                <tr>
                  <th>{{ $tbl['label'] }}</th>
                  <th class="text-center">Assigned</th>
                  <th class="text-center">Resolved</th>
                  <th class="text-center">Pending</th>
                  <th class="text-center">Overdue</th>
                  <th class="text-center">Avg resolution</th>
                  <th class="text-center">Within {{ $slaHours }}h</th>
                  <th>Resolution rate</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tbl['rows'] as $i => $r)
                  @php
                    $base = [$tbl['kind'] => $r->id];
                    $lbl = $tbl['label'] . ': ' . $r->name;
                  @endphp
                  <tr>
                    <td data-order="{{ $r->name }}">
                      <span class="perf-rank {{ $i < 3 ? 'r' . ($i + 1) : '' }}">{{ $i + 1 }}</span>
                      <span class="perf-link" data-drill='@json($base)' data-drill-label="{{ $lbl }}">{{ $r->name }}</span>
                      @if(!empty($r->parent))<div class="text-muted small ms-4 ps-2">{{ $r->parent }}</div>@endif
                    </td>
                    <td class="text-center" data-order="{{ $r->total }}"><span class="perf-num n-total" data-drill='@json($base)' data-drill-label="{{ $lbl }}">{{ $r->total }}</span></td>
                    <td class="text-center" data-order="{{ $r->resolved }}"><span class="perf-num n-resolved {{ $r->resolved ? '' : 'is-zero' }}" @if($r->resolved) data-drill='@json($base + ['status_group' => 'resolved'])' data-drill-label="{{ $lbl }} (resolved)" @endif>{{ $r->resolved }}</span></td>
                    <td class="text-center" data-order="{{ $r->pending }}"><span class="perf-num n-pending {{ $r->pending ? '' : 'is-zero' }}" @if($r->pending) data-drill='@json($base + ['status_group' => 'pending'])' data-drill-label="{{ $lbl }} (pending)" @endif>{{ $r->pending }}</span></td>
                    <td class="text-center" data-order="{{ $r->overdue }}"><span class="perf-num n-overdue {{ $r->overdue ? '' : 'is-zero' }}" @if($r->overdue) data-drill='@json($base + ['status_group' => 'overdue'])' data-drill-label="{{ $lbl }} (overdue)" @endif>{{ $r->overdue }}</span></td>
                    <td class="text-center text-nowrap" data-order="{{ $r->avg_seconds ?? 999999999 }}">{{ $r->avg_label }}</td>
                    <td class="text-center" data-order="{{ $r->sla_rate ?? -1 }}">{{ $r->sla_rate !== null ? $r->sla_rate . '%' : '—' }}</td>
                    <td data-order="{{ $r->resolution_rate }}">
                      <div class="perf-rate">
                        <strong style="min-width:44px">{{ $r->resolution_rate }}%</strong>
                        <div class="bar"><span style="width: {{ $r->resolution_rate }}%; background: {{ $rateColor($r->resolution_rate) }}"></span></div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="text-center text-muted py-4">No faults in this period.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endforeach

      <div class="perf-def no-print">
        <i class="fas fa-info-circle me-1"></i>
        Covers faults <strong>logged</strong> in the period. <strong>Resolved</strong> = Rectified, Cleared by CT or Restored.
        <strong>Resolution time</strong> = logged until the fault first reached one of those statuses.
        <strong>Overdue</strong> = still unresolved after {{ $overdueHours }} hours.
      </div>
    </div>
  </div>
</section>

@include('partials.fault_drill_modal')
@endsection

@section('scripts')
<script>
  window.__FAULTS_PDF_ASSETS__ = {
    logoUrl: @json(asset('images/powertel.png')),
    jspdfChunkUrl: @json(asset('js/node_modules_jspdf_dist_jspdf_es_min_js.js')),
    autotableChunkUrl: @json(asset('js/node_modules_jspdf-autotable_dist_jspdf_plugin_autotable_js.js')),
  };
</script>
<script src="{{ asset('js/faults-pdf.js') }}?v={{ @filemtime(public_path('js/faults-pdf.js')) }}"></script>
<script src="{{ asset('js/reports-drill.js') }}?v={{ @filemtime(public_path('js/reports-drill.js')) }}"></script>
<script src="{{ asset('js/performance.js') }}?v={{ @filemtime(public_path('js/performance.js')) }}"></script>
@endsection
