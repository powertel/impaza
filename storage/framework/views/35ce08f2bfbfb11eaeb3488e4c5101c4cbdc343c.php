<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-material')): ?>
<style>
  .mr-row {
    position: relative;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 1rem;
    padding: 0.7rem 0.7rem 0.7rem 0.85rem;
    margin-bottom: 0.7rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.03);
    overflow: hidden;
  }
  .mr-row::before {
    content: "";
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #6366F1 0%, #8B5CF6 50%, #A855F7 100%);
  }
  .mr-row-index {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; min-width: 28px;
    border-radius: 9999px;
    background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
    color: #fff; font-size: 0.78rem; font-weight: 700;
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
    letter-spacing: -0.01em;
    flex-shrink: 0;
  }
  .mr-row-remove {
    width: 32px; height: 32px; min-width: 32px;
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    border-radius: 9999px !important;
    border: 1px solid #FEE2E2 !important;
    background: #FEF2F2 !important;
    color: #B91C1C !important;
    transition: all 150ms ease;
    padding: 0 !important;
    flex-shrink: 0;
    align-self: center;
  }
  .mr-row-remove:hover:not(:disabled) {
    background: #DC2626 !important;
    color: #fff !important;
    border-color: #DC2626 !important;
    transform: scale(1.05);
  }
  .mr-row-remove:disabled {
    opacity: 0.35;
    cursor: not-allowed;
  }
  .mr-field-label {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.68rem; font-weight: 600;
    color: #64748B;
    margin-bottom: 0.18rem;
    letter-spacing: 0.01em;
  }
  .mr-field-label i {
    font-size: 0.65rem;
    color: #6366F1;
    opacity: 0.85;
  }
  .mr-row .form-control,
  .mr-row .form-select {
    border-color: #E2E8F0;
    border-radius: 0.6rem;
    font-size: 0.8rem;
    line-height: 1.15;
    padding-top: 0.32rem;
    padding-bottom: 0.32rem;
    transition: all 150ms ease;
    background: #FCFCFD;
  }
  .mr-row .form-control:focus,
  .mr-row .form-select:focus {
    border-color: #6366F1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    background: #fff;
  }
  .mr-row .form-control.bg-light,
  .mr-row input[readonly].mat-name-input {
    background: #F8FAFC !important;
    color: #334155 !important;
    font-weight: 500;
    border-color: #E2E8F0;
  }
  .mr-stock-chip {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.14rem 0.48rem;
    border-radius: 9999px;
    font-size: 0.66rem;
    font-weight: 600;
    line-height: 1.3;
    margin-top: 0.2rem;
    white-space: nowrap;
  }
  .mr-stock-chip.ok { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
  .mr-stock-chip.low { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
  .mr-stock-chip.out { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }
  .mr-section-card {
    background: #FAFBFF;
    border: 1px solid #EEF2FF;
    border-radius: 0.9rem;
    padding: 0.85rem 1rem;
  }
  .mr-section-title {
    display: inline-flex; align-items: center; gap: 0.5rem;
    font-weight: 700;
    color: #1E293B;
    font-size: 0.9rem;
  }
  .mr-section-title .icon-box {
    width: 26px; height: 26px;
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 0.72rem;
  }
  .mr-section-title.icon-amber .icon-box { background: linear-gradient(135deg, #F59E0B, #D97706); }
  .mr-section-title.icon-indigo .icon-box { background: linear-gradient(135deg, #6366F1, #8B5CF6); }
  .mr-fault-note {
    background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%);
    border: 1px solid #E0E7FF;
    border-radius: 0.85rem;
    padding: 0.7rem 0.9rem;
  }
  .mr-fault-note i { color: #6366F1; }
</style>
<?php
    $faultRef = data_get($fault, 'fault_ref_number', 'N/A');
    $pendingReqs = $pendingRequests ?? collect();
    $matList = $materials ?? collect();
    $editingMr = $editingMr ?? null;
    $isEdit = (bool) $editingMr;
    if ($isEdit) {
        $editItems = $editingMr->items->values()->all();
        $rowCount = max(1, count($editItems));
    } else {
        $editItems = [];
        $rowCount = 1;
    }
    $mrBadge = $isEdit ? $editingMr->statusBadge() : null;
?>
<script type="text/template" id="matReqRowTpl-<?php echo e($fault->id); ?>">
  <div class="mr-row mat-row d-flex align-items-end gap-3 flex-nowrap" data-row="ROW_IDX">
    <div class="d-flex align-self-center pe-1">
      <span class="mr-row-index">ROW_IDX_PLUSONE</span>
    </div>
    <div class="flex-grow-1 min-w-0">
      <div class="row gx-2 gy-1 align-items-end">
        <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
          <div class="row gx-2 gy-1 align-items-end">
            <div class="col-md-6 col-sm-12">
              <label class="mr-field-label"><i class="fas fa-list"></i>From Stock</label>
              <select class="form-select form-select-sm mat-select" name="items[ROW_IDX][material_id]" data-row="ROW_IDX" data-fault="<?php echo e($fault->id); ?>">
                <option value="">— Select SKU —</option>
                <?php $__currentLoopData = $matList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($m->id); ?>"
                    data-name="<?php echo e(e($m->name)); ?>"
                    data-unit="<?php echo e(e($m->unit)); ?>"
                    data-stock="<?php echo e($m->quantity_on_hand); ?>">
                    <?php echo e($m->name); ?><?php if($m->sku): ?> (<?php echo e($m->sku); ?>)<?php endif; ?> <?php if($m->category): ?> · <?php echo e($m->category); ?><?php endif; ?>
                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <option value="" disabled>──────────────────────────────</option>
                <option value="__custom__">✏️  Custom</option>
              </select>
            </div>
            <div class="col-md-6 col-sm-12">
              <label class="mr-field-label"><i class="fas fa-cube"></i>Material Name</label>
              <input type="text" class="form-control form-control-sm mat-name-input" name="items[ROW_IDX][material_name]" placeholder="Name (auto-filled, or type custom)" required>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6">
          <label class="mr-field-label"><i class="fas fa-ruler-combined"></i>Unit</label>
          <input type="text" class="form-control form-control-sm mat-unit-input" name="items[ROW_IDX][unit]" placeholder="pcs" value="pcs" required>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6">
          <label class="mr-field-label"><i class="fas fa-hashtag"></i>Qty / Metres</label>
          <input type="number" step="0.01" min="0" class="form-control form-control-sm mat-qty-input" name="items[ROW_IDX][quantity_requested]" placeholder="0" required>
          <span class="mr-stock-chip ok mat-stock-hint d-none" data-stock-chip="1"><i class="fas fa-circle-check"></i><span></span></span>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
          <label class="mr-field-label"><i class="fas fa-comment-dots"></i>Remark</label>
          <input type="text" class="form-control form-control-sm" name="items[ROW_IDX][remark]" placeholder="Optional: colour, spec, supplier">
        </div>
      </div>
    </div>
    <button type="button" class="mr-row-remove remove-mat-row align-self-center" title="Remove this row" disabled>
      <i class="fas fa-trash"></i>
    </button>
  </div>
</script>

<?php
    if (!function_exists('renderMatReqRow_editAware')) {
    function renderMatReqRow_editAware(int $idx, $matList, $editItem = null) {
        $selectedMatId = $editItem->material_id ?? null;
        $customName = $editItem->material_name ?? '';
        $unitVal = $editItem->unit ?? 'pcs';
        $qtyVal = $editItem->quantity_requested ?? 0;
        $remarkVal = $editItem->remark ?? '';
        $isCustom = $selectedMatId === null && !empty($customName);
        $stockText = '';
        $stockClass = 'ok';
        $stockIcon = 'fa-circle-check';
        if (!empty($selectedMatId)) {
            $stockMat = $matList->firstWhere('id', $selectedMatId);
            if ($stockMat) {
                $qoh = (float)($stockMat->quantity_on_hand ?? 0);
                if ($qoh <= 0) {
                    $stockClass = 'out'; $stockIcon = 'fa-circle-xmark';
                    $stockText = 'Out: 0 '.$stockMat->unit;
                } elseif ($qoh <= 10) {
                    $stockClass = 'low'; $stockIcon = 'fa-triangle-exclamation';
                    $stockText = 'Low: '.rtrim(rtrim(number_format($qoh, 2), '0'), '.').' '.$stockMat->unit;
                } else {
                    $stockText = 'Stock: '.rtrim(rtrim(number_format($qoh, 2), '0'), '.').' '.$stockMat->unit;
                }
            }
        }
        ob_start();
        ?>
        <div class="mr-row mat-row d-flex align-items-end gap-3 flex-nowrap" data-row="<?= $idx ?>">
          <div class="d-flex align-self-center pe-1">
            <span class="mr-row-index"><?= ($idx + 1) ?></span>
          </div>
          <div class="flex-grow-1 min-w-0">
            <div class="row gx-2 gy-1 align-items-end">
              <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                <div class="row gx-2 gy-1 align-items-end">
                  <div class="col-md-6 col-sm-12">
                    <label class="mr-field-label"><i class="fas fa-list"></i>From Stock</label>
                    <select class="form-select form-select-sm mat-select" name="items[<?= $idx ?>][material_id]" data-row="<?= $idx ?>" data-fault="<?= $GLOBALS['__faultIdForRow'] ?? 0 ?>">
                      <option value="">— Select SKU —</option>
                      <?php foreach($matList as $m):
                          $sel = ((string)$selectedMatId === (string)$m->id) ? ' selected' : '';
                          ?>
                        <option value="<?= $m->id ?>"
                          data-name="<?= e($m->name) ?>"
                          data-unit="<?= e($m->unit) ?>"
                          data-stock="<?= $m->quantity_on_hand ?>"<?= $sel ?>>
                          <?= $m->name ?><?php if($m->sku): ?> (<?= $m->sku ?>)<?php endif ?><?php if($m->category): ?> · <?= $m->category ?><?php endif ?>
                        </option>
                      <?php endforeach ?>
                      <option value="" disabled>──────────────────────────────</option>
                      <option value="__custom__"<?= $isCustom ? ' selected' : '' ?>>✏️  Custom</option>
                    </select>
                  </div>
                  <div class="col-md-6 col-sm-12">
                    <label class="mr-field-label"><i class="fas fa-cube"></i>Material Name</label>
                    <input type="text" class="form-control form-control-sm mat-name-input<?= !empty($selectedMatId) ? ' bg-light' : '' ?>" name="items[<?= $idx ?>][material_name]" placeholder="Name (auto-filled, or type custom)" value="<?= e($customName) ?>"<?= !empty($selectedMatId) ? ' readonly' : '' ?> required>
                  </div>
                </div>
              </div>
              <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6">
                <label class="mr-field-label"><i class="fas fa-ruler-combined"></i>Unit</label>
                <input type="text" class="form-control form-control-sm mat-unit-input" name="items[<?= $idx ?>][unit]" placeholder="pcs" value="<?= e($unitVal) ?>" required>
              </div>
              <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6">
                <label class="mr-field-label"><i class="fas fa-hashtag"></i>Qty / Metres</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm mat-qty-input" name="items[<?= $idx ?>][quantity_requested]" placeholder="0" value="<?= e($qtyVal) ?>" required>
                <?php if ($stockText !== ''): ?>
                <span class="mr-stock-chip <?= $stockClass ?> mat-stock-hint">
                  <i class="fas <?= $stockIcon ?>"></i><?= $stockText ?>
                </span>
                <?php else: ?>
                <span class="mr-stock-chip ok mat-stock-hint d-none" data-stock-chip="1"><i class="fas fa-circle-check"></i><span></span></span>
                <?php endif ?>
              </div>
              <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <label class="mr-field-label"><i class="fas fa-comment-dots"></i>Remark</label>
                <input type="text" class="form-control form-control-sm" name="items[<?= $idx ?>][remark]" placeholder="Optional: colour, spec, supplier" value="<?= e($remarkVal) ?>">
              </div>
            </div>
          </div>
          <button type="button" class="mr-row-remove remove-mat-row align-self-center" title="Remove this row"<?= $idx === 0 ? ' disabled' : '' ?>>
            <i class="fas fa-trash"></i>
          </button>
        </div>
        <?php
        return ob_get_clean();
    }
    }
    $GLOBALS['__faultIdForRow'] = $fault->id;
?>

<div class="modal custom-modal fade" id="requestMaterialCreateModal-<?php echo e($fault->id); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="requestMaterialCreateModalLabel-<?php echo e($fault->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="requestMaterialCreateModalLabel-<?php echo e($fault->id); ?>">
          <?php if($isEdit): ?>
            <i class="fas fa-pen-to-square me-2"></i>Update Material Request
          <?php else: ?>
            <i class="fas fa-boxes-packing me-2"></i>Request Materials
          <?php endif; ?>
        </h5>
        <div class="text-muted small mt-1">
          <?php if($isEdit): ?>
            Editing existing request <span class="font-monospace"><?php echo e($editingMr->request_number); ?></span> — your previous values are pre-loaded below. Changes replace the open request.
          <?php else: ?>
            Select materials you need for this fault and specify quantities or metres. Request will be sent to Stores.
          <?php endif; ?>
        </div>
        <?php if($isEdit && $mrBadge): ?>
        <div class="mt-2">
          <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['label' => $mrBadge['label'],'color' => $mrBadge['color'],'soft' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mrBadge['label']),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mrBadge['color']),'soft' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </div>
        <?php endif; ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="<?php echo e(route('stores.store')); ?>" method="POST" id="matReqForm-<?php echo e($fault->id); ?>" class="mat-req-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="fault_id" value="<?php echo e($fault->id); ?>">
        <?php if($isEdit): ?>
        <input type="hidden" name="_replace_mr_id" value="<?php echo e($editingMr->id); ?>">
        <?php endif; ?>

        <div class="modal-body">
          <div class="mr-fault-note mb-4 d-flex align-items-start gap-2">
            <i class="fas fa-circle-info mt-0.5 fa-fw"></i>
            <div>
              Requesting for <strong><?php echo e($faultRef); ?></strong>. Include UTP cable (meters), RJ45 connectors, splice protectors, etc. Your name will be logged automatically.
            </div>
          </div>

          <div class="mr-section-card mb-4">
            <label class="mr-section-title icon-amber">
              <span class="icon-box"><i class="fas fa-sticky-note"></i></span>
              Note to Stores <span class="text-muted fw-normal ms-1" style="font-size:0.78rem;">(optional)</span>
            </label>
            <textarea name="technician_note" class="form-control form-control-sm mt-2" rows="2" placeholder="Any additional context or special request, e.g. Urgent same-day dispatch, outdoor rated only..."><?php echo e(old('technician_note', $isEdit ? e($editingMr->technician_note ?? '') : '')); ?></textarea>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3 pe-1">
            <label class="mr-section-title icon-indigo">
              <span class="icon-box"><i class="fas fa-list-check"></i></span>
              Materials Required
            </label>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill add-mat-row-btn" data-fault="<?php echo e($fault->id); ?>" id="addRowBtn-<?php echo e($fault->id); ?>" style="border-radius:9999px;padding:.35rem .85rem;font-weight:600;font-size:.8rem;">
              <i class="fas fa-plus me-1"></i>Add Row
            </button>
          </div>

          <div class="mat-req-items mat-req-items-<?php echo e($fault->id); ?>">
            <?php for($r = 0; $r < $rowCount; $r++): ?>
              <?php echo renderMatReqRow_editAware($r, $matList, $editItems[$r] ?? null); ?>

            <?php endfor; ?>
          </div>

          <?php if($pendingReqs->count() > 0): ?>
          <div class="mt-4">
            <div class="d-flex align-items-center mb-2">
              <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border me-2"><i class="fas fa-clock-rotate-left"></i> Pending Requests</span>
              <small class="text-muted">This fault already has open material requests.</small>
            </div>
            <?php $__currentLoopData = $pendingReqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($isEdit && (int)$pr->id === (int)$editingMr->id): ?> <?php continue; ?> <?php endif; ?>
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
            <?php if($isEdit): ?>
              <i class="fas fa-save me-1"></i>Update Request
            <?php else: ?>
              <i class="fas fa-paper-plane me-1"></i>Submit Request to Stores
            <?php endif; ?>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/stores/create_modal.blade.php ENDPATH**/ ?>