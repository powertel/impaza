

<?php $__env->startSection('title'); ?>
Faults Analytics Dashboard
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<link href="<?php echo e(asset('css/call_centre.css')); ?>?v=<?php echo e(@filemtime(public_path('css/call_centre.css'))); ?>" rel="stylesheet">
<section class="content ux-unified">
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
        <form method="get" action="<?php echo e(route('call_centre.reports')); ?>" class="cc-filter-bar cc-filter-bar--two-rows d-flex flex-wrap align-items-end gap-3">
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-sliders-h me-1"></i>Time Period</label>
            <select name="filter" class="form-select form-select-sm" title="Select filter type">
              <option value="month" <?php echo e(($filter ?? 'month') === 'month' ? 'selected' : ''); ?>>Monthly</option>
              <option value="year" <?php echo e(($filter ?? '') === 'year' ? 'selected' : ''); ?>>Yearly</option>
              <option value="weekly" <?php echo e(($filter ?? '') === 'weekly' ? 'selected' : ''); ?>>Weekly Range</option>
              <option value="quarter" <?php echo e(($filter ?? '') === 'quarter' ? 'selected' : ''); ?>>Quarterly</option>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-calendar-alt me-1"></i>Month</label>
            <select name="month" class="form-select form-select-sm" title="Choose month">
              <?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e(($selectedMonth ?? 0) == $m ? 'selected' : ''); ?>><?php echo e(\Carbon\Carbon::create(null, $m)->format('F')); ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-calendar me-1"></i>Year</label>
            <select name="year" class="form-select form-select-sm" title="Choose year">
              <option value="all" <?php echo e((($filter ?? '') === 'year' && strtolower((string)request('year')) === 'all') ? 'selected' : ''); ?>>All Years</option>
              <?php $__currentLoopData = ($availableYears ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($y); ?>" <?php echo e(($selectedYear ?? 0) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-clock me-1"></i>Quarter</label>
            <select name="quarter" class="form-select form-select-sm" title="Choose quarter">
              <option value="1" <?php echo e(($quarter ?? 1) == 1 ? 'selected' : ''); ?>>Q1</option>
              <option value="2" <?php echo e(($quarter ?? 1) == 2 ? 'selected' : ''); ?>>Q2</option>
              <option value="3" <?php echo e(($quarter ?? 1) == 3 ? 'selected' : ''); ?>>Q3</option>
              <option value="4" <?php echo e(($quarter ?? 1) == 4 ? 'selected' : ''); ?>>Q4</option>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-network-wired me-1"></i>Impact Type</label>
            <select name="impact" class="form-select form-select-sm" title="Choose impact type">
              <option value="all" <?php echo e(($impact ?? 'all') === 'all' ? 'selected' : ''); ?>>All</option>
              <option value="direct" <?php echo e(($impact ?? '') === 'direct' ? 'selected' : ''); ?>>Direct</option>
              <option value="pop" <?php echo e(($impact ?? '') === 'pop' ? 'selected' : ''); ?>>POP Impacted</option>
            </select>
          </div>
          <div class="w-100"></div>
          <div class="cc-field">
            <label class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Region</label>
            <select name="region" class="form-select form-select-sm" title="Choose region">
              <option value="" <?php echo e(empty($selectedRegion ?? '') ? 'selected' : ''); ?>>All Regions</option>
              <?php $__currentLoopData = ($availableRegions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($r); ?>" <?php echo e((($selectedRegion ?? '') === $r) ? 'selected' : ''); ?>><?php echo e($r); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-play-circle me-1"></i>Start Date</label>
            <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="form-control form-control-sm" title="Start date for weekly range" />
          </div>
          <div class="cc-field">
            <label class="form-label"><i class="far fa-stop-circle me-1"></i>End Date</label>
            <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="form-control form-control-sm" title="End date for weekly range" />
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
              <div class="cc-kpi-value"><?php echo e(($periodStart ?? now())->format('d M Y')); ?> — <?php echo e(($periodEnd ?? now())->format('d M Y')); ?></div>
              <div class="cc-kpi-sub"><?php echo e($periodLabelText ?? 'Selected period'); ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--blue h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-bolt"></i></div>
                <div class="cc-kpi-title">New Faults</div>
              </div>
              <div class="cc-kpi-value"><?php echo e(number_format($newFaultsTotal ?? 0)); ?></div>
              <div class="cc-kpi-sub"><?php echo e($periodLabelText ?? 'Period total'); ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--green h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-check-circle"></i></div>
                <div class="cc-kpi-title">Resolved Faults</div>
              </div>
              <div class="cc-kpi-value"><?php echo e(number_format($resolvedTotal ?? 0)); ?></div>
              <div class="cc-kpi-sub"><?php echo e($periodLabelText ?? 'Period total'); ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="cc-kpi cc-kpi--indigo h-100">
              <div class="cc-kpi-head">
                <div class="cc-kpi-icon"><i class="fas fa-stopwatch"></i></div>
                <div class="cc-kpi-title">Resolved in ≤72h</div>
              </div>
              <div class="cc-kpi-value"><?php echo e(number_format($within3DaysPercent ?? 0, 2)); ?>%</div>
              <div class="cc-kpi-sub"><?php echo e($periodLabelText ?? 'Period total'); ?></div>
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
                <span class="badge rounded-pill bg-primary-subtle text-primary">Total: <?php echo e(number_format($newFaultsTotal ?? 0)); ?></span>
                <span class="badge rounded-pill bg-primary">Direct: <?php echo e(number_format($directFaultsTotal ?? 0)); ?></span>
                <span class="badge rounded-pill bg-success">POP Impacted: <?php echo e(number_format($popImpactedFaultsTotal ?? 0)); ?></span>
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
                <?php $ff = $filter ?? 'month'; ?>
                <div class="fw-semibold"><?php echo e($ff === 'year' ? 'Monthly Analysis' : ($ff === 'weekly' ? 'Daily Analysis' : 'Weekly Analysis')); ?></div>
                <div class="text-muted small"><?php echo e($ff === 'year' ? 'Balances and performance by month' : ($ff === 'weekly' ? 'Balances and performance by day' : 'Balances and performance by week')); ?></div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <?php $ff = $filter ?? 'month'; ?>
                  <table class="table align-middle mb-0 cc-analysis-table">
                    <thead>
                      <tr>
                        <th><?php echo e($ff === 'year' ? 'Month' : ($ff === 'weekly' ? 'Day' : 'Week')); ?></th>
                        <th>Opening Balance</th>
                        <th>New Faults Received</th>
                        <th>Total Faults</th>
                        <th>Resolved Faults</th>
                        <th>Closing Balance – Pending Faults</th>
                        <th>Resolved Within 72hrs</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if($ff === 'year'): ?>
                        <?php $labels = ($monthlyActiveLabels ?? $monthlyLabels ?? []); ?>
                        <?php $__currentLoopData = ($labels); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $ml): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php
                            $opening = (int)(($monthlyOpeningActive[$i] ?? null) ?? ($monthlyOpening[$i] ?? 0));
                            $new = (int)(($monthlyNewFaultsActive[$i] ?? null) ?? ($monthlyNewFaults[$i] ?? 0));
                            $resolved = (int)(($monthlyResolvedActive[$i] ?? null) ?? ($monthlyResolved[$i] ?? 0));
                            $closing = (int)(($monthlyOutstandingActive[$i] ?? null) ?? ($monthlyOutstanding[$i] ?? 0));
                            $totalMonth = (int)(($monthlyTotalsActive[$i] ?? null) ?? ($monthlyTotals[$i] ?? ($opening + $new)));
                            $perc = (int) round((($monthlyResolved3DaysPercActive[$i] ?? null) ?? ($monthlyResolved3DaysPerc[$i] ?? 0)));
                          ?>
                          <tr>
                            <td><div class="fw-semibold text-gray-800"><?php echo e($ml); ?></div></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format($opening)); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format($new)); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format($totalMonth)); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format($resolved)); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format($closing)); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e($perc); ?>%</span></td>
                          </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php elseif($ff === 'weekly'): ?>
                        <?php $__currentLoopData = ($dailyLabels ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php ($perc = (int) round(($dailyResolved3DaysPerc[$i] ?? 0))); ?>
                          <tr>
                            <td><div class="fw-semibold text-gray-800"><?php echo e($day); ?></div></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($dailyOpening[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($dailyNewFaults[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($dailyTotals[$i] ?? (($dailyOpening[$i] ?? 0) + ($dailyNewFaults[$i] ?? 0))))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($dailyResolved[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($dailyOutstanding[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e($perc); ?>%</span></td>
                          </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php else: ?>
                        <?php $__currentLoopData = ($weeklyLabels ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $wk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php ($perc = (int) round(($weeklyResolved3DaysPerc[$i] ?? 0))); ?>
                          <tr>
                            <td><div class="fw-semibold text-gray-800"><?php echo e($wk); ?></div></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($weeklyOpening[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($weeklyNewFaults[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($weeklyTotals[$i] ?? (($weeklyOpening[$i] ?? 0) + ($weeklyNewFaults[$i] ?? 0))))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($weeklyResolved[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e(number_format(($weeklyOutstanding[$i] ?? 0))); ?></span></td>
                            <td><span class="badge rounded-pill bg-warning-subtle text-dark"><?php echo e($perc); ?>%</span></td>
                          </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->startSection('scripts'); ?>
<script>
  window.callCentreData = {
    filter: <?php echo json_encode($filter ?? 'month', 15, 512) ?>,
    categoryLabels: <?php echo json_encode(['Direct Faults', 'POP Impacted Faults'], 512) ?>,
    categoryValues: <?php echo json_encode([(int)($directFaultsTotal ?? 0), (int)($popImpactedFaultsTotal ?? 0)], 512) ?>,
    weeklyLabels: <?php echo json_encode($weeklyLabels ?? [], 15, 512) ?>,
    weeklyNewFaults: <?php echo json_encode($weeklyNewFaults ?? [], 15, 512) ?>,
    weeklyNewFaultsDirect: <?php echo json_encode($weeklyNewFaultsDirect ?? [], 15, 512) ?>,
    weeklyNewFaultsPop: <?php echo json_encode($weeklyNewFaultsPop ?? [], 15, 512) ?>,
    weeklyResolved: <?php echo json_encode($weeklyResolved ?? [], 15, 512) ?>,
    weeklyOutstanding: <?php echo json_encode($weeklyOutstanding ?? [], 15, 512) ?>,
    weeklyResolved3DaysPerc: <?php echo json_encode($weeklyResolved3DaysPerc ?? [], 15, 512) ?>,
    resolvedBins: <?php echo json_encode($resolvedBins ?? [], 15, 512) ?>,
    outstandingBins: <?php echo json_encode($outstandingBins ?? [], 15, 512) ?>,
    dailyLabels: <?php echo json_encode($dailyLabels ?? [], 15, 512) ?>,
    dailyNewFaults: <?php echo json_encode($dailyNewFaults ?? [], 15, 512) ?>,
    dailyNewFaultsDirect: <?php echo json_encode($dailyNewFaultsDirect ?? [], 15, 512) ?>,
    dailyNewFaultsPop: <?php echo json_encode($dailyNewFaultsPop ?? [], 15, 512) ?>,
    dailyResolved: <?php echo json_encode($dailyResolved ?? [], 15, 512) ?>,
    dailyOutstanding: <?php echo json_encode($dailyOutstanding ?? [], 15, 512) ?>,
    dailyResolved3DaysPerc: <?php echo json_encode($dailyResolved3DaysPerc ?? [], 15, 512) ?>,
    dailyShiftMorning: <?php echo json_encode($dailyShiftMorning ?? [], 15, 512) ?>,
    dailyShiftAfternoon: <?php echo json_encode($dailyShiftAfternoon ?? [], 15, 512) ?>,
    dailyShiftNight: <?php echo json_encode($dailyShiftNight ?? [], 15, 512) ?>,
    weeklyShiftMorning: <?php echo json_encode($weeklyShiftMorning ?? [], 15, 512) ?>,
    weeklyShiftAfternoon: <?php echo json_encode($weeklyShiftAfternoon ?? [], 15, 512) ?>,
    weeklyShiftNight: <?php echo json_encode($weeklyShiftNight ?? [], 15, 512) ?>,
    weeklyRangeStarts: <?php echo json_encode($weeklyRangeStarts ?? [], 15, 512) ?>,
    weeklyRangeEnds: <?php echo json_encode($weeklyRangeEnds ?? [], 15, 512) ?>,
    monthlyLabels: <?php echo json_encode(($monthlyActiveLabels ?? $monthlyLabels) ?? [], 15, 512) ?>,
    monthlyOpening: <?php echo json_encode(($monthlyOpeningActive ?? $monthlyOpening) ?? [], 15, 512) ?>,
    monthlyNewFaults: <?php echo json_encode(($monthlyNewFaultsActive ?? $monthlyNewFaults) ?? [], 15, 512) ?>,
    monthlyNewFaultsDirect: <?php echo json_encode(($monthlyNewFaultsDirectActive ?? $monthlyNewFaultsDirect) ?? [], 15, 512) ?>,
    monthlyNewFaultsPop: <?php echo json_encode(($monthlyNewFaultsPopActive ?? $monthlyNewFaultsPop) ?? [], 15, 512) ?>,
    monthlyResolved: <?php echo json_encode(($monthlyResolvedActive ?? $monthlyResolved) ?? [], 15, 512) ?>,
    monthlyOutstanding: <?php echo json_encode(($monthlyOutstandingActive ?? $monthlyOutstanding) ?? [], 15, 512) ?>,
    monthlyTotals: <?php echo json_encode(($monthlyTotalsActive ?? $monthlyTotals) ?? [], 15, 512) ?>,
    monthlyResolved3DaysPerc: <?php echo json_encode(($monthlyResolved3DaysPercActive ?? $monthlyResolved3DaysPerc) ?? [], 15, 512) ?>,
    monthlyShiftMorning: <?php echo json_encode(($monthlyShiftMorningActive ?? $monthlyShiftMorning) ?? [], 15, 512) ?>,
    monthlyShiftAfternoon: <?php echo json_encode(($monthlyShiftAfternoonActive ?? $monthlyShiftAfternoon) ?? [], 15, 512) ?>,
    monthlyShiftNight: <?php echo json_encode(($monthlyShiftNightActive ?? $monthlyShiftNight) ?? [], 15, 512) ?>,
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
<script src="<?php echo e(asset('js/call_centre.js')); ?>?v=<?php echo e(file_exists(public_path('js/call_centre.js')) ? filemtime(public_path('js/call_centre.js')) : time()); ?>"></script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/call_centre/reports.blade.php ENDPATH**/ ?>