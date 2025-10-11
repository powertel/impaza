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

    <style>
      /* Global compact typography to match SmartHR-like UI */
      html, body { font-size: 12px; color: #111827; }
      .content-wrapper { background: #f5f7fb; }

      /* Navbar */
      .main-header .navbar-nav .nav-link { font-size: 12px; padding: 6px 10px; }
      .main-header .navbar-nav .nav-link i { font-size: 0.9rem; }

      /* Sidebar modern + responsive font scaling */
      .main-header { position: sticky; top: 0; z-index: 1040; border-bottom: 1px solid #e6e9f0; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
      :root { --header-height: 56px; }
      .main-sidebar {
        background: #fff;
        border-right: 2px solid #e6e9f0;
        box-shadow: 2px 0 6px rgba(16,24,40,.06);
        position: fixed;
        top: var(--header-height);
        left: 0;
        height: calc(100vh - var(--header-height));
        overflow-y: auto !important;
        overflow-x: hidden !important;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        z-index: 1030;
      }
      .main-sidebar .sidebar {
        height: auto;
        padding-top: 0;
        overflow: initial;
      }
      .main-sidebar .sidebar {
        height: calc(100vh - 56px);
        padding-top: 56px; /* account for sticky header */
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
      }
      .nav-sidebar .nav-header {
        font-size: clamp(10px, 1.1vw, 11px);
        color:rgb(6, 6, 6);
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 10px 16px 6px;
        margin-top: 6px;
      }
      .nav-sidebar .nav-link:hover {
        background-color: #f8f9fb;
        color: #111827;
      }

      @media (min-width: 992px) {
        .sidebar-mini .main-sidebar { width: 240px; }
        .sidebar-mini .content-wrapper, .sidebar-mini .main-footer, .sidebar-mini .main-header { margin-left: 240px; }
      }

      .main-header { border-bottom: 1px solid #e6e9f0; box-shadow: 0 1px 2px rgba(16,24,40,.04); }

      @media (max-width: 991.98px) {
        .sidebar-mini .main-header { margin-left: 0; }
        /* On small screens let the sidebar use full viewport when opened */
        .main-sidebar { top: 0; height: 100vh; overflow-y: auto !important; }
        .main-sidebar .sidebar { height: auto; padding-top: 0; overflow: initial; }
      }
      .nav-sidebar .nav-link {
        font-size: clamp(11px, 1.3vw, 13px);
        color:rgb(8, 8, 8);
        border-radius: 8px;
        padding: 7px 11px;
        margin: 2px 8px;
        transition: background-color .2s ease, color .2s ease, padding .2s ease;
      }
      .nav-sidebar .nav-link i.nav-icon {
        font-size: 0.9rem;
        color:rgb(10, 10, 10);
        margin-right: 8px;
      }
      .nav-sidebar .nav-link:hover {
        background-color: #f8f9fb;
        color: #111827;
      }
      .nav-sidebar .nav-link.active {
        background-color: #eef4ff;
        color: #1f5cff;
      }
      .nav-sidebar .nav-link.active i.nav-icon { color: #1f5cff; }

      /* Brand area spacing for a cleaner look */
      .brand-link { padding: 16px 14px; }
      .brand-link .brand-image { opacity: 0.9; }

      /* Reduce general sidebar font a bit while respecting accessibility */
      .main-sidebar, .sidebar { font-size: clamp(11px, 1.3vw, 13px); }

      /* Compact divider style for headers */
      .nav-sidebar .nav-header:after {
        content: '';
        display: block;
        height: 0;
        border-top: 1px solid #f0f0f0;
        margin: 6px 0 8px;
      }

      .nav-sidebar .nav-link.active, .nav-sidebar .nav-link.active:focus {
        box-shadow: none;
      }

      @media (min-width: 1280px) {
        .sidebar-mini .main-sidebar { width: 240px; }
        .sidebar-mini .content-wrapper, .sidebar-mini .main-footer, .sidebar-mini .main-header { margin-left: 240px; }
      }

      /* Cards and content elements */
      .card { border: 1px solid #eee; border-radius: 10px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
      .card-body { padding: 14px; }
      .card-title { font-size: 14px; font-weight: 700; color: #111827; }

      /* Tables */
      .table { font-size: 12px; }
      .table thead th { font-size: 11px; color: #6b7280; font-weight: 700; }
      .table tbody td { font-size: 12px; }

      /* Breadcrumbs */
      .breadcrumb { font-size: 11px; }

      /* Buttons */
      .btn { font-size: 12px; }
      .btn-sm { font-size: 11px; padding: 6px 10px; }

      /* Global icon sizing (Font Awesome) */
      .fa, .fas, .far, .fab { font-size: 0.9rem; }

      /* DataTables tweaks */
      .dataTables_wrapper .dataTables_length, 
      .dataTables_wrapper .dataTables_filter,
      .dataTables_wrapper .dataTables_info,
      .dataTables_wrapper .dataTables_paginate { font-size: 12px; }
    </style>


    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous"> -->

    <!-- Popup -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<body class="sidebar-mini" style="height: auto;">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <div class="wrapper" id="app">
        <!-- Navbar -->
        @include('layouts.header')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.sidebar')
        <!-- /.sidebar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="min-height: 399px;background:#f7f9fc;">
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
        @section('scripts')
@endsection

        <script src="{{ asset('js/app.js') }}"></script>

        <style>
          /* Heading scale override for compact UI */
          .h1, h1 { font-size: 1.6rem; }
          .h2, h2 { font-size: 1.4rem; }
          .h3, h3 { font-size: 1.25rem; }
          .h4, h4 { font-size: 1.1rem; }
          .h5, h5 { font-size: 1rem; }
          .h6, h6 { font-size: 0.9rem; }
      
          /* Compact table paddings */
          .table thead th, .table tbody td { padding: 8px 10px; }
      
          /* Compact stat icons inside cards */
          .card .rounded-circle { width: 32px; height: 32px; }
      
          /* Slightly smaller global icon size */
          .fa, .fas, .far, .fab { font-size: 0.85rem; }
        </style>
        
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

<script>
$(document).ready(function() {
	let table = $('#faults-list').DataTable();
    table.destroy();
    $('#faults-list').DataTable({
        "order": [[5,"desc"]] // Disable default sorting
    });
});

</script>

                @yield('scripts')

                @include('partials.scripts')
    </body>
    @section('scripts')
@endsection

</html>

<script>
  (function(){
    function setHeaderHeightVar(){
      var header = document.querySelector('.main-header');
      var h = header ? header.offsetHeight : 56;
      document.documentElement.style.setProperty('--header-height', h + 'px');
    }
    window.addEventListener('resize', setHeaderHeightVar);
    document.addEventListener('DOMContentLoaded', setHeaderHeightVar);
    setHeaderHeightVar();
  })();
</script>
