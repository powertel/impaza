@extends('layouts.admin')

@section('title')
Dashboard
@endsection

@include('partials.css')

@section('styles')
<style>
    .home-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        align-items: start;
    }

    .home-kpi-grid > * {
        min-width: 0;
    }

    .home-kpi-grid .dashboard-kpi-card {
        min-height: 112px;
        padding: 14px 16px;
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 6px 14px rgba(15, 23, 42, .04);
        gap: 10px;
    }

    .home-kpi-grid .dashboard-kpi-card::before {
        width: 2px;
        border-radius: 999px;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-head {
        justify-content: flex-start;
        gap: 10px;
        align-items: center;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        font-size: .76rem;
        box-shadow: 0 4px 10px rgba(99, 102, 241, .14);
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-title {
        text-align: left;
        font-size: .68rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-body {
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-value {
        font-size: 1.7rem;
        line-height: 1.05;
        letter-spacing: -.03em;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-sub,
    .home-kpi-grid .dashboard-kpi-card .impaza-stat-sublabel,
    .home-kpi-grid .dashboard-kpi-card .impaza-stat-trend {
        font-size: .66rem;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-subline {
        margin-top: 6px;
        gap: 5px;
    }

    .home-kpi-grid .dashboard-kpi-card .impaza-stat-spark {
        flex: 0 0 84px;
        max-width: 84px;
        min-width: 84px;
        align-self: center;
    }

    .home-kpi-grid .dashboard-kpi-card .kpi-spark {
        width: 84px;
        height: 34px;
        min-width: 84px;
        max-width: 84px;
        min-height: 34px;
        max-height: 34px;
        margin-left: auto;
    }

    .home-kpi-grid .dashboard-kpi-card .kpi-spark .apexcharts-canvas,
    .home-kpi-grid .dashboard-kpi-card .kpi-spark .apexcharts-svg,
    .home-kpi-grid .dashboard-kpi-card .kpi-spark foreignObject {
        width: 84px !important;
        min-width: 84px !important;
        max-width: 84px !important;
        height: 34px !important;
        min-height: 34px !important;
        max-height: 34px !important;
    }

    @media (max-width: 1399.98px) {
        .home-kpi-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .home-kpi-grid .dashboard-kpi-card {
            min-height: 100px;
            padding: 12px 14px;
            gap: 8px;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-head {
            gap: 9px;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-icon {
            width: 28px;
            height: 28px;
            font-size: .72rem;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-title {
            font-size: .64rem;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-value {
            font-size: 1.45rem;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-sub,
        .home-kpi-grid .dashboard-kpi-card .impaza-stat-sublabel,
        .home-kpi-grid .dashboard-kpi-card .impaza-stat-trend {
            font-size: .6rem;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-spark {
            flex-basis: 70px;
            max-width: 70px;
            min-width: 70px;
        }

        .home-kpi-grid .dashboard-kpi-card .kpi-spark {
            width: 70px;
            height: 28px;
            min-width: 70px;
            max-width: 70px;
            min-height: 28px;
            max-height: 28px;
        }

        .home-kpi-grid .dashboard-kpi-card .kpi-spark .apexcharts-canvas,
        .home-kpi-grid .dashboard-kpi-card .kpi-spark .apexcharts-svg,
        .home-kpi-grid .dashboard-kpi-card .kpi-spark foreignObject {
            width: 70px !important;
            min-width: 70px !important;
            max-width: 70px !important;
            height: 28px !important;
            min-height: 28px !important;
            max-height: 28px !important;
        }
    }

    @media (max-width: 991.98px) {
        .home-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .home-kpi-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .home-kpi-grid .dashboard-kpi-card {
            min-height: 92px;
            padding: 10px 12px;
        }

        .home-kpi-grid .dashboard-kpi-card .impaza-stat-spark {
            flex-basis: 62px;
            max-width: 62px;
            min-width: 62px;
        }

        .home-kpi-grid .dashboard-kpi-card .kpi-spark {
            width: 62px;
            height: 24px;
            min-width: 62px;
            max-width: 62px;
            min-height: 24px;
            max-height: 24px;
        }

        .home-kpi-grid .dashboard-kpi-card .kpi-spark .apexcharts-canvas,
        .home-kpi-grid .dashboard-kpi-card .kpi-spark .apexcharts-svg,
        .home-kpi-grid .dashboard-kpi-card .kpi-spark foreignObject {
            width: 62px !important;
            min-width: 62px !important;
            max-width: 62px !important;
            height: 24px !important;
            min-height: 24px !important;
            max-height: 24px !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $userName = auth()->user()->name ?? 'User';
    $selectedYear = ($selectedYear ?? null) === 'all' ? null : ($selectedYear ?? null);
    $selectedMonth = ($selectedMonth ?? null) === 'all' ? null : ($selectedMonth ?? null);
    $selectedRegion = ($selectedRegion ?? null) === 'all' ? null : ($selectedRegion ?? null);

    $periodLabel = $selectedYear
        ? ($selectedMonth ? (\Carbon\Carbon::create(null, (int) $selectedMonth, 1)->format('M') . ' ' . $selectedYear) : (string) $selectedYear)
        : 'All Years';

    $kpiUrlAll = route('faults.index');
    $kpiUrlOpen = route('faults.index', ['status' => 'lt4']);
    $kpiUrlRect = route('faults.index', ['status' => (int) ($rectificationId ?? 3)]);
    $kpiUrlResolved = route('faults.index', ['status' => (int) ($nocClearedId ?? 6)]);
    $kpiUrlToday = route('faults.index', ['age' => 'today']);
    $kpiUrlWaiting = route('faults.index', ['status' => (int) ($waitingAssessmentId ?? 1)]);
    $kpiUrlWithin72 = route('faults.index', ['status' => 'lt4', 'age' => 'lt72']);
    $kpiUrlOver72 = route('faults.index', ['status' => 'lt4', 'age' => 'gt72']);

    $openCount = (int) ($openFaultsCount ?? 0);
    $rectCount = (int) ($inProgressFaultsCount ?? 0);
    $resolvedCount = (int) ($resolvedFaultsCount ?? 0);
    $todayCount = (int) ($todayFaultsCount ?? 0);
    $allCount = (int) ($faultCount ?? 0);
    $waitingCount = (int) ($waitingAssessmentCount ?? 0);
    $within72Count = (int) ($within72Count ?? 0);
    $over72Count = (int) ($over72Count ?? 0);
@endphp

<section class="content ux-unified">
    <div class="container-fluid px-3 px-xl-4 py-3">
        <div class="d-flex flex-wrap align-items-start align-items-md-center justify-content-between gap-3 mb-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 border"
                        style="width:40px;height:40px;background:rgba(99,102,241,.12);border-color:rgba(99,102,241,.18) !important;color:var(--impaza-primary);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h1 class="h5 mb-0 fw-bold" style="color:var(--impaza-text);">
                            Welcome back, {{ $userName }}!
                        </h1>
                        <div class="small" style="color:var(--impaza-muted);">
                            Here's what's happening with your network today.
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill border"
                    style="background:var(--impaza-card);border-color:var(--impaza-border) !important;">
                    <i class="far fa-calendar"></i>
                    <span class="fw-semibold">{{ $periodLabel }}</span>
                    @if($selectedRegion)
                        <span class="px-2 py-1 rounded-pill"
                            style="font-size:.72rem;background:rgba(6,182,212,.12);color:var(--impaza-info);border:1px solid rgba(6,182,212,.22);">
                            {{ $selectedRegion }}
                        </span>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="collapse"
                    data-bs-target="#dashboardFilters" aria-expanded="false" aria-controls="dashboardFilters">
                    <i class="fas fa-sliders-h me-1"></i> Filters
                </button>
                <button type="button" id="homeHardRefresh" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-rotate me-1"></i> Refresh
                </button>
                @can('fault-list')
                    <a href="{{ route('faults.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Log Fault
                    </a>
                @endcan
            </div>
        </div>

        <div class="collapse mb-3" id="dashboardFilters">
            <form method="get" action="{{ route('home') }}" id="dashboardPeriodForm">
                <x-filter-bar :sticky="false">
                    <div>
                        <label class="form-label mb-1">Year</label>
                        <select name="year" class="form-select form-select-sm">
                            <option value="all" {{ $selectedYear === null ? 'selected' : '' }}>All Years</option>
                            @foreach(($availableYears ?? []) as $y)
                                <option value="{{ $y }}" {{ (string) $selectedYear === (string) $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1">Month</label>
                        <select name="month" class="form-select form-select-sm" {{ $selectedYear ? '' : 'disabled' }}>
                            <option value="all" {{ $selectedMonth === null ? 'selected' : '' }}>All Months</option>
                            @foreach(($availableMonths ?? []) as $m)
                                <option value="{{ $m }}" {{ (string) $selectedMonth === (string) $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, (int) $m, 1)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1">Region</label>
                        <select name="region" class="form-select form-select-sm">
                            <option value="all" {{ $selectedRegion === null ? 'selected' : '' }}>All Regions</option>
                            @foreach(($availableRegions ?? []) as $r)
                                <option value="{{ $r }}" {{ (string) $selectedRegion === (string) $r ? 'selected' : '' }}>
                                    {{ $r }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <x-slot name="actions">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            Reset
                        </a>
                    </x-slot>
                </x-filter-bar>
            </form>
        </div>

        <div class="mb-3 home-kpi-grid">
            <x-stat-card
                class="dashboard-kpi-card"
                title="Total Faults"
                icon="fa-layer-group"
                variant="primary"
                :value="number_format($allCount)"
                :href="$kpiUrlAll"
                :trend="$totalTrendPct"
                :trend-label="$totalTrendLabel"
            >
                <div class="kpi-spark" data-spark="logged" data-color="primary"></div>
            </x-stat-card>
            <x-stat-card
                class="dashboard-kpi-card"
                title="Under Rectification"
                icon="fa-wrench"
                variant="info"
                :value="number_format($rectCount)"
                :href="$kpiUrlRect"
                :trend="$rectTrendPct"
                :trend-label="$rectTrendLabel"
            >
                <div class="kpi-spark" data-spark="net" data-color="info"></div>
            </x-stat-card>
            <x-stat-card
                class="dashboard-kpi-card"
                title="Resolved Faults"
                icon="fa-circle-check"
                variant="success"
                :value="number_format($resolvedCount)"
                :href="$kpiUrlResolved"
                :trend="$resolvedTrendPct"
                :trend-label="$resolvedTrendLabel"
            >
                <div class="kpi-spark" data-spark="resolved" data-color="success"></div>
            </x-stat-card>
            <x-stat-card
                class="dashboard-kpi-card"
                title="Faults Today"
                icon="fa-calendar-day"
                variant="warning"
                :value="number_format($todayCount)"
                :href="$kpiUrlToday"
                :trend="$todayTrendPct"
                :trend-label="$todayTrendLabel"
            >
                <div class="kpi-spark" data-spark="logged" data-color="warning"></div>
            </x-stat-card>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ $kpiUrlOpen }}" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(99,102,241,.10);color:var(--impaza-primary);border:1px solid rgba(99,102,241,.22);">
                    Open <span class="ms-1 fw-bold">{{ $openCount }}</span>
                </a>
                <a href="{{ $kpiUrlWaiting }}" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(245,158,11,.12);color:var(--impaza-warning);border:1px solid rgba(245,158,11,.25);">
                    Waiting Assessment <span class="ms-1 fw-bold">{{ $waitingCount }}</span>
                </a>
                <a href="{{ $kpiUrlRect }}" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(6,182,212,.12);color:var(--impaza-info);border:1px solid rgba(6,182,212,.25);">
                    Under Rectification <span class="ms-1 fw-bold">{{ $rectCount }}</span>
                </a>
                <a href="{{ $kpiUrlResolved }}" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(16,185,129,.12);color:var(--impaza-success);border:1px solid rgba(16,185,129,.25);">
                    Resolved <span class="ms-1 fw-bold">{{ $resolvedCount }}</span>
                </a>
            </div>
            @can('fault-list')
                <a href="{{ route('faults.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    View all <i class="fas fa-arrow-right ms-1"></i>
                </a>
            @endcan
        </div>

        <div class="row g-3">
            <div class="col-xxl-9 col-xl-8">
                <div class="row g-3 mb-3">
                    <div class="col-xl-7">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="fw-bold">Monthly Fault Trends</div>
                                    <div class="small text-muted">Logged faults over time</div>
                                </div>
                                <select id="trendsRange" class="form-select form-select-sm"
                                    style="max-width:140px;background:var(--impaza-card);border-color:var(--impaza-border);color:var(--impaza-text);">
                                    <option value="6">Last 6 Months</option>
                                    <option value="12" selected>Last 12 Months</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="card-body">
                                <div id="apexTrends" style="min-height:300px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="fw-bold">Fault Status Distribution</div>
                                    <div class="small text-muted">Current status breakdown</div>
                                </div>
                                <span class="badge rounded-pill"
                                    style="background:rgba(16,185,129,.12);color:var(--impaza-success);border:1px solid rgba(16,185,129,.25);">
                                    Total {{ number_format(collect($statusValues ?? [])->sum() ?: 0) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div id="apexStatus" style="min-height:300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="impaza-table-card has-sticky">
                    <div class="itc-header">
                        <div>
                            <h5 class="itc-title mb-0">Recent Faults</h5>
                            <div class="itc-subtitle">Latest reported and updated faults</div>
                        </div>
                        <div class="itc-actions">
                            @can('fault-list')
                                <a href="{{ route('faults.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View all <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="itc-body is-scroll" style="max-height:460px;">
                        <div class="table-responsive border-0 rounded-0">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Ref</th>
                                        <th>Customer</th>
                                        <th>Site</th>
                                        <th>Assigned To</th>
                                        <th>Logged</th>
                                        <th>Status</th>
                                        <th>Age</th>
                                        <th>Priority</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($recentFaults ?? []) as $fault)
                                        @php
                                            $rowStatusId = (int) ($fault->status_id ?? 0);
                                            $rowStatus = trim((string) ($fault->status_description ?? ''));
                                            $statusLabel = $rowStatus !== '' ? $rowStatus : 'Unknown';
                                            $statusColor = \App\Models\Status::STATUS_COLOR[$statusLabel] ?? '#6c757d';
                                            $assignee = trim((string) ($fault->assignedTo ?? ''));
                                            $prio = trim((string) ($fault->priority ?? ''));
                                            $prioTone = strtolower($prio);
                                            $prioStyle = $prioTone === 'high' || $prioTone === 'critical' || $prioTone === 'p1'
                                                ? 'background:rgba(239,68,68,.14);color:var(--impaza-danger);border:1px solid rgba(239,68,68,.22);'
                                                : ($prioTone === 'medium' || $prioTone === 'p2'
                                                    ? 'background:rgba(245,158,11,.16);color:var(--impaza-warning);border:1px solid rgba(245,158,11,.24);'
                                                    : ($prioTone === 'low' || $prioTone === 'p3'
                                                        ? 'background:rgba(16,185,129,.14);color:var(--impaza-success);border:1px solid rgba(16,185,129,.22);'
                                                        : 'background:rgba(148,163,184,.16);color:var(--impaza-muted);border:1px solid rgba(148,163,184,.22);'));
                                            $ageStr = '—';
                                            if (!empty($fault->date_logged)) {
                                                $mins = \Carbon\Carbon::parse($fault->date_logged)->diffInMinutes(\Carbon\Carbon::now());
                                                $ageStr = (intdiv($mins, 1440) > 0 ? intdiv($mins, 1440) . 'd ' : '') . intdiv($mins % 1440, 60) . 'h ' . ($mins % 60) . 'm';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">
                                                <a href="{{ route('faults.index', ['q' => $fault->fault_ref_number]) }}">
                                                    {{ $fault->fault_ref_number }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($fault->customer ?? 'N/A', 22) }}</td>
                                            <td>{{ Str::limit($fault->link ?? '—', 26) }}</td>
                                            <td>
                                                @if($assignee !== '')
                                                    <span class="d-inline-flex align-items-center gap-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                                            style="width:26px;height:26px;background:rgba(99,102,241,.14);color:var(--impaza-primary);font-weight:800;">
                                                            {{ strtoupper(mb_substr($assignee, 0, 1)) }}
                                                        </span>
                                                        <span>{{ Str::limit($assignee, 18) }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                {{ $fault->date_logged ? \Carbon\Carbon::parse($fault->date_logged)->format('d M Y H:i') : '—' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('faults.index', ['status' => $rowStatusId]) }}">
                                                    <x-status-badge :label="$statusLabel" :color="$statusColor" />
                                                </a>
                                            </td>
                                            <td class="text-muted">{{ $ageStr }}</td>
                                            <td>
                                                @if($prio !== '')
                                                    <span class="badge rounded-pill" style="{{ $prioStyle }}">{{ $prio }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('faults.index', ['q' => $fault->fault_ref_number]) }}"
                                                    class="btn btn-sm btn-outline-primary btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
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

            <div class="col-xxl-3 col-xl-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="fw-bold">Quick Actions</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @can('fault-list')
                                <div class="col-6">
                                    <a href="{{ route('faults.index') }}" class="btn w-100 rounded-3 py-3"
                                        style="background:rgba(99,102,241,.10);color:var(--impaza-primary);border:1px solid rgba(99,102,241,.22);">
                                        <div class="mb-2"><i class="fas fa-plus"></i></div>
                                        <div class="fw-semibold" style="font-size:.78rem;">Log Fault</div>
                                    </a>
                                </div>
                            @endcan
                            @can('assign-fault')
                                <div class="col-6">
                                    <a href="{{ route('assign.create') }}" class="btn w-100 rounded-3 py-3"
                                        style="background:rgba(6,182,212,.12);color:var(--impaza-info);border:1px solid rgba(6,182,212,.25);">
                                        <div class="mb-2"><i class="fas fa-user-check"></i></div>
                                        <div class="fw-semibold" style="font-size:.78rem;">Assign</div>
                                    </a>
                                </div>
                            @endcan
                            @can('assessment-fault-list')
                                <div class="col-6">
                                    <a href="{{ route('assessments.index') }}" class="btn w-100 rounded-3 py-3"
                                        style="background:rgba(245,158,11,.12);color:var(--impaza-warning);border:1px solid rgba(245,158,11,.25);">
                                        <div class="mb-2"><i class="fas fa-clipboard-check"></i></div>
                                        <div class="fw-semibold" style="font-size:.78rem;">Assess</div>
                                    </a>
                                </div>
                            @endcan
                            @can('reports')
                                <div class="col-6">
                                    <a href="{{ route('dashboard.reports') }}" class="btn w-100 rounded-3 py-3"
                                        style="background:rgba(16,185,129,.12);color:var(--impaza-success);border:1px solid rgba(16,185,129,.25);">
                                        <div class="mb-2"><i class="fas fa-chart-bar"></i></div>
                                        <div class="fw-semibold" style="font-size:.78rem;">Reports</div>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>

                @can('fault-list')
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="fw-bold">Smart Filters</div>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('faults.index') }}">
                                <div class="mb-2">
                                    <label class="form-label mb-1">Region</label>
                                    <select name="region" class="form-select form-select-sm">
                                        <option value="">All Regions</option>
                                        @foreach(($availableRegions ?? []) as $r)
                                            <option value="{{ $r }}">{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label mb-1">Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">All Statuses</option>
                                        @foreach(($allStatuses ?? []) as $s)
                                            <option value="{{ $s->id }}">{{ $s->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label mb-1">Priority</label>
                                    <select name="priority" class="form-select form-select-sm">
                                        <option value="">All Priorities</option>
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                                    <i class="fas fa-filter me-1"></i> Apply
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan

                <div class="card mb-3">
                    <div class="card-header">
                        <div class="fw-bold">Activity Feed</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            @forelse((($recentFaults ?? collect())->take(6)) as $fault)
                                @php
                                    $afStatus = trim((string) ($fault->status_description ?? '')) ?: 'Updated';
                                    $afColor = \App\Models\Status::STATUS_COLOR[$afStatus] ?? '#6c757d';
                                @endphp
                                <a href="{{ route('faults.index', ['q' => $fault->fault_ref_number]) }}"
                                    class="d-flex align-items-start gap-2 py-2 text-decoration-none"
                                    style="border-bottom:1px solid var(--impaza-border);">
                                    <span class="d-inline-block rounded-circle"
                                        style="width:10px;height:10px;margin-top:5px;background:{{ $afColor }};"></span>
                                    <span class="d-flex flex-column" style="min-width:0;">
                                        <span class="fw-semibold"
                                            style="font-size:.8rem;color:var(--impaza-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $fault->fault_ref_number }} — {{ $afStatus }}
                                        </span>
                                        <span class="text-muted" style="font-size:.72rem;">
                                            {{ \Carbon\Carbon::parse($fault->updated_at)->diffForHumans() }}
                                        </span>
                                    </span>
                                </a>
                            @empty
                                <div class="text-muted small">No recent activity</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@php
    $techNames = collect($techResolutionAverages ?? [])->pluck('name');
    $techSecs = collect($techResolutionAverages ?? [])->pluck('avg_sec')->map(fn($s) => (int) $s);
@endphp
<div id="homeData"
    data-monthly-labels='@json($monthlyLabels ?? [])'
    data-monthly-counts='@json($monthlyCounts ?? [])'
    data-monthly-resolved='@json($monthlyResolvedCounts ?? [])'
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('dashboardPeriodForm');
    if (form) {
        var yearSelect = form.querySelector('select[name=year]');
        var monthSelect = form.querySelector('select[name=month]');
        function toggleMonth() {
            if (!yearSelect || !monthSelect) return;
            monthSelect.disabled = !yearSelect.value || yearSelect.value === 'all';
            if (monthSelect.disabled) monthSelect.value = 'all';
        }
        if (yearSelect) {
            toggleMonth();
            yearSelect.addEventListener('change', toggleMonth);
        }
    }

    var hardRefreshBtn = document.getElementById('homeHardRefresh');
    if (hardRefreshBtn) {
        hardRefreshBtn.addEventListener('click', function () {
            var url = new URL(window.location.href);
            url.searchParams.set('_hr', String(Date.now()));
            window.location.replace(url.toString());
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;
    var el = document.getElementById('homeData');
    if (!el) return;

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    var monthlyLabels = JSON.parse(el.dataset.monthlyLabels || '[]');
    var logged = JSON.parse(el.dataset.monthlyCounts || '[]');
    var resolved = JSON.parse(el.dataset.monthlyResolved || '[]');
    var statusLabels = JSON.parse(el.dataset.statusLabels || '[]');
    var statusValues = JSON.parse(el.dataset.statusValues || '[]');
    var statusOptions = JSON.parse(el.dataset.statusOptions || '[]');
    var faultsUrl = String(el.dataset.faultsUrl || '/faults');

    var COL = {
        primary: '#6366F1',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        info: '#06B6D4',
        muted: '#94A3B8',
    };

    var textColor = isDark() ? '#94A3B8' : '#64748B';
    var titleColor = isDark() ? '#E2E8F0' : '#0F172A';
    var gridColor = isDark() ? 'rgba(148,163,184,.15)' : 'rgba(148,163,184,.25)';
    var strokeBg = isDark() ? '#0F172A' : '#ffffff';

    var trendsEl = document.getElementById('apexTrends');
    if (trendsEl) {
        var full = {
            labels: monthlyLabels,
            logged: logged,
            resolved: resolved,
        };

        function sliceLast(n) {
            if (n === 'all') return full;
            var num = parseInt(String(n), 10);
            if (!num || num <= 0) return full;
            var start = Math.max(0, full.labels.length - num);
            return {
                labels: full.labels.slice(start),
                logged: full.logged.slice(start),
                resolved: full.resolved.slice(start),
            };
        }

        var initial = sliceLast(12);
        var trendsChart = new ApexCharts(trendsEl, {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif',
                zoom: { enabled: false },
            },
            series: [
                { name: 'Logged', data: initial.logged },
                { name: 'Resolved', data: initial.resolved },
            ],
            colors: [COL.primary, COL.success],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] },
            },
            xaxis: {
                categories: initial.labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: textColor } },
            },
            yaxis: { labels: { style: { colors: textColor } } },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            legend: { labels: { colors: textColor }, markers: { radius: 12 } },
            tooltip: { theme: isDark() ? 'dark' : 'light' },
        });
        trendsChart.render();

        var rangeEl = document.getElementById('trendsRange');
        if (rangeEl) {
            rangeEl.addEventListener('change', function () {
                var val = rangeEl.value;
                var s = sliceLast(val);
                trendsChart.updateOptions({
                    xaxis: { categories: s.labels },
                    series: [
                        { name: 'Logged', data: s.logged },
                        { name: 'Resolved', data: s.resolved },
                    ],
                }, false, true);
            });
        }
    }

    var statusEl = document.getElementById('apexStatus');
    if (statusEl && Array.isArray(statusValues) && statusValues.length) {
        var statusChart = new ApexCharts(statusEl, {
            chart: {
                type: 'donut',
                height: 300,
                fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif',
                events: {
                    dataPointSelection: function (_e, _ctx, cfg) {
                        var label = statusLabels[cfg.dataPointIndex];
                        var opt = statusOptions.find(function (o) {
                            return String(o.description) === String(label);
                        });
                        if (opt) {
                            window.location.href = faultsUrl + '?status=' + encodeURIComponent(opt.id);
                        }
                    },
                },
            },
            series: statusValues,
            labels: statusLabels,
            colors: [COL.primary, COL.success, COL.warning, COL.danger, COL.info, COL.muted],
            stroke: { width: 2, colors: [strokeBg] },
            dataLabels: { enabled: false },
            legend: {
                position: 'right',
                fontSize: '12px',
                labels: { colors: textColor },
                formatter: function (name, opts) {
                    var v = opts.w.globals.series[opts.seriesIndex] || 0;
                    var total = opts.w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0) || 1;
                    var pct = Math.round((v / total) * 100);
                    return name + '  ' + pct + '% (' + v + ')';
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { color: textColor },
                            value: { color: titleColor },
                            total: {
                                show: true,
                                label: 'Total',
                                color: textColor,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                                },
                            },
                        },
                    },
                },
            },
            tooltip: { theme: isDark() ? 'dark' : 'light' },
            responsive: [
                {
                    breakpoint: 1200,
                    options: { legend: { position: 'bottom' } },
                },
            ],
        });
        statusChart.render();
    }

    var net = logged.map(function (v, i) {
        var a = Number(v || 0) - Number(resolved[i] || 0);
        return a < 0 ? 0 : a;
    });

    document.querySelectorAll('.kpi-spark').forEach(function (node) {
        var which = node.getAttribute('data-spark');
        var data = which === 'resolved' ? resolved : (which === 'net' ? net : logged);
        var colorKey = node.getAttribute('data-color');
        var color = COL[colorKey] || (which === 'resolved' ? COL.success : (which === 'net' ? COL.danger : COL.primary));
        if (!data || !data.length) return;

        new ApexCharts(node, {
            chart: {
                type: 'area',
                height: 46,
                sparkline: { enabled: true },
                fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif',
            },
            series: [{ data: data }],
            stroke: { curve: 'smooth', width: 2 },
            colors: [color],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
            tooltip: { enabled: false },
        }).render();
    });
});
</script>
@endsection
