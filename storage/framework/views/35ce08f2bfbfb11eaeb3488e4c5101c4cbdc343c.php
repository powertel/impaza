<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-material')): ?>
<?php
    $faultRef = data_get($fault, 'fault_ref_number', 'N/A');
    $pendingReqs = $pendingRequests ?? collect();
    $matList = $materials ?? collect();
?>
<script type="text/template" id="matReqRowTpl-<?php echo e($fault->id); ?>">
  <div class="card card-body p-3 mb-2 mat-row">
    <div class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label small text-muted">Material</label>
        <select class="form-select form-select-sm mat-select" name="items[ROW_IDX][material_id]" data-row="ROW_IDX" data-fault="<?php echo e($fault->id); ?>">
          <option value="">— Select from inventory —</option>
          <?php $__currentLoopData = $matList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($m->id); ?>"
              data-name="<?php echo e(e($m->name)); ?>"
              data-unit="<?php echo e(e($m->unit)); ?>"
              data-stock="<?php echo e($m->quantity_on_hand); ?>">
              <?php echo e($m->name); ?><?php if($m->sku): ?> (<?php echo e($m->sku); ?>)<?php endif; ?> <?php if($m->category): ?> · <?php echo e($m->category); ?><?php endif; ?>
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <option value="" disabled>──────────────────────────────────</option>
          <option value="__custom__">✏️  Custom / Not in list (type name below ↓)</option>
        </select>
        <input type="text" class="form-control form-control-sm mt-1 mat-name-input" name="items[ROW_IDX][material_name]" placeholder="Material name (auto-filled from selection, or type custom)" required>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Unit</label>
        <input type="text" class="form-control form-control-sm mat-unit-input" name="items[ROW_IDX][unit]" placeholder="pcs / mtr / box" value="pcs" required>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Qty / Metres</label>
        <input type="number" step="0.01" min="0" class="form-control form-control-sm mat-qty-input" name="items[ROW_IDX][quantity_requested]" placeholder="0" required>
        <div class="form-text small mat-stock-hint"></div>
      </div>
      <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-outline-danger remove-mat-row w-100" title="Remove" disabled>
          <i class="fas fa-trash"></i>
        </button>
      </div>
      <div class="col-md-12">
        <input type="text" class="form-control form-control-sm" name="items[ROW_IDX][remark]" placeholder="Remark (optional, e.g. blue colour, outdoor rated)">
      </div>
    </div>
  </div>
</script>

<div class="modal custom-modal fade" id="requestMaterialCreateModal-<?php echo e($fault->id); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requestMaterialCreateModalLabel-<?php echo e($fault->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="requestMaterialCreateModalLabel-<?php echo e($fault->id); ?>">
          <i class="fas fa-boxes-packing me-2"></i>Request Materials
        </h5>
        <div class="text-muted small mt-1">
          Select materials you need for this fault and specify quantities or metres. Request will be sent to Stores.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="<?php echo e(route('stores.store')); ?>" method="POST" id="matReqForm-<?php echo e($fault->id); ?>" class="mat-req-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="fault_id" value="<?php echo e($fault->id); ?>">

        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-info-circle"></i>
            <div>
              Requesting for <strong><?php echo e($faultRef); ?></strong>. Include UTP cable (meters), RJ45 connectors, splice protectors, etc. Your name will be logged automatically.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold"><i class="fas fa-sticky-note me-1 text-secondary"></i> Note to Stores (optional)</label>
            <textarea name="technician_note" class="form-control form-control-sm" rows="2" placeholder="Any additional context or special request..."></textarea>
          </div>

          <div class="mb-2 d-flex justify-content-between align-items-center">
            <label class="form-label fw-semibold mb-0"><i class="fas fa-list-check me-1 text-secondary"></i> Materials Required</label>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill add-mat-row-btn" data-fault="<?php echo e($fault->id); ?>" id="addRowBtn-<?php echo e($fault->id); ?>">
              <i class="fas fa-plus me-1"></i>Add Row
            </button>
          </div>

          <div class="mat-req-items mat-req-items-<?php echo e($fault->id); ?>">
            <div class="card card-body p-3 mb-2 mat-row">
              <div class="row g-2 align-items-end">
                <div class="col-md-5">
                  <label class="form-label small text-muted">Material</label>
                  <select class="form-select form-select-sm mat-select" name="items[0][material_id]" data-row="0" data-fault="<?php echo e($fault->id); ?>">
                    <option value="">— Select from inventory —</option>
                    <?php $__currentLoopData = $matList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($m->id); ?>"
                        data-name="<?php echo e(e($m->name)); ?>"
                        data-unit="<?php echo e(e($m->unit)); ?>"
                        data-stock="<?php echo e($m->quantity_on_hand); ?>">
                        <?php echo e($m->name); ?><?php if($m->sku): ?> (<?php echo e($m->sku); ?>)<?php endif; ?> <?php if($m->category): ?> · <?php echo e($m->category); ?><?php endif; ?>
                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <option value="" disabled>──────────────────────────────────</option>
                    <option value="__custom__">✏️  Custom / Not in list (type name below ↓)</option>
                  </select>
                  <input type="text" class="form-control form-control-sm mt-1 mat-name-input" name="items[0][material_name]" placeholder="Material name (auto-filled from selection, or type custom)" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label small text-muted">Unit</label>
                  <input type="text" class="form-control form-control-sm mat-unit-input" name="items[0][unit]" placeholder="pcs / mtr / box" value="pcs" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label small text-muted">Qty / Metres</label>
                  <input type="number" step="0.01" min="0" class="form-control form-control-sm mat-qty-input" name="items[0][quantity_requested]" placeholder="0" required>
                  <div class="form-text small mat-stock-hint"></div>
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-sm btn-outline-danger remove-mat-row w-100" title="Remove" disabled>
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
                <div class="col-md-12">
                  <input type="text" class="form-control form-control-sm" name="items[0][remark]" placeholder="Remark (optional, e.g. blue colour, outdoor rated)">
                </div>
              </div>
            </div>
          </div>

          <?php if($pendingReqs->count() > 0): ?>
          <div class="mt-4">
            <div class="d-flex align-items-center mb-2">
              <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border me-2"><i class="fas fa-clock-rotate-left"></i> Pending Requests</span>
              <small class="text-muted">This fault already has open material requests.</small>
            </div>
            <?php $__currentLoopData = $pendingReqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="card mb-2 border-warning-subtle">
                <div class="card-body py-2 px-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="small"><?php echo e($pr->request_number); ?></strong>
                    <span class="badge" style="background:#F59E0B22;color:#B45309;"><?php echo e($pr->statusBadge()['label']); ?></span>
                  </div>
                  <ul class="list-unstyled small mb-0">
                    <?php $__currentLoopData = $pr->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <li class="d-flex justify-content-between py-1 border-top">
                        <span><?php echo e($itm->material_name); ?></span>
                        <span class="text-muted"><?php echo e($itm->quantity_requested); ?> <?php echo e($itm->unit); ?></span>
                      </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <?php endif; ?>

        </div>

        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill mat-req-submit" data-fault="<?php echo e($fault->id); ?>">
            <i class="fas fa-paper-plane me-1"></i>Submit Request to Stores
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/stores/create_modal.blade.php ENDPATH**/ ?>