<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/Favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/faults.css') }}" rel="stylesheet">

    <style>
        /* Global compact typography to match */
        html, body { font-size: 12px; color: #111827; }
        .content-wrapper { background: #f5f7fb; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        :root { color-scheme: light; }

        /* Tables */
        .table { font-size: 12px; }
        .table thead th { font-size: 11px; color: #6b7280; font-weight: 600; }
        .table tbody td { font-size: 12px; }

        /* Breadcrumbs */
        .breadcrumb { font-size: 11px; }

        /* Buttons */
        .btn { font-size: 11px; }
        .btn-sm { font-size: 11px; padding: 6px 10px; }
        /* Icon-only compact action buttons */
        .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }

        /* Flex gap utilities for BS4 */
        .gap-1 { gap: .25rem !important; }
        .gap-2 { gap: .5rem !important; }

        /* Global icon sizing (Font Awesome) */
        .fa, .fas, .far, .fab { font-size: 0.9rem; }

        /* Cards and content elements */
        .card { border: 1px solid #eef2f7; border-radius: 12px; box-shadow: 0 1px 2px rgba(16,24,40,.04); background:#fff; }
        .card-body { padding: 16px; }
        .card-title { font-size: 14px; font-weight: 700; color: #111827; }
        .card-header { border-bottom: 1px solid #d2e4ff; font-weight: 700; color:#111827; background-color: #eaf2ff; }
        .card-hover:hover { box-shadow: 0 8px 20px rgba(16,24,40,.08); transform: translateY(-2px); transition: all .2s ease; }

        /* Modern KPI stat card */
        .stat-card { display:flex; align-items:center; justify-content:space-between; }
        .stat-title { color:#6b7280; font-weight:600; letter-spacing:.02em; }
        :root { --kpi-value-size: 1.05rem; --kpi-height: 100px; --chart-height: 220px; }
        .stat-value { color:#0f172a; font-weight:700; font-size: var(--kpi-value-size); }
        .stat-sub { color:#9ca3af; font-size: .85rem; }
        .stat-right { display:flex; flex-direction:column; align-items:flex-end; gap:.35rem; }
        .stat-icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; background:linear-gradient(135deg,#4f46e5 0%, #06b6d4 100%); box-shadow: 0 4px 12px rgba(79,70,229,.25); }
        .stat-delta { font-size:.8rem; font-weight:600; padding:2px 8px; border-radius:999px; }
        .stat-delta.up { color:#166534; background:#dcfce7; }
        .stat-delta.down { color:#7f1d1d; background:#fee2e2; }
        .stat-delta.neutral { color:#374151; background:#f3f4f6; }

        /* New card styles for modern UI */
        .card.card-primary { background-color: #2563eb; color: #fff; }
        .card.card-primary .stat-title, .card.card-primary .stat-value, .card.card-primary .stat-sub { color: #fff; }
        .card.card-primary .stat-icon { background: rgba(255,255,255,0.2); box-shadow: none; }

        .card.card-success { background-color: #10b981; color: #fff; }
        .card.card-success .stat-title, .card.card-success .stat-value, .card.card-success .stat-sub { color: #fff; }
        .card.card-success .stat-icon { background: rgba(255,255,255,0.2); box-shadow: none; }

        .card.card-danger { background-color: #ef4444; color: #fff; }
        .card.card-danger .stat-title, .card.card-danger .stat-value, .card.card-danger .stat-sub { color: #fff; }
        .card.card-danger .stat-icon { background: rgba(255,255,255,0.2); box-shadow: none; }

        .card.card-info { background-color: #3b82f6; color: #fff; }
        .card.card-info .stat-title, .card.card-info .stat-value, .card.card-info .stat-sub { color: #fff; }
        .card.card-info .stat-icon { background: rgba(255,255,255,0.2); box-shadow: none; }

        /* Prominent card style for Total Shipments */
        .card.card-total-shipments { background-color: #1e3a8a; color: #fff; }
        .card.card-total-shipments .stat-title, .card.card-total-shipments .stat-value, .card.card-total-shipments .stat-sub { color: #fff; }
        .card.card-total-shipments .stat-icon { background: rgba(255,255,255,0.2); box-shadow: none; }
        .card.card-total-shipments .stat-value { font-size: 1.8rem; }

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

        /* Modal scrollability and whitespace control */
        #departmentCreateModal .modal-body { max-height: 70vh; overflow-y: auto; padding-bottom: 0.75rem; }
        #departmentCreateModal .repeater-items { max-height: 60vh; overflow-y: auto; }

        /* Uniform KPI and chart card sizing */
        .kpi-card .card-body { min-height: var(--kpi-height); }
        .card .card-header { font-size: 12px; }
        .card .card-body > canvas { height: var(--chart-height) !important; }
        /* Dashboard toolbar */
        .dashboard-toolbar { display:flex; align-items:center; justify-content:space-between; gap:.75rem; }
        .dashboard-toolbar .form-select, .dashboard-toolbar .btn { height: 32px; }
    </style>

    @include('layouts.partials.header_styles')
    @include('layouts.partials.sidebar_styles')
    @include('layouts.partials.formcss')

</head>

<body class="sidebar-mini">

    
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
            <!-- Main Footer moved inside content-wrapper to scroll with content -->
            @include('layouts.footer')
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- ./wrapper -->

        <!-- Scripts -->
    @section('scripts')
    @endsection

    <!-- All vendor scripts/styles are bundled locally via Mix -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/modals-bridge.js') }}"></script>
    <script src="{{ asset('js/faults-modals.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/reports.js') }}"></script>

    


    <script>
      (function(){
        function csrf() {
          var el = document.querySelector('meta[name="csrf-token"]');
          return el ? el.getAttribute('content') : '';
        }

        function esc(s) {
          return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
        }

        function setBadge(count) {
          var badge = document.getElementById('impazaNotifBadge');
          if (!badge) return;
          var n = parseInt(count || 0, 10) || 0;
          if (n > 0) {
            badge.textContent = String(n);
            badge.style.display = 'inline-block';
          } else {
            badge.textContent = '';
            badge.style.display = 'none';
          }
        }

        function renderList(items) {
          var root = document.getElementById('impazaNotifList');
          if (!root) return;
          if (!items || !items.length) {
            root.innerHTML = '<div class="px-3 py-2 text-muted small">No notifications</div>';
            return;
          }
          var html = items.map(function(n){
            var data = n.data || {};
            var title = esc(data.title || 'Notification');
            var body = esc(data.body || data.message || '');
            var unread = !n.read_at;
            var rowStyle = 'padding:10px 12px; border-bottom:1px solid #f1f5f9; cursor:pointer;' + (unread ? ' background:#eef6ff;' : '');
            var titleStyle = 'font-weight:700; font-size:12px; color:#0f172a; margin-bottom:2px;';
            var bodyStyle = 'font-size:11px; color:#475569; white-space:normal;';
            return '<div class="impaza-notif-item" data-id="'+n.id+'" style="'+rowStyle+'">' +
              '<div style="'+titleStyle+'">'+title+'</div>' +
              '<div style="'+bodyStyle+'">'+body+'</div>' +
            '</div>';
          }).join('');
          root.innerHTML = html;
        }

        async function fetchCount() {
          try {
            var res = await fetch('{{ route('notifications.unread-count') }}', { headers: { 'Accept':'application/json' } });
            if (!res.ok) return;
            var json = await res.json();
            setBadge(json.unread);
          } catch (e) {}
        }

        async function fetchList() {
          var root = document.getElementById('impazaNotifList');
          if (root) root.innerHTML = '<div class="px-3 py-2 text-muted small">Loading...</div>';
          try {
            var res = await fetch('{{ route('notifications.index') }}?limit=15', { headers: { 'Accept':'application/json' } });
            if (!res.ok) return;
            var json = await res.json();
            renderList((json && json.notifications) ? json.notifications : []);
          } catch (e) {}
        }

        async function markRead(id) {
          try {
            var res = await fetch('{{ url('/notifications') }}/' + encodeURIComponent(id) + '/read', {
              method: 'POST',
              headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': csrf() }
            });
            if (!res.ok) return;
            await fetchCount();
            await fetchList();
          } catch (e) {}
        }

        async function markAllRead() {
          var list = document.getElementById('impazaNotifList');
          if (!list) return;
          var ids = Array.prototype.slice.call(list.querySelectorAll('.impaza-notif-item')).map(function(el){ return el.getAttribute('data-id'); }).filter(Boolean);
          for (var i = 0; i < ids.length; i++) {
            await markRead(ids[i]);
          }
        }

        document.addEventListener('click', function(ev){
          var item = ev.target && ev.target.closest ? ev.target.closest('.impaza-notif-item') : null;
          if (item && item.getAttribute) {
            var id = item.getAttribute('data-id');
            if (id) markRead(id);
          }
        });

        document.addEventListener('show.bs.dropdown', function(ev){
          if (ev && ev.target && ev.target.querySelector && ev.target.querySelector('#impazaNotifList')) {
            fetchList();
          }
        });

        var btn = document.getElementById('impazaNotifMarkAll');
        if (btn) btn.addEventListener('click', function(e){ e.preventDefault(); markAllRead(); });
        var toggle = document.getElementById('impazaNotifToggle');
        if (toggle) toggle.addEventListener('click', function(){ fetchList(); });

        fetchCount();
        fetchList();
        setInterval(fetchCount, 30000);
      })();
    </script>

        @yield('scripts')

        @include('partials.scripts')
        @include('layouts.partials.pagination')
        @include('partials.select2')

        @section('scripts')
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

        

        <script>
            (function(){
                // Guard against missing jQuery or Select2 to avoid breaking other scripts
                if (window.$ && $.fn && typeof $.fn.select2 === 'function') {
                    $(document).ready(function () {
                        // Initialize Select2 globally on page load
                        $('.select2').select2();

                        // Reinitialize Select2 when any modal is shown
                        $('.modal').on('shown.bs.modal', function () {
                            $(this).find('.select2').select2({
                                dropdownParent: $(this),
                                width: '100%'
                            });
                        });
                    });
                }
            })();
        </script>
        @endsection

        <script>
          (function () {
            var enabled = {{ auth()->check() && (int)(auth()->user()->dashboard_auto_refresh_enabled ?? 0) === 1 ? 'true' : 'false' }};
            var seconds = {{ auth()->check() ? (int)(auth()->user()->dashboard_refresh_seconds ?? 60) : 0 }};
            if (!enabled) return;
            if (!seconds || seconds < 10) seconds = 60;
            if (seconds > 300) seconds = 300;
            var url = '{{ route('keepalive') }}';
            window.setInterval(function () {
              try { window.dispatchEvent(new Event('impaza:activity')); } catch (e) {}
              try {
                fetch(url + '?_ts=' + String(Date.now()), {
                  method: 'GET',
                  credentials: 'same-origin',
                  cache: 'no-store',
                  headers: { 'Accept': 'application/json' }
                }).catch(function(){});
              } catch (e) {}
            }, seconds * 1000);
          })();
        </script>

        @include('partials.idle_logout')
</body>

</html>



