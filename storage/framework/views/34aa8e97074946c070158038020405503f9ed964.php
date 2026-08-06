

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
                <div class="bg-gray-50 px-4 py-3 border-bottom">
                    <form method="get" action="<?php echo e(route('dashboard.reports')); ?>" class="cc-filter-bar d-flex flex-nowrap align-items-end justify-content-between gap-3" id="reportsFilterForm">
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
                            <select name="month" class="form-select form-select-sm">
                                <option value="all" <?php echo e(($selectedMonth ?? null) === null ? 'selected' : ''); ?>>All Months</option>
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo e($m); ?>" <?php echo e(($selectedMonth ?? null) == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create(null, $m)->format('F')); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-calendar me-1"></i>Year</label>
                            <select name="year" class="form-select form-select-sm">
                                <option value="all" <?php echo e(($selectedYear ?? null) === null ? 'selected' : ''); ?>>All Years</option>
                                <?php $__currentLoopData = ($availableYears ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($y); ?>" <?php echo e(($selectedYear ?? null) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-clock me-1"></i>Quarter</label>
                            <select name="quarter" class="form-select form-select-sm">
                                <option value="" <?php echo e(empty($selectedQuarter ?? '') ? 'selected' : ''); ?>>All Quarters</option>
                                <option value="1" <?php echo e(($selectedQuarter ?? null) == 1 ? 'selected' : ''); ?>>Q1</option>
                                <option value="2" <?php echo e(($selectedQuarter ?? null) == 2 ? 'selected' : ''); ?>>Q2</option>
                                <option value="3" <?php echo e(($selectedQuarter ?? null) == 3 ? 'selected' : ''); ?>>Q3</option>
                                <option value="4" <?php echo e(($selectedQuarter ?? null) == 4 ? 'selected' : ''); ?>>Q4</option>
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
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-play-circle me-1"></i>Start Date</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo e($startDate ?? request('start_date')); ?>">
                        </div>
                        <div class="cc-field">
                            <label class="form-label"><i class="far fa-stop-circle me-1"></i>End Date</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo e($endDate ?? request('end_date')); ?>">
                        </div>
                        <div class="cc-filter-actions ms-auto">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                                <i class="fas fa-filter me-1"></i>
                                Apply
                            </button>
                            <a href="<?php echo e(route('dashboard.reports')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="fas fa-undo me-1"></i>
                                Reset
                            </a>
                            <button type="button" id="reportsHardRefresh" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                <i class="fas fa-sync-alt me-1"></i>
                                Hard Refresh
                            </button>
                        </div>
                    </form>
                </div>

                <div class="px-4 py-4 bg-gradient-to-r from-gray-50 to-white">
                    <div class="kpi-grid">
                        <div class="kpi-card kpi-secondary">
                            <div class="kpi-icon">
                                <i class="far fa-calendar-alt"></i>
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
                        <div class="kpi-card kpi-primary">
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
                
                        <div class="kpi-card kpi-info">
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
                
                        <div class="kpi-card kpi-warning">
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
                                <h3>Top Root Causes (Confirmed RFO)</h3>
                            </div>
                            <div class="chart-body chart-body-lg">
                                <canvas id="chartRFO"></canvas>
                            </div>
                        </div>

                        <div class="chart-card cc-chart-card">
                            <div class="chart-header">
                                <h3>Geographic Distribution (By Region)</h3>
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
                                                    <tr class="clickable-row js-customer-rootcause" data-customer-id="<?php echo e($row['customer_id'] ?? 0); ?>">
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
                                                    <tr class="clickable-row js-customer-rootcause" data-customer-id="<?php echo e($row['customer_id'] ?? 0); ?>">
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
     data-city-faults-labels='<?php echo json_encode($cityFaultsLabels, 15, 512) ?>'
     data-city-faults-values='<?php echo json_encode($cityFaultsValues, 15, 512) ?>'></div>

<div id="reportsMeta" style="display:none" data-customer-rootcause-url="<?php echo e(route('dashboard.reports.customer-root-causes')); ?>"></div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/reports.js')); ?>?v=<?php echo e(@filemtime(public_path('js/reports.js'))); ?>"></script>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard/reports.blade.php ENDPATH**/ ?>