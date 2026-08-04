<!-- Edit User Modal -->
<div class="modal custom-modal fade" id="editUserModal-<?php echo e($user->id); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editUserModalLabel-<?php echo e($user->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel-<?php echo e($user->id); ?>"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-edit-form-<?php echo e($user->id); ?>" action="<?php echo e(route('users.update', $user->id )); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e($user->name); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e($user->email); ?>" required>
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
unset($__errorArgs, $__bag); ?>" name="department_id" data-selected="<?php echo e($user->department_id); ?>">
                <?php $__currentLoopData = $department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($depart->id); ?>" <?php echo e((int)$user->department_id === (int)$depart->id ? 'selected' : ''); ?>><?php echo e($depart->department); ?></option>
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
unset($__errorArgs, $__bag); ?>" name="section_id" data-selected="<?php echo e($user->section_id); ?>">
                <?php if(isset($user->section_id)): ?>
                  <option selected value="<?php echo e($user->section_id); ?>"><?php echo e($user->section); ?></option>
                <?php endif; ?>
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
unset($__errorArgs, $__bag); ?>" name="position_id" data-selected="<?php echo e($user->position_id); ?>">
                <?php if(isset($user->position_id)): ?>
                  <option selected value="<?php echo e($user->position_id); ?>"><?php echo e($user->position); ?></option>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <?php echo Form::select('roles[]', $roles, $user->getRoleNames(), ['class' => 'form-select']); ?>

            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" name="user_status">
                <?php if(isset($user->user_status)): ?>
                  <option selected value="<?php echo e($user->user_status); ?>"><?php echo e($user->status_name ?? 'Current Status'); ?></option>
                <?php endif; ?>
                <?php $__currentLoopData = $user_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(!isset($user->user_status) || $status->id !== $user->user_status): ?>
                    <option value="<?php echo e($status->id); ?>"><?php echo e($status->status_name); ?></option>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Region</label>
              <select class="form-select <?php $__errorArgs = ['region'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="region">
                <option value="" <?php echo e(empty($user->region) ? 'selected' : ''); ?>>Not set</option>
                <?php if(isset($regions)): ?>
                  <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($region); ?>" <?php echo e($user->region === $region ? 'selected' : ''); ?>><?php echo e($region); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Access</label>
              <select class="form-select <?php $__errorArgs = ['is_access'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="is_access">
                <option value="0" <?php echo e((string)($user->is_access ?? '0')==='0' ? 'selected' : ''); ?>>Enabled</option>
                <option value="1" <?php echo e((string)($user->is_access ?? '0')==='1' ? 'selected' : ''); ?>>Disabled</option>
              </select>
              <small class="text-muted">Only Enabled (0) users can sign in.</small>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control <?php $__errorArgs = ['phonenumber'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phonenumber" value="<?php echo e($user->phonenumber); ?>" placeholder="e.g. 0976123456">
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
                <option value="0" <?php echo e((string)($user->dashboard_auto_refresh_enabled ?? '0')==='0' ? 'selected' : ''); ?>>Disabled</option>
                <option value="1" <?php echo e((string)($user->dashboard_auto_refresh_enabled ?? '0')==='1' ? 'selected' : ''); ?>>Enabled</option>
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
unset($__errorArgs, $__bag); ?>" name="dashboard_refresh_seconds" min="10" max="3600" value="<?php echo e((int)($user->dashboard_refresh_seconds ?? 60)); ?>">
            </div>
          </div>

          <!-- Password fields removed; use dedicated Change Password modal -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal-<?php echo e($user->id); ?>">
          <i class="fas fa-key me-1"></i> Change Password
        </button>
        <button type="submit" form="user-edit-form-<?php echo e($user->id); ?>" class="btn btn-outline-primary">
          <i class="fas fa-save me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
<?php /**PATH /var/www/html/resources/views/users/edit_modal.blade.php ENDPATH**/ ?>