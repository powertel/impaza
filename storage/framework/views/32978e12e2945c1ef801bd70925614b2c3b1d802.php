<?php $__env->startSection('title'); ?>
Stores — Material Requests
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('content'); ?>

<section class="content workflow-faults-page">
<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title"><i class="fas fa-dolly me-2"></i>Material Requests</h3>
            <div class="faults-panel-subtitle">Review material requests from technicians, issue items and mark unavailability per line.</div>
        </div>
        <div class="faults-panel-actions">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['material-list','material-create'])): ?>
                <a href="<?php echo e(route('materials.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-warehouse me-1"></i>Inventory
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="faults-toolbar">
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#F59E0B;">
                    <div class="stat-card-label">Pending</div>
                    <div class="stat-card-value"><?php echo e($stats['pending']); ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#8B5CF6;">
                    <div class="stat-card-label">Partial</div>
                    <div class="stat-card-value"><?php echo e($stats['partial']); ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#10B981;">
                    <div class="stat-card-label">Issued</div>
                    <div class="stat-card-value"><?php echo e($stats['issued']); ?></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="stat-card h-100" style="--card-accent:#3B82F6;">
                    <div class="stat-card-label">Total</div>
                    <div class="stat-card-value"><?php echo e($stats['total']); ?></div>
                </div>
            </div>
        </div>

        <form method="GET" action="<?php echo e(route('stores.requests')); ?>" class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" <?php echo e($perPage==10?'selected':''); ?>>10</option>
                        <option value="20" <?php echo e($perPage==20?'selected':''); ?>>20</option>
                        <option value="50" <?php echo e($perPage==50?'selected':''); ?>>50</option>
                        <option value="100" <?php echo e($perPage==100?'selected':''); ?>>100</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field">
                <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                    <option value="pending" <?php echo e($statusFilter=='pending'?'selected':''); ?>>Pending</option>
                    <option value="processing" <?php echo e($statusFilter=='processing'?'selected':''); ?>>Processing</option>
                    <option value="partial" <?php echo e($statusFilter=='partial'?'selected':''); ?>>Partial</option>
                    <option value="issued" <?php echo e($statusFilter=='issued'?'selected':''); ?>>Issued</option>
                    <option value="cancelled" <?php echo e($statusFilter=='cancelled'?'selected':''); ?>>Cancelled</option>
                    <option value="all" <?php echo e($statusFilter=='all'?'selected':''); ?>>All Statuses</option>
                </select>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Search request #, fault ref, technician..." value="<?php echo e($q); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
                <i class="fas fa-search me-1"></i>Search
            </button>
            <a href="<?php echo e(route('stores.requests')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
                <i class="fas fa-rotate-left me-1"></i>Reset
            </a>
        </form>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Fault</th>
                        <th>Technician</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $materialRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $badge = $mr->statusBadge();
                        $itemsCount = $mr->items->count();
                        $issuedCount = $mr->items->filter(fn($i)=>$i->quantity_issued>0)->count();
                    ?>
                    <tr>
                        <td>
                            <span class="font-monospace fw-medium"><?php echo e($mr->request_number); ?></span>
                        </td>
                        <td>
                            <?php if($mr->fault): ?>
                                <div class="fw-medium"><?php echo e($mr->fault->fault_ref_number); ?></div>
                                <div class="small text-muted"><?php echo e(optional($mr->fault->customer)->customer); ?></div>
                            <?php else: ?>
                                <span class="text-muted">Fault #<?php echo e($mr->fault_id); ?> (deleted)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(optional($mr->requestedBy)->name ?: '—'); ?></td>
                        <td>
                            <span class="badge bg-light text-dark border"><?php echo e($itemsCount); ?> items</span>
                            <?php if($issuedCount>0): ?>
                                <span class="badge bg-success-subtle text-success border ms-1"><?php echo e($issuedCount); ?> issued</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['label' => $badge['label'],'color' => $badge['color'],'soft' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badge['label']),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badge['color']),'soft' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </td>
                        <td>
                            <small><?php echo e($mr->created_at ? \Carbon\Carbon::parse($mr->created_at)->format('j M Y, H:i') : '—'); ?></small>
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#viewMRModal-<?php echo e($mr->id); ?>" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stores-process')): ?>
                                <?php if($mr->isPending() || $mr->status===\App\Models\MaterialRequest::STATUS_PROCESSING): ?>
                                    <a href="<?php echo e(route('stores.issue', $mr->id)); ?>" class="btn btn-sm btn-outline-primary rounded-pill ms-1" title="Issue items for this request (Stores / Technician acknowledgement)" style="min-width:7.5rem;">
                                        <i class="fas fa-boxes-packing me-1"></i>Process
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No requests matching your filters.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing <?php echo e($materialRequests->count()); ?> of <?php echo e($materialRequests->total()); ?> requests</small>
            <div><?php echo e($materialRequests->links()); ?></div>
        </div>
    </div>
</div>
</section>

<?php $__currentLoopData = $materialRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php echo $__env->make('stores.show_modal', ['mr' => $mr], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
  <?php echo $__env->make('partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/stores/requests_index.blade.php ENDPATH**/ ?>