

<?php $__env->startSection('styles'); ?>
<style>
  .rpt-filter-bar { overflow: visible !important; display: block; padding: 14px 16px; }
  .rpt-top { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; }
  .rpt-presets { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
  .rpt-presets-label { font-size: .78rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .03em; margin-right: 4px; }
  .rpt-preset { font-size: .8rem; padding: 4px 12px; border-radius: 999px; border: 1px solid #dce3ee; color: #374151; background: #fff; text-decoration: none; transition: all .15s ease; white-space: nowrap; }
  .rpt-preset:hover { border-color: #93c5fd; color: #1d4ed8; background: #eff6ff; }
  .rpt-preset.active { background: #2563eb; border-color: #2563eb; color: #fff; box-shadow: 0 4px 10px rgba(37, 99, 235, .25); }
  .rpt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; align-items: end; }
  .rpt-grid .cc-field { min-width: 0; }
  .rpt-customer { grid-column: span 2; }
  .rpt-range { grid-column: span 2; }
  .rpt-range .input-group-text { font-size: .78rem; background: transparent; }
  .rpt-combo { position: relative; }
  .rpt-combo .form-control { padding-left: 30px !important; padding-right: 30px !important; }
  .rpt-combo-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .8rem; pointer-events: none; }
  .rpt-combo-clear { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: #9ca3af; padding: 2px 6px; line-height: 1; }
  .rpt-combo-clear:hover { color: #ef4444; }
  .rpt-combo-list { position: absolute; z-index: 60; left: 0; right: 0; top: calc(100% + 4px); max-height: 300px; overflow-y: auto; margin: 0; padding: 4px; list-style: none; background: #fff; border: 1px solid #dce3ee; border-radius: 10px; box-shadow: 0 12px 28px rgba(15, 23, 42, .15); }
  .rpt-combo-list li { padding: 7px 10px; border-radius: 7px; cursor: pointer; font-size: .85rem; display: flex; justify-content: space-between; gap: 10px; }
  .rpt-combo-list li small { color: #6b7280; white-space: nowrap; }
  .rpt-combo-list li.active, .rpt-combo-list li:hover { background: #eff6ff; color: #1d4ed8; }
  .rpt-combo-list li.rpt-empty { color: #6b7280; cursor: default; background: transparent; }
  .rpt-combo-list mark { padding: 0; background: #fef08a; color: inherit; }
  .rpt-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
  .rpt-chip { display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: #eef2ff; color: #4338ca; text-decoration: none; }
  .rpt-chip small { font-weight: 500; opacity: .8; }
  .rpt-chip-period { background: #ecfdf5; color: #047857; }
  a.rpt-chip:hover { background: #fee2e2; color: #b91c1c; }
  .rpt-chip-x { font-size: .7rem; opacity: .7; }
  @media (max-width: 575.98px) { .rpt-customer, .rpt-range { grid-column: auto; } }
  html[data-theme="dark"] .rpt-preset { background: #0f172a; border-color: #334155; color: #cbd5e1; }
  html[data-theme="dark"] .rpt-preset.active { background: #2563eb; border-color: #2563eb; color: #fff; }
  html[data-theme="dark"] .rpt-combo-list { background: #0f172a; border-color: #334155; }
  html[data-theme="dark"] .rpt-combo-list li { color: #e2e8f0; }
  html[data-theme="dark"] .rpt-combo-list li.active, html[data-theme="dark"] .rpt-combo-list li:hover { background: #1e293b; color: #93c5fd; }
  html[data-theme="dark"] .rpt-chip { background: rgba(99, 102, 241, .15); color: #a5b4fc; }
  html[data-theme="dark"] .rpt-chip-period { background: rgba(16, 185, 129, .15); color: #6ee7b7; }
  /* ---- drill-down ---- */
  .drill-hint { font-size: .72rem; color: #6b7280; background: #f1f5f9; border-radius: 999px; padding: 3px 10px; white-space: nowrap; }
  .drill-hint i { font-size: .65rem; margin-right: 3px; }
  .chart-header { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
  .chart-subtitle { font-size: .75rem; color: #6b7280; margin-top: 2px; }
  .is-drillable { cursor: pointer; transition: transform .15s ease, box-shadow .15s ease; }
  .is-drillable:hover, .is-drillable:focus-visible { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(37, 99, 235, .14); outline: none; }
  .drill-modal .modal-header { align-items: flex-start; border-bottom: 1px solid #eef2f7; }
  .drill-crumbs { font-size: .78rem; color: #6b7280; display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
  .drill-crumbs a { color: #2563eb; text-decoration: none; }
  .drill-crumbs a:hover { text-decoration: underline; }
  .drill-crumb-sep { font-size: .6rem; color: #cbd5e1; }
  .drill-crumb-current { color: #111827; font-weight: 600; }
  .drill-period { font-size: .75rem; font-weight: 600; color: #047857; background: #ecfdf5; border-radius: 999px; padding: 3px 10px; }
  .drill-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
  .drill-stat { border-radius: 12px; padding: 12px 14px; border: 1px solid #e5e7eb; background: #fff; }
  .drill-stat.is-clickable { cursor: pointer; }
  .drill-stat.is-clickable:hover { border-color: #93c5fd; box-shadow: 0 6px 16px rgba(37, 99, 235, .12); }
  .drill-stat-value { font-size: 1.45rem; font-weight: 800; color: #111827; line-height: 1.1; }
  .drill-stat-label { font-size: .75rem; color: #6b7280; text-transform: uppercase; letter-spacing: .03em; margin-top: 4px; }
  .ds-total { background: #eff6ff; border-color: #bfdbfe; }
  .ds-open { background: #fffbeb; border-color: #fde68a; }
  .ds-restored { background: #ecfdf5; border-color: #a7f3d0; }
  .ds-mttr { background: #f5f3ff; border-color: #ddd6fe; }
  .drill-bds { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-bottom: 16px; }
  .drill-bd { border: 1px solid #eef2f7; border-radius: 12px; padding: 10px 12px; }
  .drill-bd-title { font-size: .75rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 6px; }
  .drill-bd-row { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) auto; align-items: center; gap: 8px; padding: 4px 6px; border-radius: 8px; font-size: .8rem; }
  .drill-bd-row.is-clickable { cursor: pointer; }
  .drill-bd-row.is-clickable:hover { background: #eff6ff; }
  .drill-bd-label { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #1f2937; }
  .drill-bd-bar { height: 8px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
  .drill-bd-bar span { display: block; height: 100%; background: linear-gradient(90deg, #60a5fa, #2563eb); border-radius: 999px; }
  .drill-bd-count { font-weight: 700; color: #111827; min-width: 28px; text-align: right; }
  .drill-table-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap; }
  .drill-count { font-size: .8rem; color: #6b7280; }
  .drill-search { position: relative; width: 280px; max-width: 100%; }
  .drill-search i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .75rem; }
  .drill-search input { padding-left: 28px; border-radius: 8px; }
  .drill-table-wrap { border: 1px solid #eef2f7; border-radius: 12px; max-height: 52vh; }
  .drill-table thead th { position: sticky; top: 0; background: #f8fafc; font-size: .72rem; text-transform: uppercase; letter-spacing: .03em; color: #6b7280; border-bottom: 1px solid #e5e7eb; white-space: nowrap; z-index: 1; }
  .drill-table td { font-size: .82rem; vertical-align: top; padding-top: 8px; padding-bottom: 8px; }
  .drill-link { min-width: 200px; max-width: 280px; }
  .drill-ref { font-weight: 700; color: #1d4ed8; text-decoration: none; white-space: nowrap; }
  .drill-ref i { font-size: .6rem; opacity: .6; }
  .drill-cause.is-clickable, .drill-cust.is-clickable { cursor: pointer; border-bottom: 1px dashed #93c5fd; }
  .drill-cause.is-clickable:hover, .drill-cust.is-clickable:hover { color: #1d4ed8; }
  .drill-tag { display: inline-block; margin-top: 3px; font-size: .65rem; font-weight: 700; color: #7c3aed; background: #f5f3ff; border-radius: 999px; padding: 1px 7px; }
  .drill-status { display: inline-block; font-size: .72rem; font-weight: 700; border-radius: 999px; padding: 2px 9px; white-space: nowrap; }
  .st-restored { background: #d1fae5; color: #047857; }
  .st-closed { background: #e5e7eb; color: #4b5563; }
  .st-waiting { background: #fef3c7; color: #b45309; }
  .st-pop { background: #ede9fe; color: #6d28d9; }
  .st-escalated { background: #fee2e2; color: #b91c1c; }
  .st-progress { background: #dbeafe; color: #1d4ed8; }
  @media (max-width: 767.98px) { .drill-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  html[data-theme="dark"] .drill-stat, html[data-theme="dark"] .drill-bd, html[data-theme="dark"] .drill-table-wrap { background: #0f172a; border-color: #334155; }
  html[data-theme="dark"] .drill-stat-value, html[data-theme="dark"] .drill-bd-count, html[data-theme="dark"] .drill-bd-label, html[data-theme="dark"] .drill-crumb-current { color: #e2e8f0; }
  html[data-theme="dark"] .drill-table thead th { background: #1e293b; color: #94a3b8; border-color: #334155; }
  html[data-theme="dark"] .drill-bd-row.is-clickable:hover { background: #1e293b; }
  html[data-theme="dark"] .drill-hint { background: #1e293b; color: #94a3b8; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<link href="<?php echo e(asset('css/reports.css')); ?>?v=<?php echo e(@filemtime(public_path('css/reports.css'))); ?>" rel="stylesheet">
<link href="<?php echo e(asset('css/call_centre.css')); ?>?v=<?php echo e(@filemtime(public_path('css/call_centre.css'))); ?>" rel="stylesheet">

<section class="content ux-unified">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-white border-0 py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0 text-2xl font-bold text-gray-800">
                            <i class="fas fa-chart-line text-primary me-3"></i>
                            Operations Analytics
                        </h3>
                       <!--  <div>
                            <p class="text-sm text-gray-600 mb-0 mt-1">Comprehensive fault management and performance insights</p>
                        </div> -->
                    </div>
                    <!-- <div class="action-dropdown">
                        <button class="btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(route('dashboard.reports')); ?>"><i class="fas fa-refresh"></i> Reset Filters</a></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print"></i> Export Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download"></i> Download PDF</a></li>
                        </ul>
                    </div> -->
                </div>
            </div>
            <div class="card-body p-0">
                <?php
                    $nowF = now();
                    $keep = array_filter([
                        'region' => request('region'),
                        'impact' => request('impact'),
                        'customer_id' => optional($selectedCustomer ?? null)->id,
                    ], fn ($v) => $v !== null && $v !== '' && $v !== 'all');
                    $lastMonthF = $nowF->copy()->subMonthNoOverflow();
                    $presets = [
                        'this_month' => ['label' => 'This Month', 'params' => ['month' => $nowF->month, 'year' => $nowF->year]],
                        'last_month' => ['label' => 'Last Month', 'params' => ['month' => $lastMonthF->month, 'year' => $lastMonthF->year]],
                        'this_quarter' => ['label' => 'This Quarter', 'params' => ['quarter' => $nowF->quarter, 'year' => $nowF->year]],
                        'last_3m' => ['label' => 'Last 3 Months', 'params' => ['start_date' => $nowF->copy()->subMonthsNoOverflow(3)->toDateString(), 'end_date' => $nowF->toDateString()]],
                        'last_6m' => ['label' => 'Last 6 Months', 'params' => ['start_date' => $nowF->copy()->subMonthsNoOverflow(6)->toDateString(), 'end_date' => $nowF->toDateString()]],
                        'last_12m' => ['label' => 'Last 12 Months', 'params' => ['start_date' => $nowF->copy()->subMonthsNoOverflow(12)->toDateString(), 'end_date' => $nowF->toDateString()]],
                        'this_year' => ['label' => 'This Year', 'params' => ['month' => 'all', 'year' => $nowF->year]],
                        'all_time' => ['label' => 'All Time', 'params' => ['month' => 'all', 'year' => 'all']],
                    ];
                    $periodKeys = ['month', 'year', 'quarter', 'start_date', 'end_date'];
                    $currentPeriod = [];
                    foreach ($periodKeys as $k) {
                        $v = request($k);
                        if ($v !== null && $v !== '') { $currentPeriod[$k] = (string) $v; }
                    }
                    $activePreset = null;
                    foreach ($presets as $key => $p) {
                        $want = array_map('strval', $p['params']);
                        $cmp = $currentPeriod;
                        // "month=all" with a date range is equivalent to no month
                        if (isset($want['start_date'])) { unset($cmp['month'], $cmp['year']); }
                        if ($cmp == $want) { $activePreset = $key; break; }
                    }
                    if ($activePreset === null && empty($currentPeriod)) { $activePreset = 'this_month'; }

                    // Active filter chips (each links to the same page without that filter)
                    $chips = [];
                    $q = request()->query();
                    unset($q['_hr']);
                    if (!empty($selectedCustomer)) {
                        $chips[] = ['icon' => 'fa-building', 'text' => 'Customer: ' . $selectedCustomer->customer, 'url' => route('dashboard.reports', \Illuminate\Support\Arr::except($q, ['customer_id']))];
                    }
                    if (!empty($selectedRegion)) {
                        $chips[] = ['icon' => 'fa-map-marker-alt', 'text' => 'Region: ' . $selectedRegion, 'url' => route('dashboard.reports', \Illuminate\Support\Arr::except($q, ['region']))];
                    }
                    if (($selectedImpact ?? 'all') !== 'all') {
                        $chips[] = ['icon' => 'fa-bolt', 'text' => ($selectedImpact === 'pop' ? 'POP impacted faults' : 'Direct faults'), 'url' => route('dashboard.reports', \Illuminate\Support\Arr::except($q, ['impact']))];
                    }
                    $customerJson = $customerOptions ?? collect();
                ?>
                <div class="px-4 py-3 border-bottom rpt-filter-wrap">
                    <form method="get" action="<?php echo e(route('dashboard.reports')); ?>" class="cc-filter-bar rpt-filter-bar" id="reportsFilterForm" autocomplete="off">
                        <div class="rpt-top">
                            <div class="rpt-presets" role="group" aria-label="Quick periods">
                                <span class="rpt-presets-label"><i class="fas fa-bolt me-1"></i>Quick period</span>
                                <?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('dashboard.reports', array_merge($keep, $p['params']))); ?>"
                                       class="rpt-preset <?php echo e($activePreset === $key ? 'active' : ''); ?>"><?php echo e($p['label']); ?></a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="cc-filter-actions">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-filter me-1"></i> Apply
                                </button>
                                <a href="<?php echo e(route('dashboard.reports')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                                <button type="button" id="reportsHardRefresh" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="Reload without cache">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>

                        <div class="rpt-grid">
                            <div class="cc-field rpt-customer">
                                <label class="form-label" for="rptCustomerInput"><i class="fas fa-building me-1"></i>Customer</label>
                                <div class="rpt-combo" id="rptCustomerCombo">
                                    <i class="fas fa-search rpt-combo-icon"></i>
                                    <input type="text" id="rptCustomerInput" class="form-control form-control-sm"
                                           placeholder="Search customer name or account no."
                                           value="<?php echo e(optional($selectedCustomer ?? null)->customer); ?>"
                                           role="combobox" aria-expanded="false" aria-controls="rptCustomerList" aria-autocomplete="list">
                                    <input type="hidden" name="customer_id" id="rptCustomerId" value="<?php echo e(optional($selectedCustomer ?? null)->id); ?>">
                                    <button type="button" class="rpt-combo-clear <?php echo e(empty($selectedCustomer) ? 'd-none' : ''); ?>" id="rptCustomerClear" aria-label="Clear customer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <ul class="rpt-combo-list d-none" id="rptCustomerList" role="listbox"></ul>
                                </div>
                            </div>
                            <div class="cc-field">
                                <label class="form-label"><i class="fas fa-calendar-alt me-1"></i>Month</label>
                                <select name="month" class="form-select form-select-sm">
                                    <option value="all" <?php echo e(($selectedMonth ?? null) === null ? 'selected' : ''); ?>>All Months</option>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php echo e(($selectedMonth ?? null) == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create(null, $m)->format('F')); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="cc-field">
                                <label class="form-label"><i class="fas fa-calendar me-1"></i>Year</label>
                                <select name="year" class="form-select form-select-sm">
                                    <option value="all" <?php echo e(($selectedYear ?? null) === null ? 'selected' : ''); ?>>All Years</option>
                                    <?php $__currentLoopData = ($availableYears ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($y); ?>" <?php echo e(($selectedYear ?? null) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="cc-field">
                                <label class="form-label"><i class="fas fa-th-large me-1"></i>Quarter</label>
                                <select name="quarter" class="form-select form-select-sm">
                                    <option value="" <?php echo e(empty($selectedQuarter ?? '') ? 'selected' : ''); ?>>All Quarters</option>
                                    <?php for($qq = 1; $qq <= 4; $qq++): ?>
                                        <option value="<?php echo e($qq); ?>" <?php echo e(($selectedQuarter ?? null) == $qq ? 'selected' : ''); ?>>Q<?php echo e($qq); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="cc-field">
                                <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Region</label>
                                <select name="region" class="form-select form-select-sm">
                                    <option value="" <?php echo e(empty($selectedRegion ?? '') ? 'selected' : ''); ?>>All Regions</option>
                                    <?php $__currentLoopData = ($availableRegions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($r); ?>" <?php echo e((($selectedRegion ?? '') === $r) ? 'selected' : ''); ?>><?php echo e($r); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="cc-field">
                                <label class="form-label"><i class="fas fa-bolt me-1"></i>Impact</label>
                                <select name="impact" class="form-select form-select-sm">
                                    <option value="all" <?php echo e(($selectedImpact ?? 'all') === 'all' ? 'selected' : ''); ?>>All Faults</option>
                                    <option value="direct" <?php echo e(($selectedImpact ?? 'all') === 'direct' ? 'selected' : ''); ?>>Direct Faults</option>
                                    <option value="pop" <?php echo e(($selectedImpact ?? 'all') === 'pop' ? 'selected' : ''); ?>>POP Impacted Faults</option>
                                </select>
                            </div>
                            <div class="cc-field rpt-range">
                                <label class="form-label"><i class="fas fa-calendar-week me-1"></i>Custom date range</label>
                                <div class="input-group input-group-sm">
                                    <input type="date" name="start_date" id="rptStartDate" class="form-control form-control-sm" value="<?php echo e($startDate ?? request('start_date')); ?>" max="<?php echo e(now()->toDateString()); ?>" aria-label="Start date">
                                    <span class="input-group-text">to</span>
                                    <input type="date" name="end_date" id="rptEndDate" class="form-control form-control-sm" value="<?php echo e($endDate ?? request('end_date')); ?>" max="<?php echo e(now()->toDateString()); ?>" aria-label="End date">
                                </div>
                            </div>
                        </div>

                        <div class="rpt-chips">
                            <span class="rpt-chip rpt-chip-period"><i class="fas fa-calendar-check"></i>
                                <?php echo e(($periodStart ?? now())->format('d M Y')); ?> – <?php echo e(($periodEnd ?? now())->format('d M Y')); ?>

                                <small>(<?php echo e($periodLabelText ?? 'Current period'); ?>)</small>
                            </span>
                            <?php $__currentLoopData = $chips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a class="rpt-chip" href="<?php echo e($chip['url']); ?>" title="Remove this filter">
                                    <i class="fas <?php echo e($chip['icon']); ?>"></i> <?php echo e($chip['text']); ?> <i class="fas fa-times rpt-chip-x"></i>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </form>
                </div>

                <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
                    <div class="kpi-grid">
                        <div class="kpi-card kpi-secondary">
                            <div class="kpi-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-label">Reporting Period</div>
                                <div class="kpi-value">
                                    <?php echo e(($periodStart ?? now())->format('d M Y')); ?> — <?php echo e(($periodEnd ?? now())->format('d M Y')); ?>

                                </div>
                                <div class="kpi-trend">
                                    <span class="trend-period">
                                        <?php echo e($periodLabelText ?? 'Current period'); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="kpi-card kpi-primary" data-drill='{}' data-drill-label="All faults in this period" title="See these faults">
                            <div class="kpi-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value"><?php echo e(number_format($faultsThisMonth)); ?></div>
                                <div class="kpi-label">New Faults</div>
                                <div class="kpi-trend">
                                    <span class="trend-period">Current period</span>
                                </div>
                            </div>
                        </div>
                
                        <div class="kpi-card kpi-info" data-drill='{"status_group":"restored"}' data-drill-label="Restored faults" title="See restored faults">
                            <div class="kpi-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value"><?php echo e($slaCompliance); ?>%</div>
                                <div class="kpi-label">SLA Compliance</div>
                                <div class="kpi-trend">
                                    <span class="trend-period">Current period</span>
                                </div>
                            </div>
                        </div>
                
                        <div class="kpi-card kpi-warning" data-drill='{"status_group":"restored"}' data-drill-label="Restored faults" title="See restored faults and restore times">
                            <div class="kpi-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value"><?php echo e(number_format($mttrThisMonth / 3600, 1)); ?>h</div>
                                <div class="kpi-label">Avg MTTR</div>
                                <div class="kpi-trend">
                                    <span class="trend-period">Current period</span>
                                </div>
                            </div>
                        </div>
                
                        <!-- <div class="kpi-card kpi-danger">
                            <div class="kpi-icon">
                                <i class="fas fa-redo"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value"><?php echo e($reopenRate); ?>%</div>
                                <div class="kpi-label">Reopen Rate</div>
                                <div class="kpi-trend">
                                    <span class="trend-neutral">
                                        <i class="fas fa-info-circle"></i>
                                        Quality Metric
                                    </span>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Service Reliability Trends</h2>
                    <div class="w-full mb-8">
                        <div class="chart-card chart-large cc-chart-card">
                            <div class="chart-header">
                                <h3>Fault Volume Trend (12 Months)</h3>
                                <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click to drill down</span>
                            </div>
                            <div class="chart-body">
                                <canvas id="chartMonthlyFaults"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Fault Category Analysis</h2>
                    <div class="charts-grid-secondary">
                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <h3>Direct vs POP Impacted</h3>
                                <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click to drill down</span>
                            </div>
                            <div class="chart-body chart-body-lg">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge badge-info">Total: <?php echo e(number_format($faultCategoryTotal ?? 0)); ?></span>
                                    <span class="badge badge-primary">Direct: <?php echo e(number_format($directFaultCount ?? 0)); ?></span>
                                    <span class="badge badge-success">POP Impacted: <?php echo e(number_format($popImpactedCount ?? 0)); ?></span>
                                </div>
                                <canvas id="chartFaultCategorySplit"></canvas>
                            </div>
                        </div>
                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <h3>Category Trend (12 Months)</h3>
                                <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click to drill down</span>
                            </div>
                            <div class="chart-body">
                                <canvas id="chartFaultCategoryTrend"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Root Cause & Impact Analysis</h2>
                    <div class="charts-grid-secondary">
                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <div><h3>Top Root Causes (Confirmed RFO)</h3><div class="chart-subtitle">Ranked by number of faults · top 10, the rest grouped as Other</div></div>
                                <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click to drill down</span>
                            </div>
                            <div class="chart-body chart-body-lg">
                                <canvas id="chartRFO"></canvas>
                            </div>
                        </div>

                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <h3>Geographic Distribution (By Region)</h3>
                                <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click to drill down</span>
                            </div>
                            <div class="chart-body">
                                <canvas id="chartCityFaults"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4">
                    <div class="tables-section">
                        <h2 class="section-title text-lg font-bold text-gray-700 mb-3 px-1">Key Accounts Status</h2>
                        
                        <div class="tables-grid">
                            <div class="data-table-card">
                                <div class="table-header">
                                    <h3>Top Impacted Customers</h3>
                                    <span class="drill-hint"><i class="fas fa-mouse-pointer"></i> Click a customer</span>
                                    <div class="table-actions"></div>
                                </div>
                                <div class="table-body">
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Links</th>
                                                    <th>Open Faults</th>
                                                    <th>Recent RFOs</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $portfolioRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr class="clickable-row" data-drill-customer="<?php echo e($row['customer_id'] ?? 0); ?>" data-drill-customer-name="<?php echo e($row['customer']); ?>" title="See this customer's faults">
                                                        <td>
                                                            <div class="customer-info">
                                                                <div class="customer-avatar"><?php echo e(substr($row['customer'], 0, 2)); ?></div>
                                                                <span><?php echo e($row['customer']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-info"><?php echo e($row['links']); ?></span></td>
                                                        <td><span class="badge badge-warning"><?php echo e($row['open_faults']); ?></span></td>
                                                        <td><span class="badge badge-danger"><?php echo e($row['recent_rfos']); ?></span></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr><td colspan="4" class="no-data">No data available</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="data-table-card">
                                <div class="table-header">
                                    <h3>Churn Risk (Increasing Faults)</h3>
                                    <div class="risk-indicator high">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Attention Needed
                                    </div>
                                </div>
                                <div class="table-body">
                                    <div class="table-responsive">
                                        <table class="modern-table">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Fault Increase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $churnRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr class="clickable-row" data-drill-customer="<?php echo e($row['customer_id'] ?? 0); ?>" data-drill-customer-name="<?php echo e($row['customer']); ?>" title="See this customer's faults">
                                                        <td>
                                                            <div class="customer-info">
                                                                <div class="customer-avatar"><?php echo e(substr($row['customer'], 0, 2)); ?></div>
                                                                <span><?php echo e($row['customer']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge badge-danger">+<?php echo e($row['delta']); ?> Faults</span></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr><td colspan="2" class="no-data">No high-risk customers detected</td></tr>
                                                <?php endif; ?>
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

<div class="modal fade drill-modal" id="drillModal" tabindex="-1" aria-labelledby="drillTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex-grow-1 min-w-0">
                    <div class="drill-crumbs" id="drillCrumbs"></div>
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                        <button type="button" class="btn btn-sm btn-light drill-back d-none" id="drillBack" title="Back"><i class="fas fa-arrow-left"></i></button>
                        <h5 class="modal-title mb-0" id="drillTitle">Faults</h5>
                        <span class="drill-period" id="drillPeriod"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="drillLoading" class="text-center text-muted py-5">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading faults…
                </div>
                <div id="drillError" class="alert alert-danger d-none"></div>
                <div id="drillContent" class="d-none">
                    <div class="drill-stats" id="drillStats"></div>
                    <div class="drill-bds" id="drillBreakdowns"></div>
                    <div class="drill-table-head">
                        <div class="drill-count" id="drillCount"></div>
                        <div class="drill-search">
                            <i class="fas fa-search"></i>
                            <input type="search" id="drillSearch" class="form-control form-control-sm" placeholder="Filter these faults…">
                        </div>
                    </div>
                    <div class="table-responsive drill-table-wrap">
                        <table class="table table-sm align-middle drill-table mb-0">
                            <thead>
                                <tr>
                                    <th>Ref</th><th>Customer</th><th>Link / location</th><th>Root cause</th><th>Status</th><th>Reported</th><th>Duration</th>
                                </tr>
                            </thead>
                            <tbody id="drillRows"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Click a root cause, customer or region to drill further. Click a reference to open it in Faults.</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="drillCsv"><i class="fas fa-file-csv me-1"></i>Download CSV</button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="customerRootCauseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerRootCauseTitle">Root Causes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="customerRootCauseMeta" class="text-muted small mb-2"></div>
                <div id="customerRootCauseLoading" class="py-3">Loading...</div>
                <div id="customerRootCauseBody" style="display:none;">
                    <div style="height: 420px;">
                        <canvas id="customerRootCauseChart"></canvas>
                    </div>
                    <div id="customerRootCauseList" class="mt-3"></div>
                </div>
                <div id="customerRootCauseError" class="text-danger py-2" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data payload for charts -->
<div id="reportsData" style="display:none"
     data-monthly-labels='<?php echo json_encode($monthlyLabels, 15, 512) ?>'
     data-monthly-counts='<?php echo json_encode($monthlyCounts, 15, 512) ?>'
     data-fault-category-labels='<?php echo json_encode($faultCategoryLabels ?? [], 15, 512) ?>'
     data-fault-category-values='<?php echo json_encode($faultCategoryValues ?? [], 15, 512) ?>'
     data-fault-category-monthly-labels='<?php echo json_encode($faultCategoryMonthlyLabels ?? [], 15, 512) ?>'
     data-fault-category-monthly-direct='<?php echo json_encode($faultCategoryMonthlyDirect ?? [], 15, 512) ?>'
     data-fault-category-monthly-pop='<?php echo json_encode($faultCategoryMonthlyPop ?? [], 15, 512) ?>'
     data-rfo-labels='<?php echo json_encode($rfoLabels, 15, 512) ?>'
     data-rfo-values='<?php echo json_encode($rfoValues, 15, 512) ?>'
     data-rfo-ids='<?php echo json_encode($rfoIds ?? [], 15, 512) ?>'
     data-monthly-keys='<?php echo json_encode($monthlyKeys ?? [], 15, 512) ?>'
     data-drill-context='<?php echo json_encode($drillContext ?? [], 15, 512) ?>'
     data-drill-url="<?php echo e(route('dashboard.reports.drilldown')); ?>"
     data-city-faults-labels='<?php echo json_encode($cityFaultsLabels, 15, 512) ?>'
     data-city-faults-values='<?php echo json_encode($cityFaultsValues, 15, 512) ?>'></div>

<div id="reportsMeta" style="display:none" data-customer-rootcause-url="<?php echo e(route('dashboard.reports.customer-root-causes')); ?>"></div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('js/reports-drill.js')); ?>?v=<?php echo e(@filemtime(public_path('js/reports-drill.js'))); ?>"></script>
<script>
  // Customer search (type-ahead over all customers; selecting one applies the filter)
  (function () {
    var customers = <?php echo json_encode($customerJson, 15, 512) ?>;
    var input = document.getElementById('rptCustomerInput');
    var hidden = document.getElementById('rptCustomerId');
    var list = document.getElementById('rptCustomerList');
    var clearBtn = document.getElementById('rptCustomerClear');
    var combo = document.getElementById('rptCustomerCombo');
    if (!input || !hidden || !list) return;
    var form = input.form;
    var activeIdx = -1, shown = [];

    function esc(t) { return String(t).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }
    function hl(text, q) {
      if (!q) return esc(text);
      var i = text.toLowerCase().indexOf(q.toLowerCase());
      if (i < 0) return esc(text);
      return esc(text.slice(0, i)) + '<mark>' + esc(text.slice(i, i + q.length)) + '</mark>' + esc(text.slice(i + q.length));
    }
    function open(v) { list.classList.toggle('d-none', !v); input.setAttribute('aria-expanded', v ? 'true' : 'false'); }
    function render() {
      var q = input.value.trim().toLowerCase();
      shown = customers.filter(function (c) {
        return !q || c.name.toLowerCase().indexOf(q) !== -1 || (c.account && c.account.toLowerCase().indexOf(q) !== -1);
      }).slice(0, 50);
      activeIdx = shown.length ? 0 : -1;
      if (!shown.length) {
        list.innerHTML = '<li class="rpt-empty">No customer matches "' + esc(input.value.trim()) + '"</li>';
      } else {
        list.innerHTML = shown.map(function (c, i) {
          return '<li role="option" data-i="' + i + '" class="' + (i === activeIdx ? 'active' : '') + '"><span>' + hl(c.name, input.value.trim()) + '</span>' +
            (c.account ? '<small>' + hl(c.account, input.value.trim()) + '</small>' : '') + '</li>';
        }).join('');
      }
      open(true);
    }
    function setActive(i) {
      var items = list.querySelectorAll('li[data-i]');
      if (!items.length) return;
      activeIdx = (i + items.length) % items.length;
      items.forEach(function (li, k) { li.classList.toggle('active', k === activeIdx); });
      items[activeIdx].scrollIntoView({ block: 'nearest' });
    }
    function choose(c) {
      hidden.value = c ? c.id : '';
      input.value = c ? c.name : '';
      open(false);
      if (form) form.submit();
    }

    input.addEventListener('focus', function () { input.select(); render(); });
    input.addEventListener('input', render);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown') { e.preventDefault(); if (list.classList.contains('d-none')) render(); else setActive(activeIdx + 1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(activeIdx - 1); }
      else if (e.key === 'Enter') { e.preventDefault(); if (activeIdx >= 0 && shown[activeIdx]) choose(shown[activeIdx]); }
      else if (e.key === 'Escape') { open(false); input.blur(); }
    });
    list.addEventListener('mousedown', function (e) {
      var li = e.target.closest('li[data-i]');
      if (!li) return;
      e.preventDefault();
      choose(shown[+li.getAttribute('data-i')]);
    });
    document.addEventListener('click', function (e) {
      if (!combo.contains(e.target)) {
        open(false);
        // restore the selected name if the user typed but didn't pick
        var sel = customers.find(function (c) { return String(c.id) === String(hidden.value); });
        input.value = sel ? sel.name : '';
      }
    });
    if (clearBtn) clearBtn.addEventListener('click', function () { choose(null); });

    // Custom range: apply as soon as both dates are set (and clear month/quarter so they don't conflict)
    var sd = document.getElementById('rptStartDate'), ed = document.getElementById('rptEndDate');
    function maybeApply() {
      if (sd && ed && sd.value && ed.value && form) {
        if (sd.value > ed.value) { var t = sd.value; sd.value = ed.value; ed.value = t; }
        ['month', 'quarter', 'year'].forEach(function (n) { var el = form.elements[n]; if (el) el.disabled = true; });
        form.submit();
      }
    }
    if (sd) sd.addEventListener('change', maybeApply);
    if (ed) ed.addEventListener('change', maybeApply);
    // Picking a month / year / quarter replaces any custom date range
    ['month', 'year', 'quarter'].forEach(function (n) {
      var el = form && form.elements[n];
      if (el) el.addEventListener('change', function () { if (sd) sd.value = ''; if (ed) ed.value = ''; }, true);
    });
  })();
</script>
<script>
  (function () {
    var btn = document.getElementById('reportsHardRefresh');
    if (btn) {
      btn.addEventListener('click', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('_hr', String(Date.now()));
        window.location.replace(url.toString());
      });
    }
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard/reports.blade.php ENDPATH**/ ?>