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

      /* Cards and content elements */
      .card { border: 1px solid #eee; border-radius: 10px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
      .card-body { padding: 14px; }
      .card-title { font-size: 14px; font-weight: 700; color: #111827; }
    </style>

    @include('layouts.header_styles')
    @include('layouts.sidebar_styles')


    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous"> -->

    <!-- Popup -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<body class="sidebar-mini">
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
      @include('layouts.footer')
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
