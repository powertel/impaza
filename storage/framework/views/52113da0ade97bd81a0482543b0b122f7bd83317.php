<?php
    $title = 'Faults Export';
    $search = trim((string) ($filters['search'] ?? ''));
    $statusFilter = (string) ($filters['status'] ?? 'all');
    $ageFilter = (string) ($filters['age'] ?? 'all');

    $statusLabel = match ($statusFilter) {
        'lt4' => 'Open Faults',
        'all', '' => 'All Statuses',
        default => 'Status #' . $statusFilter,
    };

    $ageLabel = match ($ageFilter) {
        'today' => 'Today',
        'lt72' => 'Within 72 Hours',
        'gt72' => 'Over 72 Hours',
        default => 'All Ages',
    };

    $assignedCount = $faults->filter(fn ($fault) => !empty($fault->assignedTo))->count();
    $unassignedCount = max($faults->count() - $assignedCount, 0);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <style>
        @page {
            margin: 18px 20px 22px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            background: #ffffff;
        }

        .page {
            width: 100%;
        }

        .header {
            border: 1px solid #dbe5f3;
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);
            margin-bottom: 12px;
        }

        .header-table,
        .stats-table,
        .filters-table,
        .faults-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f3d91;
            margin: 0 0 4px;
        }

        .header-subtitle {
            font-size: 10px;
            color: #4b5563;
        }

        .header-meta {
            text-align: right;
            font-size: 10px;
            color: #334155;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 8px;
        }

        .stats-row td {
            width: 25%;
            vertical-align: top;
            padding-right: 8px;
        }

        .stat-card {
            border: 1px solid #dbe5f3;
            border-radius: 12px;
            padding: 10px 12px;
            background: #ffffff;
        }

        .stat-card.blue { background: #eff6ff; border-color: #bfdbfe; }
        .stat-card.green { background: #ecfdf5; border-color: #a7f3d0; }
        .stat-card.purple { background: #f5f3ff; border-color: #ddd6fe; }
        .stat-card.amber { background: #fffbeb; border-color: #fde68a; }

        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-note {
            font-size: 9px;
            color: #475569;
            margin-top: 4px;
        }

        .filters-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            margin: 12px 0;
            overflow: hidden;
        }

        .filters-head {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: bold;
            color: #334155;
        }

        .filters-table td {
            padding: 8px 12px;
            border-right: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .filters-table td:last-child {
            border-right: 0;
        }

        .filter-label {
            display: block;
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
        }

        .filter-value {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
        }

        .table-shell {
            border: 1px solid #dbe5f3;
            border-radius: 12px;
            overflow: hidden;
        }

        .faults-table thead th {
            background: #eaf2ff;
            color: #0f3d91;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 7px;
            border-bottom: 1px solid #cddcf5;
            text-align: left;
        }

        .faults-table tbody td {
            padding: 8px 7px;
            border-bottom: 1px solid #e5edf6;
            vertical-align: top;
            font-size: 9px;
        }

        .faults-table tbody tr:nth-child(even) {
            background: #f8fbff;
        }

        .faults-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .cell-strong {
            font-weight: bold;
            color: #111827;
        }

        .cell-muted {
            color: #64748b;
        }

        .empty-state {
            text-align: center;
            padding: 18px 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>
                        <div class="header-title"><?php echo e($title); ?></div>
                        <div class="header-subtitle">Compact operational export aligned to the current faults table.</div>
                    </td>
                    <td class="header-meta">
                        <div><strong>Generated:</strong> <?php echo e($generatedAt); ?></div>
                        <div><strong>Total Faults:</strong> <?php echo e($faults->count()); ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="stats-table">
            <tr class="stats-row">
                <td>
                    <div class="stat-card blue">
                        <div class="stat-label">Exported Faults</div>
                        <div class="stat-value"><?php echo e($faults->count()); ?></div>
                        <div class="stat-note">Rows included in this document</div>
                    </div>
                </td>
                <td>
                    <div class="stat-card green">
                        <div class="stat-label">Assigned</div>
                        <div class="stat-value"><?php echo e($assignedCount); ?></div>
                        <div class="stat-note">Faults with an assignee</div>
                    </div>
                </td>
                <td>
                    <div class="stat-card purple">
                        <div class="stat-label">Unassigned</div>
                        <div class="stat-value"><?php echo e($unassignedCount); ?></div>
                        <div class="stat-note">Still awaiting allocation</div>
                    </div>
                </td>
                <td>
                    <div class="stat-card amber">
                        <div class="stat-label">View Scope</div>
                        <div class="stat-value" style="font-size: 12px;"><?php echo e($statusLabel); ?></div>
                        <div class="stat-note"><?php echo e($ageLabel); ?></div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="filters-wrap">
            <div class="filters-head">Applied Filters</div>
            <table class="filters-table">
                <tr>
                    <td>
                        <span class="filter-label">Search</span>
                        <span class="filter-value"><?php echo e($search !== '' ? $search : 'None'); ?></span>
                    </td>
                    <td>
                        <span class="filter-label">Status</span>
                        <span class="filter-value"><?php echo e($statusLabel); ?></span>
                    </td>
                    <td>
                        <span class="filter-label">Age</span>
                        <span class="filter-value"><?php echo e($ageLabel); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="table-shell">
            <table class="faults-table">
                <thead>
                    <tr>
                        <th style="width: 11%;">Ref No</th>
                        <th style="width: 17%;">Customer</th>
                        <th style="width: 18%;">Link</th>
                        <th style="width: 11%;">Switch</th>
                        <th style="width: 9%;">Port</th>
                        <th style="width: 13%;">Assigned To</th>
                        <th style="width: 10%;">Date Reported</th>
                        <th style="width: 7%;">Status</th>
                        <th style="width: 4%;">Age</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $faults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fault): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php ($latestRemark = $remarksByFault->get($fault->id)); ?>
                        <tr>
                            <td><span class="cell-strong"><?php echo e($fault->fault_ref_number); ?></span></td>
                            <td><?php echo e($fault->customer ?: 'N/A'); ?></td>
                            <td><?php echo e($fault->link ?: 'N/A'); ?></td>
                            <td><?php echo e($latestRemark->switch_name ?? 'N/A'); ?></td>
                            <td><?php echo e($latestRemark->port ?? 'N/A'); ?></td>
                            <td><?php echo e($fault->assignedTo ?: 'Not yet assigned'); ?></td>
                            <td>
                                <span class="cell-strong"><?php echo e(\Carbon\Carbon::parse($fault->created_at)->format('d M Y')); ?></span><br>
                                <span class="cell-muted"><?php echo e(\Carbon\Carbon::parse($fault->created_at)->format('H:i')); ?></span>
                            </td>
                            <td><?php echo e($fault->description ?: 'N/A'); ?></td>
                            <td><?php echo e($faultAges[$fault->id] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="empty-state">No faults matched the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/faults/export_pdf.blade.php ENDPATH**/ ?>