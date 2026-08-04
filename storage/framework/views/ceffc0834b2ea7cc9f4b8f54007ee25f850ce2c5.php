<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-edit')): ?>
<?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal custom-modal fade" id="customerEditModal<?php echo e($customer->id); ?>" tabindex="-1" aria-labelledby="customerEditModalLabel<?php echo e($customer->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="customerEditModalLabel<?php echo e($customer->id); ?>"><i class="fas fa-pen-to-square me-2"></i>Edit Customer</h5>
          <div class="text-muted small mt-1">Update this customer profile, assignment, and contact details using the refreshed business modal layout.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-users"></i> <?php echo e($customer->customer); ?></span>
            <span class="fault-modal-meta-item"><i class="fas fa-hashtag"></i> <?php echo e($customer->account_number ?? 'No Account'); ?></span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('customers.update', $customer->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changes here update how the customer appears across linked views, account ownership, and service records.</div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-users"></i></span>
              <div>
                <div class="fault-modal-section-title">Customer Profile</div>
                <div class="fault-modal-section-subtitle">Update customer identity, assignment, and contact details.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-3">
                <label class="form-label">Customer</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-building"></i></span>
                  <input type="text" name="customer" class="form-control customer-name-input" value="<?php echo e($customer->customer); ?>" required data-ignore-id="<?php echo e($customer->id); ?>">
                </div>
                <div class="invalid-feedback">This customer name already exists.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Account Number</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                  <input type="text" name="account_number" class="form-control account-number-input" value="<?php echo e($customer->account_number ?? ''); ?>" required data-ignore-id="<?php echo e($customer->id); ?>" readonly>
                </div>
                <div class="invalid-feedback">This account number already exists.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Contract Number</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                  <input type="text" name="contract_number" class="form-control" value="<?php echo e($customer->contract_number ?? ''); ?>" placeholder="e.g. CTR-00001">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Account Manager</label>
                <select name="account_manager_id" class="form-select" required>
                  <option value="" disabled <?php echo e(empty($customer->account_manager_id) ? 'selected' : ''); ?>>Select Account Manager</option>
                  <?php if(isset($accountManagers)): ?>
                    <?php $__currentLoopData = $accountManagers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($am->am_id); ?>" <?php echo e((int)$customer->account_manager_id === (int)$am->am_id ? 'selected' : ''); ?>>
                        <?php echo e($am->name ?? ('User #'.$am->user_id)); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php endif; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo e($customer->address ?? ''); ?>" placeholder="Address">
              </div>
              <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="<?php echo e($customer->contact_number ?? ''); ?>" placeholder="Contact Number">
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_pop_aggregator" value="1" id="is_pop_aggregator_<?php echo e($customer->id); ?>" <?php echo e(!empty($customer->is_pop_aggregator) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="is_pop_aggregator_<?php echo e($customer->id); ?>">
                  POP Aggregator Customer
                </label>
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
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/customers/edit_modal.blade.php ENDPATH**/ ?>