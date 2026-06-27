<!-- Main Sidebar Container -->
<aside class="main-sidebar impaza-sidebar sidebar-dark-primary elevation-4" id="impazaSidebar">


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel d-flex bg-transparent">
            <div class="image w-100 text-left">
                <!-- <img src="{{ asset('img/impazamon-v2.png') }}" alt="iMPAZAMON" class="impaza-brand-logo light-mode-logo" decoding="async"> -->
                <img src="{{ asset('img/impazamon-v2-dark.png') }}" alt="iMPAZAMON" class="impaza-brand-logo " decoding="async">
            </div>
        </div>

        

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">Main</li>
            <li class="nav-item">
              <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>

            @canany(['fault-list','my-fault-list','assigned-fault-list','assessment-fault-list','chief-tech-clear-faults-list','noc-clear-faults-list','department-faults-list','manage-faults','referred-faults','resolved-faults-list'])
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
                <p>Managed</p>
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
            
            @can('assigned-fault-list')
            <li class="nav-item">
              <a href="{{ route('assign.index') }}" class="nav-link {{ request()->routeIs('assign.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Assigned</p>
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
                <p>Rectified</p>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('chief-tech-escalations.*') ? 'active' : '' }}" href="{{ route('chief-tech-escalations.index') }}">
                <i class="nav-icon fas fa-level-up-alt"></i>
                <p>Escalations</p>
              </a>
            </li>
            @endcan
            @can('noc-clear-faults-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('noc-clear.*') ? 'active' : '' }}" href="{{ route('noc-clear.index') }}">
                <i class="nav-icon fas fa-broom"></i>
                <p>Resolved</p>
              </a>
            </li>
            @endcan
            @can('resolved-faults-list')
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('resolved.*') ? 'active' : '' }}" href="{{ route('resolved.index') }}">
                <i class="nav-icon fas fa-check-circle"></i>
                <p>Cleared</p>
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

            @can('referred-faults')
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('referred_faults.index') ? 'active' : '' }}" href="{{ route('referred_faults.index') }}">
                  <i class="nav-icon fas fa-share-square"></i>
                  <p>Referred</p>
                </a>
              </li>
            @endcan

            @canany(['surveys-list'])
              <li class="nav-header">Surveys</li>
            @endcanany
            @can('surveys-list')
              <li class="nav-item">
                <a href="{{ route('lte-site-surveys.index') }}" class="nav-link {{ request()->routeIs('lte-site-surveys.*') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-clipboard"></i>
                  <p>LTE Site Surveys</p>
                </a>
              </li>
            @endcan
            @can('surveys-list')
              <li class="nav-item">
                <a href="{{ route('customer-connectivity-surveys.index') }}" class="nav-link {{ request()->routeIs('customer-connectivity-surveys.*') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-wifi"></i>
                  <p>Customer Surveys</p>
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
            <li class="nav-item">
              <a href="{{ route('zones.index') }}" class="nav-link {{ request()->routeIs('zones.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marked-alt"></i>
                <p>Zones</p>
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

            @canany(['reports','call-centre-reports','performance-reports'])
            <li class="nav-header">Reports</li>
            @endcanany
            @can('reports')
            <li class="nav-item">
              <a href="{{ route('dashboard.reports') }}" class="nav-link {{ request()->routeIs('dashboard.reports') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reports</p>
              </a>
            </li>
            @endcan
            @can('call-centre-reports')
            <li class="nav-item">
              <a href="{{ route('call_centre.reports') }}" class="nav-link {{ request()->routeIs('call_centre.reports') ? 'active' : '' }}">
                <i class="nav-icon fas fa-headset"></i>
                <p>Contact Centre</p>
              </a>
            </li>
            @endcan

            @can('user-list')
            <li class="nav-item">
              <a href="{{ route('system-usage-settings.edit') }}" class="nav-link {{ request()->routeIs('system-usage-settings.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-envelope-open-text"></i>
                <p>Usage Mail</p>
              </a>
            </li>
            @endcan

            @can('performance-reports')
            <li class="nav-item">
              <a href="{{ route('performance.index') }}" class="nav-link {{ request()->routeIs('performance.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Performance</p>
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

        @auth
          @php
            $impazaUser = Auth::user();
            $impazaInitial = strtoupper(mb_substr((string)($impazaUser->name ?? 'U'), 0, 1));
            $impazaRole = data_get($impazaUser, 'role');
            if (!$impazaRole && is_object($impazaUser) && method_exists($impazaUser, 'getRoleNames')) {
              try { $impazaRole = $impazaUser->getRoleNames()->first(); } catch (\Throwable $e) { $impazaRole = null; }
            }
          @endphp
          <div class="impaza-sidebar-footer">
            <div class="impaza-sidebar-user">
              <div class="avatar">{{ $impazaInitial }}</div>
              <div class="meta">
                <div class="name">{{ $impazaUser->name }}</div>
                <div class="role">{{ $impazaRole ?: 'Signed in' }}</div>
              </div>
              <div class="actions">
                <a href="{{ route('user.profile') }}" title="Profile" aria-label="Profile"><i class="fas fa-user"></i></a>
                <a href="{{ route('logout') }}" title="Logout" aria-label="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-power-off"></i></a>
              </div>
            </div>
          </div>
        @endauth
    </div>
    <!-- /.sidebar -->
</aside>
