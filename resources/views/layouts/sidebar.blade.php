<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4" style="background:#fff;border-right:1px solid #eee;">
    <!-- Sidebar -->
    <div class="sidebar" style="display: flex; flex-direction: column; min-height: 0;">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel d-flex" style="flex: 0 0 auto;">
            <div class="info">
                <h3 class="d-block" style="color: orange;">IMPAZAMON</h3>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2" style="flex: 1 1 auto;">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="margin: 0; padding: 0;">
            <li class="nav-header">Main</li>
            <li class="nav-item">
              <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>

            @canany(['fault-list','my-fault-list','assigned-fault-list','assessment-fault-list','chief-tech-clear-faults-list','noc-clear-faults-list','department-faults-list'])
              <li class="nav-header">Faults</li>
            @endcanany
            @can('fault-list')
            <li class="nav-item">
              <a href="{{ route('faults.index') }}" class="nav-link {{ request()->routeIs('faults.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-exclamation-triangle"></i>
                <p>Log</p>
              </a>
            </li>
            @endcan
            @can('my-fault-list')
            <li class="nav-item">
              <a href="{{ route('my_faults.index') }}" class="nav-link {{ request()->routeIs('my_faults.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-satellite-dish"></i>
                <p>My Faults</p>
              </a>
            </li>
            @endcan
            
            @can('manage-faults')
            <li class="nav-item">
              <a href="{{ route('manage.faults') }}" class="nav-link {{ request()->routeIs('manage.faults') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Managed Faults</p>
              </a>
            </li>
            @endcan
            
            @can('assigned-fault-list')
            <li class="nav-item">
              <a href="{{ route('assign.index') }}" class="nav-link {{ request()->routeIs('assign.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Assigned</p>
              </a>
            </li>
            @endcan
            @can('assign-fault')
            <li class="nav-item">
              <a href="{{ route('assign.create') }}" class="nav-link {{ request()->routeIs('assign.create') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-check"></i>
                <p>Assign</p>
              </a>
            </li>
            @endcan
            @can('assessment-fault-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}" href="{{ route('assessments.index') }}">
                <i class="nav-icon fas fa-clipboard-check"></i>
                <p>Assess</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{ route('rfos.index') }}" class="nav-link {{ request()->routeIs('rfos.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-list-alt"></i>
                <p>RFO</p>
              </a>
            </li>

            @endcan
            @can('chief-tech-clear-faults-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('chief-tech-clear.*') ? 'active' : '' }}" href="{{ route('chief-tech-clear.index') }}">
                <i class="nav-icon fas fa-wrench"></i>
                <p>Clear</p>
              </a>
            </li>
            @endcan
            @can('noc-clear-faults-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('noc-clear.*') ? 'active' : '' }}" href="{{ route('noc-clear.index') }}">
                <i class="nav-icon fas fa-broom"></i>
                <p>NOC Clear</p>
              </a>
            </li>
            @endcan
            @can('department-faults-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('department_faults.*') ? 'active' : '' }}" href="{{ route('department_faults.index') }}">
                <i class="nav-icon fas fa-satellite"></i>
                <p>Department</p>
              </a>
            </li>
            @endcan

            @canany(['permit-list'])
              <li class="nav-header">Permits</li>
            @endcanany
            @can('permit-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('permits.*') ? 'active' : '' }}" href="{{ route('permits.index') }}">
                <i class="nav-icon fas fa-check-circle"></i>
                <p>Approved Permits</p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('request-permit.*') ? 'active' : '' }}" href="{{ route('request-permit.index') }}">
                <i class="nav-icon fas fa-plus-square"></i>
                <p>Requested Permits</p>
              </a>
            </li>
            @endcan

            @canany(['finance'])
              <li class="nav-header">Finance</li>
            @endcanany
            @can('finance')
            <li class="nav-item">
              <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-money-check-alt"></i>
                <p>Finance</p>
              </a>
            </li>
            @endcan

            @canany(['technician-configuration'])
            <li class="nav-header">Configuration</li>
            @endcanany
            @can('technician-configuration')
            <li class="nav-item">
              <a href="{{ route('technicians.config') }}" class="nav-link {{ request()->routeIs('technicians.config') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>Technician Settings</p>
              </a>
            </li>
            @endcan

            @canany(['department-list'])
              <li class="nav-header">Organization</li>
            @endcanany
            @can('department-list')
            <li class="nav-item">
              <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-building"></i>
                <p>Departments</p>
              </a>
            </li>
            @endcan
            @can('department-list')
            <li class="nav-item">
              <a href="{{ route('sections.index') }}" class="nav-link {{ request()->routeIs('sections.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>Sections</p>
              </a>
            </li>
            @endcan
            @can('department-list')
            <li class="nav-item">
              <a href="{{ route('positions.index') }}" class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-briefcase"></i>
                <p>Positions</p>
              </a>
            </li>
            @endcan

            @canany(['city-list','location-list','pop-list'])
              <li class="nav-header">Network</li>
            @endcanany
            @can('city-list')
            <li class="nav-item">
              <a href="{{ route('cities.index') }}" class="nav-link {{ request()->routeIs('cities.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-city"></i>
                <p>Cities</p>
              </a>
            </li>
            @endcan
            @can('location-list')
            <li class="nav-item">
              <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marker-alt"></i>
                <p>Locations</p>
              </a>
            </li>
            @endcan
            @can('pop-list')
            <li class="nav-item">
              <a href="{{ route('pops.index') }}" class="nav-link {{ request()->routeIs('pops.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-bullseye"></i>
                <p>Pops</p>
              </a>
            </li>
            @endcan

            @canany(['customer-list','link-list','account-manager-list'])
              <li class="nav-header">Business</li>
            @endcanany
            @can('customer-list')
            <li class="nav-item">
              <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-address-card"></i>
                <p>Customers</p>
              </a>
            </li>
            @endcan
            @can('link-list')
            <li class="nav-item">
              <a href="{{ route('links.index') }}" class="nav-link {{ request()->routeIs('links.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-link"></i>
                <p>Links</p>
              </a>
            </li>
            @endcan
            @can('account-manager-list')
            <li class="nav-item">
              <a href="{{ route('account_managers.index') }}" class="nav-link {{ request()->routeIs('account_managers.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tie"></i>
                <p>Account Managers</p>
              </a>
            </li>
            @endcan

            @canany(['reports'])
            <li class="nav-header">Reports</li>
            @endcanany
            @can('reports')
            <li class="nav-item">
              <a class="nav-link">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reports</p>
              </a>
            </li>
            @endcan

            @canany(['user-list','role-list','permissions'])
              <li class="nav-header">User Management</li>
            @endcanany
            @can('user-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="nav-icon fas fa-user-cog"></i>
                <p>Users</p>
              </a>
            </li>
            @endcan
            @can('role-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>Roles</p>
              </a>
            </li>
            @endcan
            @can('permissions')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('permission.*') ? 'active' : '' }}" href="{{ route('permission.index') }}">
                <i class="nav-icon fas fa-users-cog"></i>
                <p>Permissions</p>
              </a>
            </li>
            @endcan

            <li class="nav-header">Account</li>
            <li class="nav-item">
              <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user"></i>
                <p>Profile</p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-power-off"></i>
                <p>Logout</p>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
