<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-edit')): ?>
<?php
  $full = DB::table('links')
    ->leftJoin('customers','links.customer_id','=','customers.id')
    ->leftJoin('cities','links.city_id','=','cities.id')
    ->leftJoin('suburbs','links.suburb_id','=','suburbs.id')
    ->leftJoin('pops','links.pop_id','=','pops.id')
    ->leftJoin('link_types','links.linkType_id','=','link_types.id')
    ->where('links.id', $link->id)
    ->select('links.id','links.link','links.customer_id','links.city_id','links.suburb_id','links.pop_id','links.linkType_id','links.contract_number','links.jcc_number','links.sapcodes','links.comment','links.quantity','links.service_type','links.capacity')
    ->first();
?>
<div class="modal custom-modal fade" id="linkEditModal<?php echo e($link->id); ?>" tabindex="-1" aria-labelledby="linkEditModalLabel<?php echo e($link->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="linkEditModalLabel<?php echo e($link->id); ?>"><i class="fas fa-pen-to-square me-2"></i>Edit Link</h5>
          <div class="text-muted small mt-1">Update service, customer, and mapping details for this link using the refreshed business modal layout.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-link"></i> <?php echo e($link->link); ?></span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('links.update', $link->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changes here affect reporting, customer views, and link lifecycle actions, so keep the mapping and service details accurate.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-link"></i></span>
              <div>
                <div class="fault-modal-section-title">Link Details</div>
                <div class="fault-modal-section-subtitle">Update customer ownership, service details, and network location mapping.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Customer</label>
              <select name="customer_id" class="form-select" required>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($cust->id); ?>" data-contract-number="<?php echo e($cust->contract_number); ?>" <?php echo e(($full && $full->customer_id == $cust->id) ? 'selected' : ''); ?>><?php echo e($cust->customer); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Link</label>
              <input type="text" name="link" value="<?php echo e($full ? $full->link : $link->link); ?>" class="form-control link-name-input" data-ignore-id="<?php echo e($link->id); ?>" required>
            </div>
            <div class="w-100"></div>
            <div class="col-md-4">
              <label class="form-label">JCC Number</label>
              <input type="text" name="jcc_number" value="<?php echo e($full ? $full->jcc_number : ''); ?>" class="form-control jcc-number-input" data-ignore-id="<?php echo e($link->id); ?>" placeholder="e.g. JCC-12345">
              <div class="invalid-feedback">JCC number already exists.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Service Type</label>
              <select name="service_type" class="form-select">
                <option value="" <?php echo e(empty($full?->service_type) ? 'selected disabled' : ''); ?>>Select Service Type</option>
                <option value="Internet" <?php echo e(($full && $full->service_type === 'Internet') ? 'selected' : ''); ?>>Internet</option>
                <option value="Metro VPN" <?php echo e(($full && $full->service_type === 'Metro VPN') ? 'selected' : ''); ?>>Metro VPN</option>
                <option value="Intercity VPN" <?php echo e(($full && $full->service_type === 'Intercity VPN') ? 'selected' : ''); ?>>Intercity VPN</option>
                <option value="Carrier Services" <?php echo e(($full && $full->service_type === 'Carrier Services') ? 'selected' : ''); ?>>Carrier Services</option>
                <option value="E-Vending" <?php echo e(($full && $full->service_type === 'E-Vending') ? 'selected' : ''); ?>>E-Vending</option>
                <option value="Dark-Fibre" <?php echo e(($full && $full->service_type === 'Dark-Fibre') ? 'selected' : ''); ?>>Dark-Fibre</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Capacity</label>
              <input type="text" name="capacity" value="<?php echo e($full ? $full->capacity : ''); ?>" class="form-control" placeholder="e.g. 100Mbps">
            </div>

           <div class="col-md-4">
             <label class="form-label">Contract Number</label>
             <input type="text" name="contract_number" value="<?php echo e($full ? $full->contract_number : ''); ?>" class="form-control" placeholder="e.g. CTR-2025-001" readonly>
           </div>
           <div class="col-md-4"> 
            <label class="form-label">SAP Codes</label> 
            <input type="text" name="sapcodes" value="<?php echo e($full ? $full->sapcodes : ''); ?>" class="form-control" placeholder="e.g. SAP-ABC-123" readonly>
           </div>
           <div class="col-md-4">
             <label class="form-label">Quantity</label>
             <input type="number" name="quantity" value="<?php echo e($full ? $full->quantity : ''); ?>" class="form-control" min="0" placeholder="e.g. 1" readonly>
           </div>
           <div class="col-md-12">
             <label class="form-label">Comment</label>
             <input type="text" name="comment" value="<?php echo e($full ? $full->comment : ''); ?>" class="form-control" placeholder="Optional notes" readonly>
           </div>
            <div class="col-md-4">
              <label class="form-label">City/Town</label>
              <select name="city_id" class="form-select" required>
                 <option value="" selected disabled>Select City</option>
                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($city->id); ?>"><?php echo e($city->city); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Location</label>
              <?php $uniqueSuburbs = collect($suburbs)->unique('id'); ?>
              <select name="suburb_id" class="form-select" required>
                <option value="" selected disabled>Select Location</option>
                <?php $__currentLoopData = $uniqueSuburbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->suburb); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Pop</label>
              <?php $uniquePops = collect($pops)->unique('id'); ?>
              <select name="pop_id" class="form-select" required>
                <option value="" selected disabled>Select Pop</option>
                <?php $__currentLoopData = $uniquePops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($p->id); ?>"><?php echo e($p->pop); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Link Type</label>
              <select name="linkType_id" class="form-select" required>
                <?php $__currentLoopData = $linkTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($lt->id); ?>" <?php echo e(($full && $full->linkType_id == $lt->id) ? 'selected' : ''); ?>><?php echo e($lt->linkType); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
          </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/links/edit_modal.blade.php ENDPATH**/ ?>