
<!-- Change Password Modal -->
<div class="modal custom-modal fade" id="changePasswordModal-<?php echo e($user->id); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePasswordModalLabel-<?php echo e($user->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel-<?php echo e($user->id); ?>"><i class="fas fa-key me-2"></i>Change Password</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-change-password-form-<?php echo e($user->id); ?>" action="<?php echo e(route('users.change-password', $user->id)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">New Password</label>
              <div class="password-wrapper">
    <input id="change_password_<?php echo e($user->id); ?>" type="password" class="form-control <?php $__errorArgs = ['newpassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="newpassword" required minlength="8" maxlength="30" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+" autocomplete="new-password">
                <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="change_password_<?php echo e($user->id); ?>">
                  <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                </button>
              </div>
              <div class="password-strength-meter"><div class="strength-bar"></div></div>
              <div class="password-strength-text"></div>
              <small class="text-muted">Minimum 8 characters with uppercase, lowercase, number, and special character.</small>
              <?php $__errorArgs = ['newpassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12">
              <label class="form-label">Confirm New Password</label>
              <div class="password-wrapper">
    <input id="change_password_confirm_<?php echo e($user->id); ?>" type="password" class="form-control" name="newpassword_confirmation" required minlength="8" maxlength="30" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+" autocomplete="new-password">
                <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="change_password_confirm_<?php echo e($user->id); ?>">
                  <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" form="user-change-password-form-<?php echo e($user->id); ?>" class="btn btn-outline-warning">
          <i class="fas fa-save me-1"></i> Update Password
        </button>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/resources/views/users/change_password_modal.blade.php ENDPATH**/ ?>