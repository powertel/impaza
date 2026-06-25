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

    .dashboard-panel,
    .dashboard-side-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        margin: 0;
        background: var(--impaza-card);
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, .9);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 10px 28px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .dashboard-panel .card-header,
    .dashboard-side-card .card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        min-height: 68px;
        border-bottom: 1px solid rgba(226, 232, 240, .85);
        background: rgba(248, 250, 252, .82);
    }

    html[data-theme="dark"] .dashboard-panel,
    html[data-theme="dark"] .dashboard-side-card {
        border-color: rgba(30, 41, 59, .95);
        box-shadow: 0 1px 2px rgba(2, 6, 23, .35), 0 10px 28px rgba(2, 6, 23, .35);
    }

    html[data-theme="dark"] .dashboard-panel .card-header,
    html[data-theme="dark"] .dashboard-side-card .card-header {
        background: rgba(15, 23, 42, .86);
        border-bottom-color: rgba(30, 41, 59, .9);
    }

    .dashboard-card-copy {
        min-width: 0;
    }

    .dashboard-card-title {
        font-size: .92rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--impaza-text);
        margin: 0;
    }

    .dashboard-card-subtitle {
        font-size: .72rem;
        color: var(--impaza-muted);
        margin-top: 3px;
        line-height: 1.35;
    }

    .dashboard-panel .card-body,
    .dashboard-side-card .card-body {
        padding: 16px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .dashboard-panel .card-body {
        flex: 1 1 auto;
    }

    .dashboard-panel,
    .dashboard-side-card,
    .impaza-table-card.dashboard-panel {
        position: relative;
        isolation: isolate;
    }

    .dashboard-side-card .card-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .dashboard-main-row {
        align-items: stretch;
    }

    .dashboard-main-row > [class*="col-"] {
        min-width: 0;
    }

    .dashboard-main-column,
    .dashboard-side-column {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        min-width: 0;
    }

    .dashboard-charts-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, .95fr);
        gap: 12px;
        align-items: stretch;
        width: 100%;
        min-width: 0;
    }

    .dashboard-chart-col {
        display: flex;
        width: 100%;
        min-width: 0;
    }

    .dashboard-side-stack {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
        width: 100%;
        min-width: 0;
    }

    .dashboard-main-column > *,
    .dashboard-side-column > *,
    .dashboard-charts-grid > *,
    .dashboard-side-stack > * {
        width: 100%;
        min-width: 0;
        max-width: 100%;
    }

    .dashboard-side-stack > .dashboard-side-card {
        flex: 0 0 auto;
        margin-bottom: 0 !important;
    }

    .dashboard-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        border: 1px solid var(--impaza-border);
        background: var(--impaza-card);
        color: var(--impaza-muted);
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .fault-distribution-layout {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
        min-height: 300px;
    }

    .fault-distribution-chart {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-width: 0;
    }

    .fault-distribution-chart #apexStatus {
        width: min(100%, 320px);
        min-height: 260px;
    }

    .fault-distribution-legend {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 16px;
        width: 100%;
        min-width: 0;
    }

    .fault-legend-item {
        display: grid;
        grid-template-columns: 12px minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 8px 10px;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 12px;
        background: rgba(148, 163, 184, .06);
        cursor: pointer;
        transition: border-color .18s ease, background .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    .fault-legend-item:hover,
    .fault-legend-item:focus-visible {
        background: rgba(99, 102, 241, .08);
        border-color: rgba(99, 102, 241, .28);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
        transform: translateY(-1px);
        outline: none;
    }

    .fault-legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
    }

    .fault-legend-label {
        font-size: .92rem;
        color: var(--impaza-text);
        font-weight: 500;
        min-width: 0;
        line-height: 1.25;
        word-break: keep-all;
    }

    .fault-legend-value {
        font-size: .86rem;
        color: var(--impaza-muted);
        font-weight: 600;
        white-space: nowrap;
    }

    .recent-status-link {
        display: inline-flex;
        text-decoration: none;
    }

    .recent-status-link:hover,
    .recent-status-link:focus-visible {
        text-decoration: none;
        outline: none;
    }

    .recent-status-link .impaza-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .01em;
        white-space: nowrap;
        box-shadow: none;
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }

    .recent-status-link:hover .impaza-badge,
    .recent-status-link:focus-visible .impaza-badge {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, .08);
        filter: saturate(1.05);
    }

    .dashboard-side-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .dashboard-action-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .dashboard-action-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 74px;
        border-radius: 14px;
        text-decoration: none;
        border: 1px solid var(--impaza-border);
        background: var(--impaza-card);
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }

    .dashboard-action-tile:hover {
        transform: translateY(-2px);
        box-shadow: var(--impaza-shadow-sm);
        text-decoration: none;
    }

    .dashboard-action-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .82rem;
    }

    .dashboard-action-label {
        font-size: .73rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .dashboard-activity-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 0;
        text-decoration: none;
        border-bottom: 1px solid rgba(226, 232, 240, .75);
    }

    .dashboard-activity-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    html[data-theme="dark"] .dashboard-activity-item {
        border-bottom-color: rgba(30, 41, 59, .8);
    }

    .dashboard-filter-grid {
        display: grid;
        gap: 10px;
    }

    .dashboard-panel .itc-header,
    .dashboard-panel .itc-body,
    .dashboard-panel .table-responsive,
    .dashboard-panel .table {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .dashboard-panel .table-responsive {
        overflow-x: auto;
        overflow-y: auto;
    }

    .dashboard-panel .itc-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        min-height: 68px;
        border-bottom: 1px solid rgba(226, 232, 240, .85);
        background: rgba(248, 250, 252, .82);
    }

    html[data-theme="dark"] .dashboard-panel .itc-header {
        background: rgba(15, 23, 42, .86);
        border-bottom-color: rgba(30, 41, 59, .9);
    }

    @media (max-width: 1199.98px) {
        .dashboard-charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .fault-distribution-layout {
            gap: 12px;
        }

        .fault-distribution-legend {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .fault-distribution-chart #apexStatus {
            width: min(100%, 280px);
        }
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

        <div class="row g-3 dashboard-main-row">
            <div class="col-xxl-9 col-xl-8">
                <div class="dashboard-main-column">
                <div class="dashboard-charts-grid">
                    <div class="dashboard-chart-col">
                        <div class="card h-100 dashboard-panel">
                            <div class="card-header">
                                <div class="dashboard-card-copy">
                                    <div class="dashboard-card-title">Fault Trends</div>
                                    <div class="dashboard-card-subtitle">Fault resolution patterns over time</div>
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
                    <div class="dashboard-chart-col">
                        <div class="card h-100 dashboard-panel">
                            <div class="card-header">
                                <div class="dashboard-card-copy">
                                    <div class="dashboard-card-title">Fault Status Distribution</div>
                                    <div class="dashboard-card-subtitle">Current status breakdown</div>
                                </div>
                                <span class="dashboard-chip" style="color:var(--impaza-success);">
                                    Total {{ number_format(collect($statusValues ?? [])->sum() ?: 0) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="fault-distribution-layout">
                                    <div class="fault-distribution-chart">
                                        <div id="apexStatus"></div>
                                    </div>
                                    <div id="statusLegend" class="fault-distribution-legend"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="impaza-table-card has-sticky dashboard-panel">
                    <div class="itc-header">
                        <div class="dashboard-card-copy">
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
                                            $statusDisplayLabel = match (strtolower($statusLabel)) {
                                                'fault has been restored', 'resolved' => 'Fault Restored',
                                                'fault is under rectification', 'under rectification' => 'Under Rectification',
                                                'waiting for assessment', 'waiting assessment' => 'Waiting Assessment',
                                                'fault has been assessed', 'assessed' => 'Assessed',
                                                'open' => 'Open',
                                                'fault has been rectified', 'rectified' => 'Rectified',
                                                'fault has been cleared by ct', 'cleared by ct' => 'Cleared by CT',
                                                'fault has been refered', 'fault has been referred', 'referred' => 'Referred',
                                                'fault has been parked', 'parked' => 'Parked',
                                                'fault has been revoked', 'revoked' => 'Revoked',
                                                'fault  escalated to chief technician', 'fault escalated to chief technician', 'escalated to chief technician' => 'Escalated',
                                                'impacted by pop outage' => 'POP Outage',
                                                default => $statusLabel,
                                            };
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
                                                <a href="{{ route('faults.index', ['status' => $rowStatusId]) }}" class="recent-status-link">
                                                    <x-status-badge :label="$statusDisplayLabel" :color="$statusColor" :soft="true" />
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
            </div>

            <div class="col-xxl-3 col-xl-4">
                <div class="dashboard-side-column">
                <div class="dashboard-side-stack">
                @canany(['fault-list', 'assign-fault', 'assessment-fault-list', 'reports'])
                <div class="card dashboard-side-card">
                    <div class="card-header">
                        <div class="dashboard-card-copy">
                            <div class="dashboard-card-title">Quick Actions</div>
                            <div class="dashboard-card-subtitle">Only actions you can access</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-action-grid">
                            @can('fault-list')
                                <a href="{{ route('faults.index') }}" class="dashboard-action-tile"
                                    style="color:var(--impaza-primary);background:rgba(99,102,241,.08);border-color:rgba(99,102,241,.18);">
                                    <span class="dashboard-action-icon" style="background:rgba(99,102,241,.12);color:var(--impaza-primary);">
                                        <i class="fas fa-plus-circle"></i>
                                    </span>
                                    <span class="dashboard-action-label">Log Fault</span>
                                </a>
                            @endcan
                            @can('assign-fault')
                                <a href="{{ route('assign.create') }}" class="dashboard-action-tile"
                                    style="color:var(--impaza-info);background:rgba(6,182,212,.10);border-color:rgba(6,182,212,.20);">
                                    <span class="dashboard-action-icon" style="background:rgba(6,182,212,.12);color:var(--impaza-info);">
                                        <i class="fas fa-user-check"></i>
                                    </span>
                                    <span class="dashboard-action-label">Assign Fault</span>
                                </a>
                            @endcan
                            @can('assessment-fault-list')
                                <a href="{{ route('assessments.index') }}" class="dashboard-action-tile"
                                    style="color:var(--impaza-warning);background:rgba(245,158,11,.10);border-color:rgba(245,158,11,.22);">
                                    <span class="dashboard-action-icon" style="background:rgba(245,158,11,.12);color:var(--impaza-warning);">
                                        <i class="fas fa-clipboard-check"></i>
                                    </span>
                                    <span class="dashboard-action-label">Assess</span>
                                </a>
                            @endcan
                            @can('reports')
                                <a href="{{ route('dashboard.reports') }}" class="dashboard-action-tile"
                                    style="color:var(--impaza-success);background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.20);">
                                    <span class="dashboard-action-icon" style="background:rgba(16,185,129,.12);color:var(--impaza-success);">
                                        <i class="fas fa-chart-bar"></i>
                                    </span>
                                    <span class="dashboard-action-label">Report</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                @endcanany

                <div class="card dashboard-side-card">
                    <div class="card-header">
                        <div class="dashboard-card-copy">
                            <div class="dashboard-card-title">Activity Feed</div>
                            <div class="dashboard-card-subtitle">Recent changes and updates</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            @forelse((($recentFaults ?? collect())->take(6)) as $fault)
                                @php
                                    $afStatus = trim((string) ($fault->status_description ?? '')) ?: 'Updated';
                                    $afColor = \App\Models\Status::STATUS_COLOR[$afStatus] ?? '#6c757d';
                                @endphp
                                <a href="{{ route('faults.index', ['q' => $fault->fault_ref_number]) }}"
                                    class="dashboard-activity-item">
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

    function compactStatusLabel(label) {
        var normalized = String(label || '').trim().toLowerCase();
        if (normalized === 'fault has been restored' || normalized === 'resolved') return 'Resolved';
        if (normalized === 'fault is under rectification' || normalized === 'under rectification') return 'Under Rectification';
        if (normalized === 'fault has been assessed' || normalized === 'assessed') return 'Assessed';
        if (normalized === 'waiting for assessment' || normalized === 'waiting assessment') return 'Waiting Assessment';
        if (normalized === 'open' || normalized === 'all open faults') return 'Open';
        if (normalized === 'fault has been rectified' || normalized === 'rectified') return 'Rectified';
        if (normalized === 'fault has been cleared by ct' || normalized === 'cleared by ct') return 'Cleared by CT';
        if (normalized === 'fault has been refered' || normalized === 'fault has been referred' || normalized === 'referred') return 'Referred';
        if (normalized === 'fault has been parked' || normalized === 'parked') return 'Parked';
        if (normalized === 'fault has been revoked' || normalized === 'revoked') return 'Revoked';
        if (normalized === 'fault  escalated to chief technician' || normalized === 'fault escalated to chief technician' || normalized === 'escalated to chief technician') return 'Escalated';
        if (normalized === 'impacted by pop outage') return 'POP Outage';
        return String(label || 'Other');
    }

    var statusDisplayLabels = statusLabels.map(compactStatusLabel);

    function goToStatus(index) {
        var label = statusLabels[index];
        var opt = statusOptions.find(function (o) {
            return String(o.description) === String(label);
        });
        if (opt) {
            window.location.href = faultsUrl + '?status=' + encodeURIComponent(opt.id);
        }
    }

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
        var legendEl = document.getElementById('statusLegend');
        var statusChart = new ApexCharts(statusEl, {
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif',
                events: {
                    dataPointSelection: function (_e, _ctx, cfg) {
                        goToStatus(cfg.dataPointIndex);
                    },
                },
            },
            series: statusValues,
            labels: statusDisplayLabels,
            colors: [COL.primary, COL.success, COL.warning, COL.danger, COL.info, COL.muted],
            stroke: { width: 0, colors: ['transparent'] },
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: {
                pie: {
                    expandOnClick: false,
                    offsetY: 8,
                    donut: {
                        size: '64%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                color: textColor,
                                offsetY: -10,
                            },
                            value: {
                                show: true,
                                color: titleColor,
                                fontSize: '38px',
                                fontWeight: 700,
                                offsetY: 10,
                                formatter: function (_value, opts) {
                                    return opts.w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                                },
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                color: textColor,
                                fontSize: '16px',
                                fontWeight: 600,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                                },
                            },
                        },
                    },
                },
            },
            tooltip: { theme: isDark() ? 'dark' : 'light' },
        });
        statusChart.render();

        if (legendEl) {
            var total = statusValues.reduce(function (a, b) { return a + b; }, 0) || 1;
            legendEl.innerHTML = statusDisplayLabels.map(function (label, index) {
                var value = Number(statusValues[index] || 0);
                var pct = ((value / total) * 100).toFixed(1);
                var color = [COL.primary, COL.success, COL.warning, COL.danger, COL.info, COL.muted][index] || COL.muted;
                return [
                    '<div class="fault-legend-item" role="button" tabindex="0" data-status-index="' + index + '" aria-label="View ' + label + ' faults">',
                    '<span class="fault-legend-dot" style="background:' + color + ';"></span>',
                    '<span class="fault-legend-label">' + label + '</span>',
                    '<span class="fault-legend-value">' + pct + '% (' + value + ')</span>',
                    '</div>'
                ].join('');
            }).join('');

            legendEl.querySelectorAll('.fault-legend-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    goToStatus(Number(item.dataset.statusIndex));
                });
                item.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        goToStatus(Number(item.dataset.statusIndex));
                    }
                });
            });
        }
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
