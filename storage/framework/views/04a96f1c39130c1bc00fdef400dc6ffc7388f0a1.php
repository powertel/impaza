<?php $__env->startSection('title'); ?>
Materials Inventory
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('content'); ?>

<section class="content workflow-faults-page">
<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title"><i class="fas fa-warehouse me-2"></i>Materials Inventory</h3>
            <div class="faults-panel-subtitle">Manage stock items: UTP cable, RJ45 connectors, splice protectors and more.</div>
        </div>
        <div class="faults-panel-actions">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('material-create')): ?>
                <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createMaterialModal" id="openCreateMaterialBtn">
                    <i class="fas fa-plus-circle me-1"></i>New Material
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="faults-toolbar">
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#3B82F6;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-cubes"></i></div>
                        <div class="impaza-stat-title">Total SKUs</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value"><?php echo e(number_format($stats['total'])); ?></div>
                            <div class="impaza-stat-sub">Unique stock items on inventory register</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#10B981;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-circle-check"></i></div>
                        <div class="impaza-stat-title">Active</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value"><?php echo e(number_format($stats['active'])); ?></div>
                            <div class="impaza-stat-sub">Items currently available for requisition</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#F59E0B;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
                        <div class="impaza-stat-title">Low Stock</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value"><?php echo e(number_format($stats['lowStock'])); ?></div>
                            <div class="impaza-stat-sub">Below reorder threshold — procure soon</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#EF4444;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-circle-xmark"></i></div>
                        <div class="impaza-stat-title">Out of Stock</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value"><?php echo e(number_format($stats['outOfStock'])); ?></div>
                            <div class="impaza-stat-sub">Zero on-hand — cannot issue right now</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="<?php echo e(route('materials.index')); ?>" class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select class="form-select form-select-sm" name="per_page" aria-label="Rows per page" onchange="this.form.submit()">
                        <option value="10" <?php echo e($perPage==10?'selected':''); ?>>10</option>
                        <option value="20" <?php echo e($perPage==20?'selected':''); ?>>20</option>
                        <option value="50" <?php echo e($perPage==50?'selected':''); ?>>50</option>
                        <option value="100" <?php echo e($perPage==100?'selected':''); ?>>100</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field">
                <select class="form-select form-select-sm" name="category" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c); ?>" <?php echo e($category==$c?'selected':''); ?>><?php echo e($c); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="faults-toolbar-field">
                <select class="form-select form-select-sm" name="stock" onchange="this.form.submit()">
                    <option value="all" <?php echo e($stockFilter=='all'?'selected':''); ?>>All Stocks</option>
                    <option value="active" <?php echo e($stockFilter=='active'?'selected':''); ?>>Active Only</option>
                    <option value="low" <?php echo e($stockFilter=='low'?'selected':''); ?>>Low Stock</option>
                    <option value="out" <?php echo e($stockFilter=='out'?'selected':''); ?>>Out of Stock</option>
                </select>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Search name, SKU, category..." value="<?php echo e($q); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
                <i class="fas fa-search me-1"></i>Search
            </button>
            <a href="<?php echo e(route('materials.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
                <i class="fas fa-rotate-left me-1"></i>Reset
            </a>
        </form>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th class="text-end">On Hand</th>
                        <th class="text-end">Reorder Lvl</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($materials->firstItem() + $idx); ?></td>
                        <td>
                            <div class="fw-medium"><?php echo e($m->name); ?></div>
                            <?php if($m->description): ?>
                                <div class="small text-muted mt-1"><?php echo e(Str::limit($m->description, 80)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace"><?php echo e($m->sku ?: '—'); ?></span></td>
                        <td>
                            <?php if($m->category): ?>
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border"><?php echo e($m->category); ?></span>
                            <?php else: ?> — <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo e($m->unit); ?></span></td>
                        <td class="text-end">
                            <strong class="<?php echo e($m->quantity_on_hand <= 0 ? 'text-danger' : ($m->isLowStock() ? 'text-warning' : 'text-success')); ?>">
                                <?php echo e(number_format($m->quantity_on_hand, 2)); ?>

                            </strong>
                        </td>
                        <td class="text-end text-muted"><?php echo e(number_format($m->reorder_level, 2)); ?></td>
                        <td>
                            <?php if(!$m->is_active): ?>
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border">Inactive</span>
                            <?php elseif($m->quantity_on_hand <= 0): ?>
                                <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border">Out of Stock</span>
                            <?php elseif($m->isLowStock()): ?>
                                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border">Low Stock</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-success-subtle text-success-emphasis border">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#viewMaterialModal-<?php echo e($m->id); ?>" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('material-edit')): ?>
                            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editMaterialModal-<?php echo e($m->id); ?>" title="Edit">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('material-delete')): ?>
                            <form action="<?php echo e(route('materials.destroy', $m->id)); ?>" method="POST" onsubmit="return confirm('Delete material: <?php echo e($m->name); ?>?');" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">No materials found. Add your first item.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing <?php echo e($materials->count()); ?> of <?php echo e($materials->total()); ?> items</small>
            <div><?php echo e($materials->links()); ?></div>
        </div>
    </div>
</div>
</section>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('material-create')): ?>
  <?php echo $__env->make('materials.create_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php echo $__env->make('materials.view_modal', ['material' => $m], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('material-edit')): ?>
    <?php echo $__env->make('materials.edit_modal', ['material' => $m, 'categories' => $categories], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
  <?php echo $__env->make('partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
(function () {
  document.querySelectorAll('.mat-edit-submit').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const formId = btn.getAttribute('data-form-id');
      const form = document.getElementById(formId);
      if (!form) return;
      if (!form.reportValidity()) return;
      const nativeSubmit = document.createElement('button');
      nativeSubmit.type = 'submit';
      nativeSubmit.style.display = 'none';
      form.appendChild(nativeSubmit);
      nativeSubmit.click();
      setTimeout(function () { nativeSubmit.remove(); }, 100);
    });
  });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/materials/index.blade.php ENDPATH**/ ?>