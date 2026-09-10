<div class="modal custom-modal fade" id="editMaterialModal-<?php echo e($material->id); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editMaterialModalLabel-<?php echo e($material->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="<?php echo e(route('materials.update', $material->id)); ?>" method="POST" id="editMaterialForm-<?php echo e($material->id); ?>" novalidate>
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0" id="editMaterialModalLabel-<?php echo e($material->id); ?>"><i class="fas fa-pencil me-2 text-primary"></i>Edit Material</h5>
            <div class="text-muted small mt-1">Update catalogue details for <?php echo e($material->name); ?>.</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label small fw-semibold">Name *</label>
              <input type="text" class="form-control form-control-sm" name="name" value="<?php echo e(old('name', $material->name)); ?>" required autocomplete="off">
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold">SKU</label>
              <input type="text" class="form-control form-control-sm" name="sku" value="<?php echo e(old('sku', $material->sku)); ?>" autocomplete="off">
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold">Category</label>
              <input type="text" class="form-control form-control-sm" name="category" list="categoryListEdit-<?php echo e($material->id); ?>" value="<?php echo e(old('category', $material->category)); ?>" autocomplete="off">
              <datalist id="categoryListEdit-<?php echo e($material->id); ?>">
                <option value="Cable"></option>
                <option value="Connectors"></option>
                <option value="Splicing"></option>
                <option value="Patch Panels"></option>
                <option value="Tools"></option>
                <option value="Cabinets & Racks"></option>
                <option value="Consumables"></option>
                <?php $__currentLoopData = ($categories ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($c); ?>"></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </datalist>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Unit *</label>
              <input type="text" class="form-control form-control-sm" name="unit" value="<?php echo e(old('unit', $material->unit)); ?>" required autocomplete="off">
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Qty on Hand *</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="quantity_on_hand" value="<?php echo e(old('quantity_on_hand', $material->quantity_on_hand)); ?>" required>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Reorder Lvl</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="reorder_level" value="<?php echo e(old('reorder_level', $material->reorder_level)); ?>">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Description</label>
              <textarea class="form-control form-control-sm" name="description" rows="2"><?php echo e(old('description', $material->description)); ?></textarea>
            </div>
            <div class="col-12">
              <input type="hidden" name="is_active" value="0">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="matEditActive-<?php echo e($material->id); ?>" value="1" <?php echo e(old('is_active', $material->is_active ? 1 : 0) ? 'checked' : ''); ?>>
                <label class="form-check-label small" for="matEditActive-<?php echo e($material->id); ?>">Active in catalogue</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm rounded-pill mat-edit-submit" data-form-id="editMaterialForm-<?php echo e($material->id); ?>">
            <i class="fas fa-save me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/resources/views/materials/edit_modal.blade.php ENDPATH**/ ?>