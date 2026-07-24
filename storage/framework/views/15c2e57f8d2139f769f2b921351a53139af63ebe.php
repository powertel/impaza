<?php $__env->startSection('title'); ?>
Dashboard
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .home-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
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

    .dashboard-header-split {
        align-items: center !important;
    }

    .dashboard-header-split .dashboard-card-copy {
        flex: 1 1 auto;
    }

    .dashboard-header-action {
        margin-left: auto;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .dashboard-header-action .form-select {
        width: 180px;
        max-width: 100%;
        background: var(--impaza-card);
        border-color: var(--impaza-border);
        color: var(--impaza-text);
        box-shadow: none;
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
        --bs-gutter-x: 16px;
        --bs-gutter-y: 16px;
    }

    .dashboard-main-row > [class*="col-"] {
        min-width: 0;
    }

    .dashboard-main-column,
    .dashboard-side-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
        min-width: 0;
    }

    .dashboard-charts-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, .95fr);
        gap: 16px;
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
        gap: 16px;
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

    .ux-unified .impaza-table-card .table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--impaza-text);
        --bs-table-hover-bg: rgba(99, 102, 241, .06);
        --bs-table-hover-color: var(--impaza-text);
        margin-bottom: 0;
        color: var(--impaza-text);
    }

    .ux-unified .impaza-table-card .table tbody > tr > * { background-color: transparent; }

    html[data-theme="dark"] .ux-unified .impaza-table-card .itc-body {
        background: var(--impaza-card);
    }

    html[data-theme="dark"] .ux-unified .impaza-table-card .table thead th {
        background: rgba(15, 23, 42, .86);
        border-bottom-color: rgba(30, 41, 59, .9);
        color: rgba(148, 163, 184, .95);
    }

    html[data-theme="dark"] .ux-unified .impaza-table-card .table tbody td {
        border-color: rgba(30, 41, 59, .75);
        color: rgba(226, 232, 240, .92);
    }

    html[data-theme="dark"] .ux-unified .impaza-table-card .table tbody tr:hover > * {
        background-color: rgba(99, 102, 241, .08);
    }

    html[data-theme="dark"] .ux-unified .impaza-table-card .text-muted {
        color: rgba(148, 163, 184, .92) !important;
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

        .dashboard-header-split {
            align-items: flex-start !important;
        }

        .dashboard-header-action {
            width: 100%;
            margin-left: 0;
            justify-content: flex-start;
        }

        .dashboard-header-action .form-select {
            width: 100%;
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
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
?>

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
                            Welcome back, <?php echo e($userName); ?>!
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
                    <span class="fw-semibold"><?php echo e($periodLabel); ?></span>
                    <?php if($selectedRegion): ?>
                        <span class="px-2 py-1 rounded-pill"
                            style="font-size:.72rem;background:rgba(6,182,212,.12);color:var(--impaza-info);border:1px solid rgba(6,182,212,.22);">
                            <?php echo e($selectedRegion); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="collapse"
                    data-bs-target="#dashboardFilters" aria-expanded="false" aria-controls="dashboardFilters">
                    <i class="fas fa-sliders-h me-1"></i> Filters
                </button>
                <button type="button" id="homeHardRefresh" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-rotate me-1"></i> Refresh
                </button>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
                    <a href="<?php echo e(route('faults.index')); ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> Log Fault
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="collapse mb-3" id="dashboardFilters">
            <form method="get" action="<?php echo e(route('home')); ?>" id="dashboardPeriodForm">
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['sticky' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['sticky' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
                    <div>
                        <label class="form-label mb-1">Year</label>
                        <select name="year" class="form-select form-select-sm">
                            <option value="all" <?php echo e($selectedYear === null ? 'selected' : ''); ?>>All Years</option>
                            <?php $__currentLoopData = ($availableYears ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($y); ?>" <?php echo e((string) $selectedYear === (string) $y ? 'selected' : ''); ?>>
                                    <?php echo e($y); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1">Month</label>
                        <select name="month" class="form-select form-select-sm" <?php echo e($selectedYear ? '' : 'disabled'); ?>>
                            <option value="all" <?php echo e($selectedMonth === null ? 'selected' : ''); ?>>All Months</option>
                            <?php $__currentLoopData = ($availableMonths ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m); ?>" <?php echo e((string) $selectedMonth === (string) $m ? 'selected' : ''); ?>>
                                    <?php echo e(\Carbon\Carbon::create(null, (int) $m, 1)->format('F')); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1">Region</label>
                        <select name="region" class="form-select form-select-sm">
                            <option value="all" <?php echo e($selectedRegion === null ? 'selected' : ''); ?>>All Regions</option>
                            <?php $__currentLoopData = ($availableRegions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($r); ?>" <?php echo e((string) $selectedRegion === (string) $r ? 'selected' : ''); ?>>
                                    <?php echo e($r); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                     <?php $__env->slot('actions', null, []); ?> 
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            Reset
                        </a>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            </form>
        </div>

        <div class="mb-3 home-kpi-grid">
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['class' => 'dashboard-kpi-card','title' => 'Total Faults','icon' => 'fa-layer-group','variant' => 'primary','value' => number_format($allCount),'href' => $kpiUrlAll,'trend' => $totalTrendPct,'trendLabel' => $totalTrendLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'dashboard-kpi-card','title' => 'Total Faults','icon' => 'fa-layer-group','variant' => 'primary','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($allCount)),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpiUrlAll),'trend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalTrendPct),'trend-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalTrendLabel)]); ?>
                <div class="kpi-spark" data-spark="logged" data-color="primary"></div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['class' => 'dashboard-kpi-card','title' => 'Under Rectification','icon' => 'fa-wrench','variant' => 'info','value' => number_format($rectCount),'href' => $kpiUrlRect,'trend' => $rectTrendPct,'trendLabel' => $rectTrendLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'dashboard-kpi-card','title' => 'Under Rectification','icon' => 'fa-wrench','variant' => 'info','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($rectCount)),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpiUrlRect),'trend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rectTrendPct),'trend-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rectTrendLabel)]); ?>
                <div class="kpi-spark" data-spark="net" data-color="info"></div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['class' => 'dashboard-kpi-card','title' => 'Resolved Faults','icon' => 'fa-circle-check','variant' => 'success','value' => number_format($resolvedCount),'href' => $kpiUrlResolved,'trend' => $resolvedTrendPct,'trendLabel' => $resolvedTrendLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'dashboard-kpi-card','title' => 'Resolved Faults','icon' => 'fa-circle-check','variant' => 'success','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($resolvedCount)),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpiUrlResolved),'trend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($resolvedTrendPct),'trend-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($resolvedTrendLabel)]); ?>
                <div class="kpi-spark" data-spark="resolved" data-color="success"></div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['class' => 'dashboard-kpi-card','title' => 'Faults Today','icon' => 'fa-calendar-day','variant' => 'warning','value' => number_format($todayCount),'href' => $kpiUrlToday,'trend' => $todayTrendPct,'trendLabel' => $todayTrendLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'dashboard-kpi-card','title' => 'Faults Today','icon' => 'fa-calendar-day','variant' => 'warning','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($todayCount)),'href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kpiUrlToday),'trend' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($todayTrendPct),'trend-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($todayTrendLabel)]); ?>
                <div class="kpi-spark" data-spark="logged" data-color="warning"></div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?php echo e($kpiUrlOpen); ?>" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(99,102,241,.10);color:var(--impaza-primary);border:1px solid rgba(99,102,241,.22);">
                    Open <span class="ms-1 fw-bold"><?php echo e($openCount); ?></span>
                </a>
                <a href="<?php echo e($kpiUrlWaiting); ?>" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(245,158,11,.12);color:var(--impaza-warning);border:1px solid rgba(245,158,11,.25);">
                    Waiting Assessment <span class="ms-1 fw-bold"><?php echo e($waitingCount); ?></span>
                </a>
                <a href="<?php echo e($kpiUrlRect); ?>" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(6,182,212,.12);color:var(--impaza-info);border:1px solid rgba(6,182,212,.25);">
                    Under Rectification <span class="ms-1 fw-bold"><?php echo e($rectCount); ?></span>
                </a>
                <a href="<?php echo e($kpiUrlResolved); ?>" class="btn btn-sm rounded-pill px-3"
                    style="background:rgba(16,185,129,.12);color:var(--impaza-success);border:1px solid rgba(16,185,129,.25);">
                    Resolved <span class="ms-1 fw-bold"><?php echo e($resolvedCount); ?></span>
                </a>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
                <a href="<?php echo e(route('faults.index')); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    View all <i class="fas fa-arrow-right ms-1"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="row g-3 dashboard-main-row">
            <div class="col-xxl-9 col-xl-8">
                <div class="dashboard-main-column">
                <div class="dashboard-charts-grid">
                    <div class="dashboard-chart-col">
                        <div class="card h-100 dashboard-panel">
                            <div class="card-header dashboard-header-split">
                                <div class="dashboard-card-copy">
                                    <div class="dashboard-card-title">Fault Trends</div>
                                    <div class="dashboard-card-subtitle">Fault resolution patterns over time</div>
                                </div>
                              <!--   <div class="dashboard-header-action">
                                    <select id="trendsRange" class="form-select form-select-sm">
                                        <option value="6">Last 6 Months</option>
                                        <option value="12" selected>Last 12 Months</option>
                                        <option value="all">All</option>
                                    </select>
                                </div> -->
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
                                    Total <?php echo e(number_format(collect($statusValues ?? [])->sum() ?: 0)); ?>

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
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
                                <a href="<?php echo e(route('faults.index')); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View all <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            <?php endif; ?>
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
                                    <?php $__empty_1 = true; $__currentLoopData = ($recentFaults ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
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
                                        ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                <a href="<?php echo e(route('faults.index', ['q' => $fault->fault_ref_number])); ?>">
                                                    <?php echo e($fault->fault_ref_number); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e(Str::limit($fault->customer ?? 'N/A', 22)); ?></td>
                                            <td><?php echo e(Str::limit($fault->link ?? '—', 26)); ?></td>
                                            <td>
                                                <?php if($assignee !== ''): ?>
                                                    <span class="d-inline-flex align-items-center gap-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                                            style="width:26px;height:26px;background:rgba(99,102,241,.14);color:var(--impaza-primary);font-weight:800;">
                                                            <?php echo e(strtoupper(mb_substr($assignee, 0, 1))); ?>

                                                        </span>
                                                        <span><?php echo e(Str::limit($assignee, 18)); ?></span>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted">
                                                <?php echo e($fault->date_logged ? \Carbon\Carbon::parse($fault->date_logged)->format('d M Y H:i') : '—'); ?>

                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('faults.index', ['status' => $rowStatusId])); ?>" class="recent-status-link">
                                                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['label' => $statusDisplayLabel,'color' => $statusColor,'soft' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusDisplayLabel),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusColor),'soft' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                                                </a>
                                            </td>
                                            <td class="text-muted"><?php echo e($ageStr); ?></td>
                                            <td>
                                                <?php if($prio !== ''): ?>
                                                    <span class="badge rounded-pill" style="<?php echo e($prioStyle); ?>"><?php echo e($prio); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?php echo e(route('faults.index', ['q' => $fault->fault_ref_number])); ?>"
                                                    class="btn btn-sm btn-outline-primary btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                No recent faults found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['fault-list', 'assign-fault', 'assessment-fault-list', 'reports'])): ?>
                <div class="card dashboard-side-card">
                    <div class="card-header">
                        <div class="dashboard-card-copy">
                            <div class="dashboard-card-title">Quick Actions</div>
                            <div class="dashboard-card-subtitle">Only actions you can access</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-action-grid">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
                                <a href="<?php echo e(route('faults.index')); ?>" class="dashboard-action-tile"
                                    style="color:var(--impaza-primary);background:rgba(99,102,241,.08);border-color:rgba(99,102,241,.18);">
                                    <span class="dashboard-action-icon" style="background:rgba(99,102,241,.12);color:var(--impaza-primary);">
                                        <i class="fas fa-plus-circle"></i>
                                    </span>
                                    <span class="dashboard-action-label">Log Fault</span>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign-fault')): ?>
                                <a href="<?php echo e(route('assign.create')); ?>" class="dashboard-action-tile"
                                    style="color:var(--impaza-info);background:rgba(6,182,212,.10);border-color:rgba(6,182,212,.20);">
                                    <span class="dashboard-action-icon" style="background:rgba(6,182,212,.12);color:var(--impaza-info);">
                                        <i class="fas fa-user-check"></i>
                                    </span>
                                    <span class="dashboard-action-label">Assign Fault</span>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assessment-fault-list')): ?>
                                <a href="<?php echo e(route('assessments.index')); ?>" class="dashboard-action-tile"
                                    style="color:var(--impaza-warning);background:rgba(245,158,11,.10);border-color:rgba(245,158,11,.22);">
                                    <span class="dashboard-action-icon" style="background:rgba(245,158,11,.12);color:var(--impaza-warning);">
                                        <i class="fas fa-clipboard-check"></i>
                                    </span>
                                    <span class="dashboard-action-label">Assess</span>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports')): ?>
                                <a href="<?php echo e(route('dashboard.reports')); ?>" class="dashboard-action-tile"
                                    style="color:var(--impaza-success);background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.20);">
                                    <span class="dashboard-action-icon" style="background:rgba(16,185,129,.12);color:var(--impaza-success);">
                                        <i class="fas fa-chart-bar"></i>
                                    </span>
                                    <span class="dashboard-action-label">Report</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card dashboard-side-card">
                    <div class="card-header">
                        <div class="dashboard-card-copy">
                            <div class="dashboard-card-title">Activity Feed</div>
                            <div class="dashboard-card-subtitle">Recent changes and updates</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <?php $__empty_1 = true; $__currentLoopData = (($recentFaults ?? collect())->take(6)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $afStatus = trim((string) ($fault->status_description ?? '')) ?: 'Updated';
                                    $afColor = \App\Models\Status::STATUS_COLOR[$afStatus] ?? '#6c757d';
                                ?>
                                <a href="<?php echo e(route('faults.index', ['q' => $fault->fault_ref_number])); ?>"
                                    class="dashboard-activity-item">
                                    <span class="d-inline-block rounded-circle"
                                        style="width:10px;height:10px;margin-top:5px;background:<?php echo e($afColor); ?>;"></span>
                                    <span class="d-flex flex-column" style="min-width:0;">
                                        <span class="fw-semibold"
                                            style="font-size:.8rem;color:var(--impaza-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            <?php echo e($fault->fault_ref_number); ?> — <?php echo e($afStatus); ?>

                                        </span>
                                        <span class="text-muted" style="font-size:.72rem;">
                                            <?php echo e(\Carbon\Carbon::parse($fault->updated_at)->diffForHumans()); ?>

                                        </span>
                                    </span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-muted small">No recent activity</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    $techNames = collect($techResolutionAverages ?? [])->pluck('name');
    $techSecs = collect($techResolutionAverages ?? [])->pluck('avg_sec')->map(fn($s) => (int) $s);
?>
<div id="homeData"
    data-monthly-labels='<?php echo json_encode($monthlyLabels ?? [], 15, 512) ?>'
    data-monthly-counts='<?php echo json_encode($monthlyCounts ?? [], 15, 512) ?>'
    data-monthly-resolved='<?php echo json_encode($monthlyResolvedCounts ?? [], 15, 512) ?>'
    data-status-labels='<?php echo json_encode($statusLabels ?? [], 15, 512) ?>'
    data-status-values='<?php echo json_encode($statusValues ?? [], 15, 512) ?>'
    data-status-options='<?php echo json_encode(($allStatuses ?? collect())->map(fn($s)=>["id"=>$s->id, "description"=>$s->description])->values(), 512) ?>'
    data-selected-year='<?php echo json_encode($selectedYear ?? null, 15, 512) ?>'
    data-selected-month='<?php echo json_encode($selectedMonth ?? null, 15, 512) ?>'
    data-faults-url='<?php echo e(route('faults.index')); ?>'
    data-reports-url='<?php echo e(route('dashboard.reports')); ?>'
    data-tech-labels='<?php echo json_encode($techNames ?? [], 15, 512) ?>'
    data-tech-values='<?php echo json_encode($techSecs ?? [], 15, 512) ?>'
    data-top-customer-labels='<?php echo json_encode($topCustomerLabels ?? [], 15, 512) ?>'
    data-top-customer-values='<?php echo json_encode($topCustomerCounts ?? [], 15, 512) ?>'
    style="display:none"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo $__env->make('partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

              <!--   return [
                    '<div class="fault-legend-item" role="button" tabindex="0" data-status-index="' + index + '" aria-label="View ' + label + ' faults">',
                    '<span class="fault-legend-dot" style="background:' + color + ';"></span>',
                    '<span class="fault-legend-label">' + label + '</span>',
                    '<span class="fault-legend-value">' + pct + '% (' + value + ')</span>',
                    '</div>' 
                ].join(''); -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/home.blade.php ENDPATH**/ ?>