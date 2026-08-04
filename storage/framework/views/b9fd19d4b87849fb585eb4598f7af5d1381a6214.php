<?php $__env->startSection('title'); ?>
Users
<?php $__env->stopSection(); ?>
<?php echo $__env->make('partials.css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('styles'); ?>
<style>
  .users-page .users-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto auto;
  }

  .users-page .toolbar-search-form,
  .users-page .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .users-page .users-status-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .users-page .users-status-primary {
    color: #111827 !important;
  }

  @media (max-width: 991.98px) {
    .users-page .users-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .users-page .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .users-page .users-toolbar {
      grid-template-columns: 1fr;
    }

    .users-page .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php
  $perPage = request('per_page', 20);
  $visibleUsers = collect($users->items());
  $enabledUsers = $visibleUsers->where('is_access', 0)->count();
  $disabledUsers = $visibleUsers->where('is_access', 1)->count();
?>

<section class="content workflow-faults-page users-page">
  <div class="workspace-summary-grid">
    <div class="workspace-summary-card" style="--summary-color:#6366F1;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-users"></i></span>
          <div>
            <div class="workspace-summary-label">Total Users</div>
            <div class="workspace-summary-title">Directory coverage</div>
          </div>
        </div>
        <div class="workspace-summary-value"><?php echo e($users->total()); ?></div>
      </div>
    </div>

    <div class="workspace-summary-card" style="--summary-color:#10B981;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-check"></i></span>
          <div>
            <div class="workspace-summary-label">Access Enabled</div>
            <div class="workspace-summary-title">Visible on this page</div>
          </div>
        </div>
        <div class="workspace-summary-value"><?php echo e($enabledUsers); ?></div>
      </div>
    </div>

    <div class="workspace-summary-card" style="--summary-color:#F59E0B;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-lock"></i></span>
          <div>
            <div class="workspace-summary-label">Access Disabled</div>
            <div class="workspace-summary-title">Needs review</div>
          </div>
        </div>
        <div class="workspace-summary-value"><?php echo e($disabledUsers); ?></div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Users</h3>
        <div class="page-lead">Search, review, edit, and manage platform access from one responsive workspace with dark-theme friendly controls.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-user-friends"></i> <?php echo e($users->total()); ?> total records</span>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-create')): ?>
          <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus-circle me-1"></i> Create User
          </button>
        <?php endif; ?>
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar users-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="usersPageSize" class="form-select" aria-label="Rows per page">
              <option value="10" <?php echo e((int)$perPage===10 ? 'selected' : ''); ?>>Show 10</option>
              <option value="20" <?php echo e((int)$perPage===20 ? 'selected' : ''); ?>>Show 20</option>
              <option value="50" <?php echo e((int)$perPage===50 ? 'selected' : ''); ?>>Show 50</option>
              <option value="100" <?php echo e((int)$perPage===100 ? 'selected' : ''); ?>>Show 100</option>
            </select>
          </div>
        </div>

        <form id="usersSearchForm" method="GET" action="<?php echo e(route('users.index')); ?>" class="toolbar-search-form">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" value="<?php echo e(request('q','')); ?>" class="form-control" placeholder="Search users, emails, roles, departments, or sections">
            <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
          </div>
        </form>

        <button type="submit" form="usersSearchForm" class="btn btn-primary btn-sm px-3">
          <i class="fas fa-search me-1"></i> Search
        </button>

        <a href="<?php echo e(route('users.index', ['per_page' => $perPage])); ?>" class="btn btn-outline-secondary btn-sm px-3">
          <i class="fas fa-rotate-left me-1"></i> Reset
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover faults-table" id="usersTable">
          <thead>
            <tr>
              <th>No.</th>
              <th>User</th>
              <th>Roles</th>
              <th>Department</th>
              <th>Section</th>
              <th>Status</th>
              <th class="text-end">Action(s)</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>
                  <span class="age-ticker">#<?php echo e($users->firstItem() + $loop->index); ?></span>
                </td>
                <td>
                  <div class="workspace-cell-main"><?php echo e($user->name); ?></div>
                  <div class="workspace-cell-sub"><?php echo e($user->email); ?></div>
                  <div class="workspace-cell-sub">Last login: <?php echo e($user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never'); ?></div>
                </td>
                <td>
                  <div class="workspace-chip-stack">
                    <?php $__empty_2 = true; $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                      <span class="badge rounded-pill" style="background: rgba(14, 165, 233, .12); color: #0369A1;"><?php echo e($v); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                      <span class="workspace-cell-sub">No roles assigned</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <div class="workspace-cell-main"><?php echo e($user->department ?: 'Not assigned'); ?></div>
                  <div class="workspace-cell-sub">Department mapping</div>
                </td>
                <td>
                  <div class="workspace-cell-main"><?php echo e($user->section ?: 'Not assigned'); ?></div>
                  <div class="workspace-cell-sub">Section coverage</div>
                </td>
                <td class="text-nowrap">
                  <div class="users-status-stack">
                    <span class="badge rounded-pill users-status-primary" style="background-color: <?php echo e(App\Models\UserStatus::STATUS_COLOR[$user->status_name] ?? '#CBD5E1'); ?>;">
                      <?php echo e($user->status_name); ?>

                    </span>
                    <span class="badge rounded-pill <?php echo e(((int)($user->is_access ?? 0) === 0) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'); ?>">
                      <?php echo e(((int)($user->is_access ?? 0) === 0) ? 'Enabled' : 'Disabled'); ?>

                    </span>
                  </div>
                </td>
                <td>
                  <div class="workspace-actions">
                    <form action="<?php echo e(route('users.destroy',$user->id)); ?>" method="POST">
                      <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showUserModal-<?php echo e($user->id); ?>">
                        <i class="fas fa-eye me-1"></i> View
                      </button>
                      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-edit')): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal-<?php echo e($user->id); ?>">
                          <i class="fas fa-edit me-1"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm <?php echo e(((int)($user->is_access ?? 0) === 0) ? 'btn-outline-danger' : 'btn-outline-success'); ?>" data-bs-toggle="modal" data-bs-target="#accessUserModal-<?php echo e($user->id); ?>">
                          <i class="fas fa-user-lock me-1"></i> <?php echo e(((int)($user->is_access ?? 0) === 0) ? 'Disable' : 'Enable'); ?>

                        </button>
                      <?php endif; ?>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="fas fa-info-circle me-1"></i> No users found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="faults-table-footer">
        <small class="text-muted">
          <?php if($users->count()): ?>
            Showing <?php echo e($users->firstItem()); ?> to <?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?> results
          <?php else: ?>
            Showing 0 results
          <?php endif; ?>
        </small>
        <?php echo e($users->appends(request()->except('page'))->links('pagination::bootstrap-5')); ?>

      </div>
      <div id="usersPager" class="mt-2"></div>
    </div>
  </div>

<?php echo $__env->make('users.create_modal', ['roles' => $roles, 'department' => $department, 'section' => $section, 'position' => $position, 'user_statuses' => $user_statuses, 'regions' => $regions, 'currentUserRegion' => $currentUserRegion], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php echo $__env->make('users.show_modal', ['user' => $user, 'loginAudits' => $loginAuditsByUser->get($user->id, collect())], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('users.edit_modal', ['user' => $user, 'department' => $department, 'section' => $section, 'position' => $position, 'roles' => $roles, 'user_statuses' => $user_statuses, 'regions' => $regions], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('users.access_modal', ['user' => $user], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('users.change_password_modal', ['user' => $user], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo $__env->make('partials.users', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="createUserModal"], [id^="showUserModal-"], [id^="editUserModal-"], [id^="accessUserModal-"], [id^="changePasswordModal-"]').forEach(function(modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    const sel = document.getElementById('usersPageSize');
    if (sel) {
        sel.addEventListener('change', function(ev) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', ev.target.value);
            params.delete('page');
            window.location.assign(window.location.pathname + '?' + params.toString());
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/users/index.blade.php ENDPATH**/ ?>