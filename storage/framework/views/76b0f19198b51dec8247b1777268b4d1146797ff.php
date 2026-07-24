<!-- Main Sidebar Container -->
<aside class="main-sidebar impaza-sidebar sidebar-dark-primary elevation-4" id="impazaSidebar">


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel d-flex bg-transparent">
            <div class="image w-100 text-left">
                <!-- <img src="<?php echo e(asset('img/impazamon-v2.png')); ?>" alt="iMPAZAMON" class="impaza-brand-logo light-mode-logo" decoding="async"> -->
                <img src="<?php echo e(asset('img/impazamon-v2-dark.png')); ?>" alt="iMPAZAMON" class="impaza-brand-logo " decoding="async">
            </div>
        </div>

        

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">Main</li>
            <li class="nav-item">
              <a href="<?php echo e(route('home')); ?>" class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['fault-list','my-fault-list','assigned-fault-list','assessment-fault-list','chief-tech-clear-faults-list','noc-clear-faults-list','department-faults-list','manage-faults','referred-faults','resolved-faults-list'])): ?>
              <li class="nav-header">Faults</li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fault-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('faults.index')); ?>" class="nav-link <?php echo e(request()->routeIs('faults.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                <p>Log</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('my-fault-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('my_faults.index')); ?>" class="nav-link <?php echo e(request()->routeIs('my_faults.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-satellite-dish"></i>
                <p>My Faults</p>
              </a>
            </li>
            <?php endif; ?>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-faults')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('manage.faults')); ?>" class="nav-link <?php echo e(request()->routeIs('manage.faults') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Managed</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assign-fault')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('assign.create')); ?>" class="nav-link <?php echo e(request()->routeIs('assign.create') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-user-check"></i>
                <p>Assign</p>
              </a>
            </li>
            <?php endif; ?>
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assigned-fault-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('assign.index')); ?>" class="nav-link <?php echo e(request()->routeIs('assign.index') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Assigned</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('assessment-fault-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('assessments.*') ? 'active' : ''); ?>" href="<?php echo e(route('assessments.index')); ?>">
                <i class="nav-icon fas fa-clipboard-check"></i>
                <p>Assess</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo e(route('rfos.index')); ?>" class="nav-link <?php echo e(request()->routeIs('rfos.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-list-alt"></i>
                <p>RFO</p>
              </a>
            </li>

            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('chief-tech-clear-faults-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('chief-tech-clear.*') ? 'active' : ''); ?>" href="<?php echo e(route('chief-tech-clear.index')); ?>">
                <i class="nav-icon fas fa-wrench"></i>
                <p>Rectified</p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('chief-tech-escalations.*') ? 'active' : ''); ?>" href="<?php echo e(route('chief-tech-escalations.index')); ?>">
                <i class="nav-icon fas fa-level-up-alt"></i>
                <p>Escalations</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('noc-clear-faults-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('noc-clear.*') ? 'active' : ''); ?>" href="<?php echo e(route('noc-clear.index')); ?>">
                <i class="nav-icon fas fa-broom"></i>
                <p>Resolved</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('resolved-faults-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('resolved.*') ? 'active' : ''); ?>" href="<?php echo e(route('resolved.index')); ?>">
                <i class="nav-icon fas fa-check-circle"></i>
                <p>Cleared</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department-faults-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('department_faults.*') ? 'active' : ''); ?>" href="<?php echo e(route('department_faults.index')); ?>">
                <i class="nav-icon fas fa-satellite"></i>
                <p>Department</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('referred-faults')): ?>
              <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('referred_faults.index') ? 'active' : ''); ?>" href="<?php echo e(route('referred_faults.index')); ?>">
                  <i class="nav-icon fas fa-share-square"></i>
                  <p>Referred</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['surveys-list'])): ?>
              <li class="nav-header">Surveys</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('surveys-list')): ?>
              <li class="nav-item">
                <a href="<?php echo e(route('lte-site-surveys.index')); ?>" class="nav-link <?php echo e(request()->routeIs('lte-site-surveys.*') ? 'active' : ''); ?>">
                  <i class="nav-icon fas fa-clipboard"></i>
                  <p>LTE Site Surveys</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('surveys-list')): ?>
              <li class="nav-item">
                <a href="<?php echo e(route('customer-connectivity-surveys.index')); ?>" class="nav-link <?php echo e(request()->routeIs('customer-connectivity-surveys.*') ? 'active' : ''); ?>">
                  <i class="nav-icon fas fa-wifi"></i>
                  <p>Customer Surveys</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['permit-list'])): ?>
              <li class="nav-header">Permits</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permit-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('permits.*') ? 'active' : ''); ?>" href="<?php echo e(route('permits.index')); ?>">
                <i class="nav-icon fas fa-check-circle"></i>
                <p>Approved Permits</p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('request-permit.*') ? 'active' : ''); ?>" href="<?php echo e(route('request-permit.index')); ?>">
                <i class="nav-icon fas fa-plus-square"></i>
                <p>Requested Permits</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['finance'])): ?>
              <li class="nav-header">Finance</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('finance')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('finance.index')); ?>" class="nav-link <?php echo e(request()->routeIs('finance.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-money-check-alt"></i>
                <p>Finance</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['technician-configuration'])): ?>
            <li class="nav-header">Configuration</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('technician-configuration')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('technicians.config')); ?>" class="nav-link <?php echo e(request()->routeIs('technicians.config') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>Technician Settings</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('zones.index')); ?>" class="nav-link <?php echo e(request()->routeIs('zones.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-map-marked-alt"></i>
                <p>Zones</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['department-list'])): ?>
              <li class="nav-header">Organization</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('departments.index')); ?>" class="nav-link <?php echo e(request()->routeIs('departments.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-building"></i>
                <p>Departments</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('sections.index')); ?>" class="nav-link <?php echo e(request()->routeIs('sections.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>Sections</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('department-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('positions.index')); ?>" class="nav-link <?php echo e(request()->routeIs('positions.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-briefcase"></i>
                <p>Positions</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['city-list','location-list','pop-list'])): ?>
              <li class="nav-header">Network</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('city-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('cities.index')); ?>" class="nav-link <?php echo e(request()->routeIs('cities.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-city"></i>
                <p>Cities</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('location-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('locations.index')); ?>" class="nav-link <?php echo e(request()->routeIs('locations.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-map-marker-alt"></i>
                <p>Locations</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pop-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('pops.index')); ?>" class="nav-link <?php echo e(request()->routeIs('pops.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-bullseye"></i>
                <p>Pops</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['customer-list','link-list','account-manager-list'])): ?>
              <li class="nav-header">Business</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('customers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-address-card"></i>
                <p>Customers</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('link-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('links.index')); ?>" class="nav-link <?php echo e(request()->routeIs('links.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-link"></i>
                <p>Links</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('account-manager-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('account_managers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('account_managers.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Account Managers</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['reports','call-centre-reports','performance-reports'])): ?>
            <li class="nav-header">Reports</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('dashboard.reports')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard.reports') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reports</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('call-centre-reports')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('call_centre.reports')); ?>" class="nav-link <?php echo e(request()->routeIs('call_centre.reports') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-headset"></i>
                <p>Contact Centre</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-list')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('system-usage-settings.edit')); ?>" class="nav-link <?php echo e(request()->routeIs('system-usage-settings.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-envelope-open-text"></i>
                <p>Usage Mail</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('performance-reports')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('performance.index')); ?>" class="nav-link <?php echo e(request()->routeIs('performance.index') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Performance</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['user-list','role-list','permissions'])): ?>
              <li class="nav-header">User Management</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">
                <i class="nav-icon fas fa-user-cog"></i>
                <p>Users</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role-list')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>" href="<?php echo e(route('roles.index')); ?>">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>Roles</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('permission.*') ? 'active' : ''); ?>" href="<?php echo e(route('permission.index')); ?>">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>Permissions</p>
              </a>
            </li>
            <?php endif; ?>

            <li class="nav-header">Account</li>
            <li class="nav-item">
              <a href="<?php echo e(route('user.profile')); ?>" class="nav-link <?php echo e(request()->routeIs('user.profile') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-user"></i>
                <p>Profile</p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-power-off"></i>
                <p>Logout</p>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->

        <?php if(auth()->guard()->check()): ?>
          <?php
            $impazaUser = Auth::user();
            $impazaInitial = strtoupper(mb_substr((string)($impazaUser->name ?? 'U'), 0, 1));
            $impazaRole = data_get($impazaUser, 'role');
            if (!$impazaRole && is_object($impazaUser) && method_exists($impazaUser, 'getRoleNames')) {
              try { $impazaRole = $impazaUser->getRoleNames()->first(); } catch (\Throwable $e) { $impazaRole = null; }
            }
          ?>
          <div class="impaza-sidebar-footer">
            <div class="impaza-sidebar-user">
              <div class="avatar"><?php echo e($impazaInitial); ?></div>
              <div class="meta">
                <div class="name"><?php echo e($impazaUser->name); ?></div>
                <div class="role"><?php echo e($impazaRole ?: 'Signed in'); ?></div>
              </div>
              <div class="actions">
                <a href="<?php echo e(route('user.profile')); ?>" title="Profile" aria-label="Profile"><i class="fas fa-user"></i></a>
                <a href="<?php echo e(route('logout')); ?>" title="Logout" aria-label="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-power-off"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>
    </div>
    <!-- /.sidebar -->
</aside>
<?php /**PATH /var/www/html/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>