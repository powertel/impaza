<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-delete')): ?>
<?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal custom-modal fade" id="customerDeleteModal<?php echo e($customer->id); ?>" tabindex="-1" aria-labelledby="customerDeleteModalLabel<?php echo e($customer->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="customerDeleteModalLabel<?php echo e($customer->id); ?>"><i class="fas fa-trash me-2"></i>Delete Customer</h5>
          <div class="text-muted small mt-1">This action permanently removes the selected customer record from the business workspace.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-0">
          <i class="fas fa-triangle-exclamation"></i>
          <div>Are you sure you want to delete <strong><?php echo e($customer->customer); ?></strong>? This action cannot be undone.</div>
        </div>
      </div>
      <div class="modal-footer">
        <form action="<?php echo e(route('customers.destroy', $customer->id)); ?>" method="POST" class="d-inline">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="fas fa-trash me-1"></i> Delete
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/customers/delete_modal.blade.php ENDPATH**/ ?>