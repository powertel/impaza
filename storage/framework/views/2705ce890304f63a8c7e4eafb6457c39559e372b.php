<!-- Access (Enable/Disable) Modal -->
<div class="modal custom-modal fade" id="accessUserModal-<?php echo e($user->id); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="accessUserModalLabel-<?php echo e($user->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accessUserModalLabel-<?php echo e($user->id); ?>"><i class="fas fa-user-lock me-2"></i>Account Access</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-access-form-<?php echo e($user->id); ?>" action="<?php echo e(route('users.access', $user->id)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PATCH'); ?>
          <p class="mb-2">
            Current: <strong><?php echo e(((int)($user->is_access ?? 0) === 0) ? 'Enabled' : 'Disabled'); ?></strong>
          </p>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="is_access" id="enableUser-<?php echo e($user->id); ?>" value="0" <?php echo e(((int)($user->is_access ?? 0) === 0) ? 'checked' : ''); ?>>
            <label class="form-check-label" for="enableUser-<?php echo e($user->id); ?>">
              Enable (allow login)
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="is_access" id="disableUser-<?php echo e($user->id); ?>" value="1" <?php echo e(((int)($user->is_access ?? 0) === 1) ? 'checked' : ''); ?>>
            <label class="form-check-label" for="disableUser-<?php echo e($user->id); ?>">
              Disable (block login)
            </label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" form="user-access-form-<?php echo e($user->id); ?>" class="btn btn-outline-primary">
          <i class="fas fa-save me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/resources/views/users/access_modal.blade.php ENDPATH**/ ?>