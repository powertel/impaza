<!-- Create User Modal -->
<div class="modal custom-modal fade" id="createUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel"><i class="fas fa-user-plus me-2"></i>Create User</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-create-form" action="<?php echo e(route('users.store')); ?>" method="POST">
          <?php echo e(csrf_field()); ?>

          <div class="row g-3">
            <div class="col-md-4">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" placeholder="Name" value="<?php echo e(old('name')); ?>">
            </div>
            <div class="col-md-4">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" placeholder="Email" value="<?php echo e(old('email')); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control <?php $__errorArgs = ['phonenumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phonenumber" placeholder="e.g. 263776123456" value="<?php echo e(old('phonenumber')); ?>">
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <select class="form-select department-select <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="department_id">
                <option selected disabled>Select Department</option>
                <?php $__currentLoopData = $department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(old('department_id')==$depart->id): ?>
                    <option value="<?php echo e($depart->id); ?>" selected><?php echo e($depart->department); ?></option>
                  <?php else: ?>
                    <option value="<?php echo e($depart->id); ?>"><?php echo e($depart->department); ?></option>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select class="form-select section-select <?php $__errorArgs = ['section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="section_id">
                <option selected disabled>Select Section</option>
                <?php $__currentLoopData = $section; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sect): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(old('section_id')==$sect->id): ?>
                    <option value="<?php echo e($sect->id); ?>" selected><?php echo e($sect->section); ?></option>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <select class="form-select position-select <?php $__errorArgs = ['position_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="position_id">
                <option selected disabled>Select Position</option>
                <?php $__currentLoopData = $position; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(old('position_id')==$pos->id): ?>
                    <option value="<?php echo e($pos->id); ?>" selected><?php echo e($pos->position); ?></option>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <?php echo Form::select('roles[]', $roles, [], ['class' => 'form-select']); ?>

            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select <?php $__errorArgs = ['user_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="user_status">
                <option selected disabled>Select Status</option>
                <?php $__currentLoopData = $user_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(old('user_status')==$status->id): ?>
                    <option value="<?php echo e($status->id); ?>" selected><?php echo e($status->status_name); ?></option>
                  <?php else: ?>
                    <option value="<?php echo e($status->id); ?>"><?php echo e($status->status_name); ?></option>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Region</label>
              <select class="form-select <?php $__errorArgs = ['region'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="region">
                <option value="" disabled <?php echo e(old('region', $currentUserRegion ?? '') ? '' : 'selected'); ?>>Select Region</option>
                <?php if(isset($regions)): ?>
                  <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($region); ?>" <?php echo e(old('region', $currentUserRegion ?? '') === $region ? 'selected' : ''); ?>><?php echo e($region); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
              </select>
              <small class="text-muted">Defaults to your region: <?php echo e($currentUserRegion ?? 'Not set'); ?></small>
            </div>
            <div class="col-md-3">
              <label class="form-label">Access</label>
              <select class="form-select <?php $__errorArgs = ['is_access'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="is_access">
                <option value="0" <?php echo e(old('is_access','0')==='0' ? 'selected' : ''); ?>>Enabled</option>
                <option value="1" <?php echo e(old('is_access')==='1' ? 'selected' : ''); ?>>Disabled</option>
              </select>
              <small class="text-muted">Only Enabled users can sign in.</small>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Dashboard Auto Refresh</label>
              <select class="form-select <?php $__errorArgs = ['dashboard_auto_refresh_enabled'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="dashboard_auto_refresh_enabled">
                <option value="0" <?php echo e(old('dashboard_auto_refresh_enabled','0')==='0' ? 'selected' : ''); ?>>Disabled</option>
                <option value="1" <?php echo e(old('dashboard_auto_refresh_enabled')==='1' ? 'selected' : ''); ?>>Enabled</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Refresh Interval (seconds)</label>
              <input type="number" class="form-control <?php $__errorArgs = ['dashboard_refresh_seconds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="dashboard_refresh_seconds" min="10" max="3600" value="<?php echo e((int) old('dashboard_refresh_seconds', 60)); ?>">
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <div class="password-wrapper">
            <input id="create_password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="Password" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+" autocomplete="new-password">
                <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="create_password">
                  <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                  <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                </button>
              </div>
              <div class="password-strength-meter"><div class="strength-bar"></div></div>
              <div class="password-strength-text"></div>
              <small class="text-muted">Minimum 8 characters with uppercase, lowercase, number, and special character.</small>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <div class="password-wrapper">
            <input id="create_password_confirm" type="password" class="form-control <?php $__errorArgs = ['confirm-password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="confirm-password" placeholder="Confirm Password" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+" autocomplete="new-password">
                <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="create_password_confirm">
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
        <button type="submit" form="user-create-form" class="btn btn-outline-primary">
          <i class="fas fa-save me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/resources/views/users/create_modal.blade.php ENDPATH**/ ?>