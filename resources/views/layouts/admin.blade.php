<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="sidebar-mini" style="height: auto;">
    <div class="wrapper" id="app">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index3.html" class="nav-link">Home</a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
              <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fa fa-user" style="color: rgb(35, 157, 233);"></i> &nbsp;
                        <span class="align-middle d-none d-sm-inline-block" style="font-weight: 700; color: rgb(35, 157, 233);"> {{ Auth::user()->name }}</span> <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
                    </a>
                  <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">

                      <!-- item-->
                      <a  class="dropdown-item notify-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                              document.getElementById('logout-form').submit();">
                                  {{ __('Logout') }}
                          <i class="mdi mdi-logout me-1"></i>
                      </a>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                  @csrf
                      </form>
                  </div>
              </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="info">
                        <h3 class="d-block" style="color: orange;">IMPAZAMON</h3>
                    </div>
<!--                     <span class="logo-lg">
                            <img src="{{ asset('img/logo.png') }}">
                        </span>
                    </div> -->
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('home') }}"  class="nav-link">
                                <i class="nav-icon fas fa-chalkboard"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @can('user-list')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Configurations
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                @can('user-list')
                                <li class="nav-item">
                                    <a  class="nav-link" href="{{ route('users.index') }}">
                                        <i class="nav-icon fas fa-user-cog"></i>
                                        <p>Users</p>
                                    </a>
                                </li>                                
                                @endcan

                                @can('role-list')
                                <li class="nav-item">
                                    <a  class="nav-link" href="{{ route('roles.index') }}">
                                        <i class="fas fa-bomb nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>                                  
                                @endcan

                                @can('permissions')
                                <li class="nav-item">
                                    <a   class="nav-link" href="{{ route('permission.index') }}">
                                        <i class="fas fa-users-cog nav-icon"></i>
                                        <p>Permissions</p>
                                    </a>
                                </li>                                    
                                @endcan

                                @can('department-list')
                                <li class="nav-item">
                                    <a href="{{ route('departments.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-book-reader"></i>
                                        <p>Departments</p>
                                    </a>
                                </li>                                  
                                @endcan

                                @can('account-manager-list')
                                <li class="nav-item">
                                    <a href="{{ route('account_managers.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-user"></i>
                                        <p>Account Managers</p>
                                    </a>
                                </li>
                                @endcan

                                @can('city-list')
                                <li class="nav-item">
                                        <a href="{{ route('cities.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-city"></i>
                                        <p>Cities</p>
                                        </a>
                                </li>                                  
                                @endcan

                                @can('location-list')
                                <li class="nav-item">
                                        <a href="{{ route('locations.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-location"></i>
                                        <p>Locations</p>
                                        </a>
                                </li>                                  
                                @endcan

                                @can('pop-list')
                                <li class="nav-item">
                                        <a href="{{ route('pops.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-bullseye"></i>
                                        <p>Pops</p>
                                        </a>
                                </li>                                
                                @endcan

                                @can('customer-list')
                                <li class="nav-item">
                                        <a href="{{ route('customers.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-address-card"></i>
                                        <p>Customers</p>
                                        </a>
                                </li>                                
                                @endcan

                                @can('link-list')
                                <li class="nav-item">
                                        <a href="{{ route('links.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-street-view"></i>
                                        <p>Links</p>
                                        </a>
                                </li>                                
                                @endcan
                            </ul>
                        </li>                            
                        @endcan
                        @can('fault-list')
                        <li  class="nav-item">
                            <a href="{{ route('faults.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>Faults</p>
                            </a>
                        </li>                            
                        @endcan

                        @can('finance')
                        <li class="nav-item">
                            <a  class="nav-link">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>Finance</p>
                            </a>
                        </li>                            
                        @endcan


                        @can('permit-list')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-rocket"></i>
                                <p>
                                    Permits
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                              <li class="nav-item">
                                <a  class="nav-link" href="{{ route('permits.index') }}">
                                    <i class="nav-icon 	fas fa-check-circle"></i>
                                    <p>Approved Permits</p>
                                </a>
                              </li>
                              <li class="nav-item">
                                <a  class="nav-link" href="{{ route('request-permit.index') }}">
                                    <i class="nav-icon fas fa-plus-square"></i>
                                    <p>Requested Permits</p>
                                </a>
                              </li>
                            </ul>
                        </li>                            
                        @endcan
                        
                        @can('my-fault-list')
                        <li class="nav-item">
                            <a  class="nav-link" href="{{ route('my_faults.index') }}">
                                <i class="nav-icon 	fas fa-satellite-dish"></i>
                                <p>My Faults</p>
                            </a>
                        </li>                            
                        @endcan

                        @can('department-faults-list')
                        <li class="nav-item">
                            <a  class="nav-link"  href="{{ route('department_faults.index') }}">
                                <i class="nav-icon 	fas fa-satellite"></i>
                                <p>Department Faults</p>
                            </a>
                        </li>                            
                        @endcan

                        @can('chief-tech-clear-faults-list')
                        <li class="nav-item">
                            <a  class="nav-link"  href="{{ route('chief-tech-clear.index') }}">
                                <i class="nav-icon 	fas fa-exclamation-triangle"></i>
                                <p>Chief Tech Clear Faults</p>
                            </a>
                        </li>                            
                        @endcan

                        @can('noc-clear-faults-list')
                        <li class="nav-item">
                            <a  class="nav-link"  href="{{ route('noc-clear.index') }}">
                                <i class="nav-icon 	fas fa-exclamation-triangle"></i>
                                <p>Noc Clear Faults</p>
                            </a>
                        </li>                            
                        @endcan
                           
                        @can('materials')
                        <li class="nav-item">
                            <a  class="nav-link">
                                <i class="nav-icon 	fas fa-wrench"></i>
                                <p>Materials</p>
                            </a>
                        </li>                            
                        @endcan


                        @can('reports')
                        <li class="nav-item">
                            <a  class="nav-link">
                              <i class="nav-icon fas fa-file-export"></i>
                              <p>Reports</p>
                            </a>
                        </li>                            
                        @endcan
                        <li  class="nav-item">
                            <a href="{{ route('user.profile') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                          document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-power-off"></i>
                                <p>
                                    Logout
                                </p>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="min-height: 399px;">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">@yield('pageName')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

              <!-- /Main Content-->
              <div class="content">

                @include('partials.alerts')
                @yield('content')
              </div>
        </div>
        <!-- /.content-wrapper -->

    <!-- Main Footer -->
      <footer class="main-footer">
        <!-- Default to the left -->
        <div class="text-center">
        <strong>Copyright &copy; <?php echo date('Y') ?> <a>POWERTEL</a>.</strong> All rights reserved.
        </div>

      </footer>
    </div>
    <!-- ./wrapper -->

        <!-- Scripts -->

        <script src="{{ asset('js/app.js') }}"></script>

        <script>

        $(function () {
            $('table').DataTable({
                processing: true,
                serverSide: false
            });
        });

        </script>
        <script>
            window.addEventListener('load', function()
{
    var xhr = null;

    getXmlHttpRequestObject = function()
    {
        if(!xhr)
        {               
            // Create a new XMLHttpRequest object 
            xhr = new XMLHttpRequest();
        }
        return xhr;
    };

    updateLiveData = function()
    {
        var now = new Date();
        // Date string is appended as a query with live data 
        // for not to use the cached version 
        var url = 'livefeed.txt?' + now.getTime();
        xhr = getXmlHttpRequestObject();
        xhr.onreadystatechange = evenHandler;
        // asynchronous requests
        xhr.open("GET", url, true);
        // Send the request over the network
        xhr.send(null);
    };

    updateLiveData();

    function evenHandler()
    {
        // Check response is ready or not
        if(xhr.readyState == 4 && xhr.status == 200)
        {
            dataDiv = document.getElementById('liveData');
            // Set current data text
            dataDiv.innerHTML = xhr.responseText;
            // Update the live data every 1 sec
            setTimeout(updateLiveData(), 1000);
        }
    }
});
        </script>
                @yield('scripts')
    </body>

</html>
