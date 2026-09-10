<?php
    $badge = $mr->statusBadge();
?>
<div class="modal custom-modal fade" id="viewMRModal-<?php echo e($mr->id); ?>" tabindex="-1" aria-labelledby="viewMRModalLabel-<?php echo e($mr->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="viewMRModalLabel-<?php echo e($mr->id); ?>"><i class="fas fa-file-invoice me-2"></i>Request Details</h5>
          <div class="mt-1">
            <span class="font-monospace me-2"><?php echo e($mr->request_number); ?></span>
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
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="fault-modal-section h-100">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div>
                  <div class="fault-modal-section-title">Fault</div>
                  <div class="fault-modal-section-subtitle">The fault this request is linked to.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                <div class="fault-modal-grid">
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Fault Ref.</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional($mr->fault)->fault_ref_number ?: '#' . $mr->fault_id); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Customer</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional(optional($mr->fault)->customer)->customer ?: '—'); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">City</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional(optional($mr->fault)->city)->city ?: '—'); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Location</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional(optional($mr->fault)->suburb)->suburb ?: '—'); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Link</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional(optional($mr->fault)->link)->link ?: '—'); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">POP</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional(optional($mr->fault)->pop)->pop ?: '—'); ?></div>
                  </div>
                </div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
                    <?php if($mr->fault): ?>
                    <div class="mt-2">
                        <a href="<?php echo e(route('faults.show', $mr->fault->id)); ?>" target="_blank" class="small text-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Open Fault Profile
                        </a>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="fault-modal-section h-100">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-user-circle"></i></span>
                <div>
                  <div class="fault-modal-section-title">Request Timeline</div>
                  <div class="fault-modal-section-subtitle">Who requested and when it was processed.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                <div class="fault-modal-grid">
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Technician</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional($mr->requestedBy)->name ?: '—'); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Submitted</span>
                    <div class="fault-modal-kv-value"><small><?php echo e($mr->submitted_at ? \Carbon\Carbon::parse($mr->submitted_at)->format('j M Y H:i') : '—'); ?></small></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Processed By</span>
                    <div class="fault-modal-kv-value"><?php echo e(optional($mr->processedBy)->name ?: '—'); ?></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Processed At</span>
                    <div class="fault-modal-kv-value"><small><?php echo e($mr->processed_at ? \Carbon\Carbon::parse($mr->processed_at)->format('j M Y H:i') : '—'); ?></small></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if($mr->technician_note || $mr->stores_note): ?>
        <div class="row g-3 mb-3">
            <?php if($mr->technician_note): ?>
            <div class="col-md-6">
                <div class="small text-muted fw-semibold mb-1"><i class="fas fa-comment-dots me-1"></i> Technician's Note</div>
                <div class="card card-body bg-light border p-2 small"><?php echo e($mr->technician_note); ?></div>
            </div>
            <?php endif; ?>
            <?php if($mr->stores_note): ?>
            <div class="col-md-6">
                <div class="small text-muted fw-semibold mb-1"><i class="fas fa-warehouse me-1"></i> Stores' Note</div>
                <div class="card card-body bg-light border p-2 small"><?php echo e($mr->stores_note); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="mt-2">
          <div class="small text-muted fw-semibold mb-2"><i class="fas fa-list-ul me-1"></i>Requested Materials</div>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:40%">Material</th>
                  <th class="text-center">Unit</th>
                  <th class="text-end">Requested</th>
                  <th class="text-end">Issued</th>
                  <th class="text-center">Available</th>
                  <th>Remark</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $mr->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo e(is_null($item->is_available) ? '' : (!$item->is_available ? 'table-danger' : ($item->isFullyIssued() ? 'table-success' : 'table-warning'))); ?>">
                  <td>
                    <div class="fw-medium"><?php echo e($item->material_name); ?></div>
                    <?php if($item->material): ?>
                        <div class="small text-muted"><span class="font-monospace"><?php echo e($item->material->sku ?? 'no sku'); ?></span> · <?php echo e($item->material->category ?? ''); ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="text-center"><span class="badge bg-light text-dark border"><?php echo e($item->unit); ?></span></td>
                  <td class="text-end fw-medium"><?php echo e(number_format($item->quantity_requested, 2)); ?></td>
                  <td class="text-end">
                    <span class="<?php echo e($item->quantity_issued>0 ? 'text-success fw-medium' : 'text-muted'); ?>">
                      <?php echo e(number_format($item->quantity_issued, 2)); ?>

                    </span>
                  </td>
                  <td class="text-center">
                    <?php if(is_null($item->is_available)): ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border">Pending</span>
                    <?php elseif($item->is_available): ?>
                        <span class="badge bg-success-subtle text-success-emphasis border"><i class="fas fa-check"></i> Yes</span>
                    <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger-emphasis border"><i class="fas fa-xmark"></i> No Stock</span>
                    <?php endif; ?>
                  </td>
                  <td class="small text-muted"><?php echo e($item->remark ?: '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer fault-modal-footer">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stores-process')): ?>
            <?php if($mr->isPending() || $mr->status===\App\Models\MaterialRequest::STATUS_PROCESSING): ?>
                <a href="<?php echo e(route('stores.issue', $mr->id)); ?>" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-check-double me-1"></i>Process / Issue
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/resources/views/stores/show_modal.blade.php ENDPATH**/ ?>