<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-create')): ?>
<div class="modal custom-modal fade" id="customerCreateModal" tabindex="-1" aria-labelledby="customerCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="customerCreateModalLabel"><i class="fas fa-users me-2"></i>Create Customers</h5>
          <div class="text-muted small mt-1">Add one or more customer records in the same modern workflow used across the refreshed business pages.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-layer-group"></i> Bulk Create</span>
            <span class="fault-modal-meta-item"><i class="fas fa-user-tie"></i> Manager Mapping</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('customers.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Use <strong>Add another</strong> to capture multiple customer records at once while validating duplicate names and account numbers.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-users"></i></span>
              <div>
                <div class="fault-modal-section-title">Customer Entries</div>
                <div class="fault-modal-section-subtitle">Capture customer profile, account, and ownership details for each new record.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="repeater" id="customerRepeater">
                <div class="repeater-items">
                  <div class="repeater-item border rounded p-3 mb-3">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-building"></i></span>
                          <input type="text" name="items[0][customer]" class="form-control customer-name-input" placeholder="e.g. Acme Corp" required>
                        </div>
                        <div class="invalid-feedback">This customer name already exists.</div>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Account Number</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                          <input type="text" name="items[0][account_number]" class="form-control account-number-input" placeholder="e.g. 123456789" required>
                        </div>
                        <div class="invalid-feedback">This account number already exists.</div>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Contract Number</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                          <input type="text" name="items[0][contract_number]" class="form-control" placeholder="e.g. 40000001">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Account Manager</label>
                        <select name="items[0][account_manager_id]" class="form-select">
                          <option value="" selected disabled>Select Account Manager</option>
                          <?php if(isset($accountManagers)): ?>
                            <?php $__currentLoopData = $accountManagers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($am->am_id); ?>"><?php echo e($am->name ?? ('User #'.$am->user_id)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          <?php endif; ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="items[0][address]" class="form-control" placeholder="Address">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="items[0][contact_number]" class="form-control" placeholder="Contact Number">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addCustomerRepeaterItem"><i class="fas fa-plus me-1"></i> Add another</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="removeCustomerRepeaterItem"><i class="fas fa-minus me-1"></i> Remove last</button>
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
<?php /**PATH /var/www/html/resources/views/customers/create_modal.blade.php ENDPATH**/ ?>