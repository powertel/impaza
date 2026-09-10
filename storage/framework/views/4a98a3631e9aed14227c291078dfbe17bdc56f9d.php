<div class="modal custom-modal fade" id="createMaterialModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createMaterialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="<?php echo e(route('materials.store')); ?>" method="POST" id="createMaterialForm" novalidate>
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0" id="createMaterialModalLabel"><i class="fas fa-plus-circle me-2 text-primary"></i>New Material</h5>
            <div class="text-muted small mt-1">Add an item to the inventory catalogue.</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label small fw-semibold">Name *</label>
              <input type="text" class="form-control form-control-sm" name="name" id="cmName" placeholder="e.g. UTP Cable Cat6" required autocomplete="off">
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold">SKU</label>
              <input type="text" class="form-control form-control-sm" name="sku" placeholder="e.g. UTP-C6-305M" autocomplete="off">
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold">Category</label>
              <input type="text" class="form-control form-control-sm" name="category" list="categoryList" placeholder="e.g. Cable / Connectors" autocomplete="off">
              <datalist id="categoryList">
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
              <input type="text" class="form-control form-control-sm" name="unit" value="pcs" placeholder="pcs / mtr / box" required autocomplete="off">
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Qty on Hand *</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="quantity_on_hand" value="0" required>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Reorder Lvl</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="reorder_level" value="0">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Description</label>
              <textarea class="form-control form-control-sm" name="description" rows="2" placeholder="Optional details / specifications"></textarea>
            </div>
            <div class="col-12">
              <input type="hidden" name="is_active" value="0">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="matCreateActive" value="1" checked>
                <label class="form-check-label small" for="matCreateActive">Active in catalogue</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm rounded-pill" id="createMaterialSubmitBtn" onclick="document.getElementById('createMaterialForm').requestSubmit && document.getElementById('createMaterialForm').requestSubmit() ? false : document.getElementById('createMaterialForm').submit();">
            <i class="fas fa-save me-1"></i>Save Material
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
(function () {
  const btn = document.getElementById('createMaterialSubmitBtn');
  const form = document.getElementById('createMaterialForm');
  if (btn && form) {
    btn.addEventListener('click', function (e) {
      if (!form.reportValidity()) return;
      const submitNative = document.createElement('button');
      submitNative.type = 'submit';
      submitNative.style.display = 'none';
      form.appendChild(submitNative);
      submitNative.click();
      setTimeout(() => submitNative.remove(), 100);
    });
  }
  const openBtn = document.getElementById('openCreateMaterialBtn');
  if (openBtn) {
    openBtn.addEventListener('click', function (e) {
      const modal = document.getElementById('createMaterialModal');
      if (modal && window.bootstrap && typeof bootstrap.Modal === 'function') {
        const inst = bootstrap.Modal.getOrCreateInstance(modal);
        inst.show();
        const nameInput = document.getElementById('cmName');
        if (nameInput) setTimeout(()=> nameInput.focus(), 400);
      }
    });
  }
})();
</script>
<?php /**PATH /var/www/html/resources/views/materials/create_modal.blade.php ENDPATH**/ ?>