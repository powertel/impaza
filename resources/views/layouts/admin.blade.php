<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script>
        (function () {
            try {
                var theme = window.localStorage.getItem('impaza-theme') || 'light';
                document.documentElement.setAttribute('data-theme', theme === 'dark' ? 'dark' : 'light');
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/Favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/faults.css') }}" rel="stylesheet">
    <link href="{{ asset('css/impaza-components.css') }}?v={{ @filemtime(public_path('css/impaza-components.css')) }}" rel="stylesheet">
    @yield('styles')

    <style>
        html, body { font-size: 12px; color: var(--impaza-text); background: var(--impaza-bg); }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        :root {
            color-scheme: light;
            --impaza-primary: #6366F1;
            --impaza-success: #10B981;
            --impaza-warning: #F59E0B;
            --impaza-danger: #EF4444;
            --impaza-info: #06B6D4;
            --impaza-bg: #F8FAFC;
            --impaza-card: #FFFFFF;
            --impaza-border: #E2E8F0;
            --impaza-text: #0F172A;
            --impaza-muted: #64748B;
            --impaza-radius: 16px;
            --impaza-shadow: 0 1px 2px rgba(15,23,42,.06), 0 12px 30px rgba(15,23,42,.08);
            --impaza-shadow-sm: 0 1px 2px rgba(15,23,42,.06);
        }
        html[data-theme="dark"] {
            color-scheme: dark;
            --impaza-bg: #020617;
            --impaza-card: #0F172A;
            --impaza-border: #1E293B;
            --impaza-text: #E2E8F0;
            --impaza-muted: #94A3B8;
            --impaza-shadow: 0 1px 2px rgba(2,6,23,.5), 0 14px 36px rgba(2,6,23,.6);
            --impaza-shadow-sm: 0 1px 2px rgba(2,6,23,.55);
        }

        /* Tables */
        .table { font-size: 12px; color: var(--impaza-text); }
        .table thead th { font-size: 11px; color: var(--impaza-muted); font-weight: 600; }
        .table tbody td { font-size: 12px; }

        /* Breadcrumbs */
        .breadcrumb { font-size: 11px; }

        /* Buttons */
        .btn { font-size: 11px; }
        .btn-sm { font-size: 11px; padding: 6px 10px; }
        /* Icon-only compact action buttons */
        .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
        .btn-primary { background: var(--impaza-primary); border-color: var(--impaza-primary); }
        .btn-primary:hover { background: #4F46E5; border-color: #4F46E5; }
        .btn-outline-primary { color: var(--impaza-primary); border-color: rgba(99,102,241,.35); }
        .btn-outline-primary:hover { background: rgba(99,102,241,.08); color: var(--impaza-primary); border-color: rgba(99,102,241,.45); }

        /* Flex gap utilities for BS4 */
        .gap-1 { gap: .25rem !important; }
        .gap-2 { gap: .5rem !important; }
        .gap-3 { gap: 1rem !important; }

        /* Global icon sizing (Font Awesome) */
        .fa, .fas, .far, .fab { font-size: 0.9rem; }

        /* Cards and content elements */
        .card { border: 1px solid var(--impaza-border); border-radius: var(--impaza-radius); box-shadow: var(--impaza-shadow-sm); background: var(--impaza-card); }
        .card-body { padding: 16px; }
        .card-title { font-size: 14px; font-weight: 700; color: var(--impaza-text); }
        .card-header { border-bottom: 1px solid var(--impaza-border); font-weight: 700; color: var(--impaza-text); background: transparent; }
        .card-hover:hover { box-shadow: 0 8px 20px rgba(16,24,40,.08); transform: translateY(-2px); transition: all .2s ease; }

        /* Modern KPI stat card */
        .stat-card { display:flex; align-items:center; justify-content:space-between; }
        .stat-title { color: var(--impaza-muted); font-weight:600; letter-spacing:.02em; }
        :root { --kpi-value-size: 1.05rem; --kpi-height: 100px; --chart-height: 220px; }
        .stat-value { color: var(--impaza-text); font-weight:700; font-size: var(--kpi-value-size); }
        .stat-sub { color: var(--impaza-muted); font-size: .85rem; }
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

        .form-control, .custom-select, .form-select {
          border-radius: 12px;
          border-color: var(--impaza-border);
          background: var(--impaza-card);
          color: var(--impaza-text);
          min-height: 38px;
          padding: 9px 12px;
          transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }
        textarea.form-control { min-height: 96px; resize: vertical; }
        .form-control::placeholder { color: rgba(100,116,139,.85); }
        html[data-theme="dark"] .form-control::placeholder { color: rgba(148,163,184,.75); }
        .form-control:focus, .custom-select:focus, .form-select:focus {
          border-color: rgba(99,102,241,.55);
          box-shadow: 0 0 0 .2rem rgba(99,102,241,.18);
          background: var(--impaza-card);
        }
        .form-control:disabled, .form-control[readonly],
        .custom-select:disabled, .form-select:disabled {
          background: rgba(148,163,184,.10);
          color: rgba(100,116,139,.9);
          cursor: not-allowed;
        }
        html[data-theme="dark"] .form-control:disabled, html[data-theme="dark"] .form-control[readonly],
        html[data-theme="dark"] .custom-select:disabled, html[data-theme="dark"] .form-select:disabled {
          background: rgba(2,6,23,.25);
          color: rgba(148,163,184,.85);
        }

        .is-invalid.form-control, .is-invalid.form-select, .is-invalid.custom-select {
          border-color: rgba(239,68,68,.60) !important;
          box-shadow: 0 0 0 .2rem rgba(239,68,68,.14);
        }
        .invalid-feedback { font-size: 11px; font-weight: 600; }
        .valid-feedback { font-size: 11px; font-weight: 600; }

        .form-check-input {
          width: 16px;
          height: 16px;
          border-radius: 6px;
          border: 1px solid var(--impaza-border);
          background: var(--impaza-card);
          accent-color: var(--impaza-primary);
        }
        .form-check-input:focus { box-shadow: 0 0 0 .2rem rgba(99,102,241,.18); border-color: rgba(99,102,241,.55); }
        .form-check-label { color: var(--impaza-text); }

        .input-group > .form-control,
        .input-group > .custom-select,
        .input-group > .form-select { border-top-left-radius: 0; border-bottom-left-radius: 0; }
        .input-group .input-group-text { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .input-group .input-group-prepend .input-group-text { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .input-group .input-group-append .btn { border-top-left-radius: 0; border-bottom-left-radius: 0; }

        .content-header { padding: 14px 0; }
        .content-header h1 { font-size: 16px; font-weight: 800; color: var(--impaza-text); letter-spacing: -0.01em; }
        .content-wrapper { background: var(--impaza-bg); }
        .content-header .breadcrumb { margin-bottom: 0; }
        .content-header .breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: rgba(100,116,139,.7); }
        .content-header .breadcrumb a { color: var(--impaza-muted); text-decoration: none; }
        .content-header .breadcrumb a:hover { color: var(--impaza-text); }
        .content-header .breadcrumb .active { color: var(--impaza-text); font-weight: 600; }

        .table-responsive {
          background: var(--impaza-card);
          border: 1px solid var(--impaza-border);
          border-radius: 18px;
          box-shadow: var(--impaza-shadow-sm);
        }
        .card .table-responsive { box-shadow: none; }
        .table-responsive { overflow: auto; }
        .table-responsive thead th {
          position: sticky;
          top: 0;
          z-index: 2;
          background: color-mix(in srgb, var(--impaza-primary) 3%, var(--impaza-card));
        }
        html[data-theme="dark"] .table-responsive thead th { background: color-mix(in srgb, var(--impaza-primary) 10%, #0B1220); }

        .table { margin-bottom: 0; }
        .table thead th {
          text-transform: uppercase;
          letter-spacing: .06em;
          font-size: 10px;
          font-weight: 700;
          padding: 14px 12px;
          border-top: 0;
          border-bottom: 1px solid var(--impaza-border);
        }
        .table tbody td {
          padding: 13px 12px;
          border-top: 1px solid rgba(226,232,240,.85);
          color: var(--impaza-text);
          vertical-align: middle;
        }
        html[data-theme="dark"] .table tbody td { border-top-color: rgba(30,41,59,.85); }
        .table.table-hover tbody tr:hover { background: rgba(99,102,241,.05); }
        html[data-theme="dark"] .table.table-hover tbody tr:hover { background: rgba(99,102,241,.12); }

        .table .text-muted { color: var(--impaza-muted) !important; }
        .table a { color: var(--impaza-primary); text-decoration: none; }
        .table a:hover { text-decoration: underline; }

        .badge { font-weight: 700; }
        .badge.rounded-pill { border: 1px solid rgba(15,23,42,.10); }
        html[data-theme="dark"] .badge.rounded-pill { border-color: rgba(148,163,184,.14); }

        .pagination { gap: 6px; }
        .pagination .page-link {
          border-radius: 10px;
          border: 1px solid var(--impaza-border);
          color: var(--impaza-muted);
          background: var(--impaza-card);
          min-width: 32px;
          height: 32px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          padding: 0 10px;
        }
        .pagination .page-link:hover { color: var(--impaza-text); border-color: rgba(99,102,241,.35); background: rgba(99,102,241,.06); }
        .pagination .page-item.active .page-link {
          background: var(--impaza-primary);
          border-color: var(--impaza-primary);
          color: #fff;
          box-shadow: 0 10px 18px rgba(99,102,241,.22);
        }
        .pagination .page-item.disabled .page-link { opacity: .55; background: var(--impaza-card); }

        .input-group-text { border-radius: 12px; border-color: var(--impaza-border); color: var(--impaza-muted); background: var(--impaza-bg); }
        html[data-theme="dark"] .input-group-text { background: rgba(2,6,23,.25); }

        .dropdown-menu { border-radius: 14px; border: 1px solid var(--impaza-border); box-shadow: var(--impaza-shadow); }
        .dropdown-item { font-size: 12px; }
        .dropdown-item:active { background: rgba(99,102,241,.18); }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
          border-radius: 12px;
          border-color: var(--impaza-border);
          min-height: 34px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered { color: var(--impaza-text); line-height: 32px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px; }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
          background: rgba(99,102,241,.16);
          color: var(--impaza-text);
        }
        .select2-container--default .select2-results__option--selected { background: rgba(99,102,241,.08); }
        html[data-theme="dark"] .select2-container--default .select2-selection--single,
        html[data-theme="dark"] .select2-container--default .select2-selection--multiple {
          background: rgba(2,6,23,.15);
        }
        html[data-theme="dark"] .select2-container--default .select2-results__option { color: #E2E8F0; }
        html[data-theme="dark"] .select2-dropdown { background: #0B1220; border-color: var(--impaza-border); }

        html[data-theme="dark"] .dropdown-menu { background: #0B1220; border-color: var(--impaza-border); color: var(--impaza-text); }
        html[data-theme="dark"] .dropdown-item { color: var(--impaza-text); }
        html[data-theme="dark"] .dropdown-item:hover { background: rgba(148,163,184,.12); color: var(--impaza-text); }

        .filter-toolbar {
          padding: 10px;
          border-radius: 14px;
          border: 1px solid var(--impaza-border);
          background: rgba(248,250,252,.9);
        }
        html[data-theme="dark"] .filter-toolbar { background: rgba(2,6,23,.25); }
        .filter-toolbar .input-group { margin-bottom: 0; }

        .card-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 10px;
          padding: 14px 16px;
        }
        .card-header .card-title { margin: 0; font-size: 13px; font-weight: 800; letter-spacing: -0.01em; }
        .card-header .card-tools { display: inline-flex; align-items: center; gap: 8px; }

        .table td .btn { border-radius: 10px; }
        .table td .btn.btn-sm { padding: 6px 10px; }

        .modal-content { border-radius: 18px; border: 1px solid var(--impaza-border); box-shadow: var(--impaza-shadow); background: var(--impaza-card); }
        .modal-header { border-bottom: 1px solid var(--impaza-border); padding: 14px 16px; }
        .modal-title { font-weight: 800; font-size: 14px; letter-spacing: -0.01em; color: var(--impaza-text); }
        .modal-body { padding: 16px; }
        .modal-footer { border-top: 1px solid var(--impaza-border); padding: 12px 16px; gap: 8px; }
        html[data-theme="dark"] .btn-light { background: rgba(148,163,184,.10); border-color: rgba(148,163,184,.16); color: var(--impaza-text); }
        html[data-theme="dark"] .btn-light:hover { background: rgba(148,163,184,.14); border-color: rgba(148,163,184,.22); }

        .form-label { font-size: 11px; font-weight: 700; color: var(--impaza-muted); margin-bottom: 6px; }
        html[data-theme="dark"] .form-label { color: rgba(148,163,184,.95); }
        .form-text { color: var(--impaza-muted); }

        .impaza-field { position: relative; }
        .impaza-field .form-control { padding-top: 16px; padding-bottom: 8px; }
        .impaza-field textarea.form-control { padding-top: 18px; }
        .impaza-field .impaza-float-label {
          position: absolute;
          left: 12px;
          top: 11px;
          margin: 0;
          padding: 0 6px;
          border-radius: 8px;
          background: var(--impaza-card);
          color: var(--impaza-muted);
          font-size: 11px;
          font-weight: 700;
          letter-spacing: .02em;
          transform-origin: left top;
          transform: translateY(0) scale(1);
          transition: transform .14s ease, color .14s ease, top .14s ease;
          pointer-events: none;
        }
        html[data-theme="dark"] .impaza-field .impaza-float-label { background: var(--impaza-card); color: rgba(148,163,184,.95); }
        .impaza-field .form-control:focus ~ .impaza-float-label,
        .impaza-field .form-control:not(:placeholder-shown) ~ .impaza-float-label {
          top: 6px;
          transform: translateY(-4px) scale(.92);
          color: rgba(99,102,241,.95);
        }
        .impaza-field .form-control.is-invalid ~ .impaza-float-label { color: rgba(239,68,68,.95); }

        .impaza-form-section {
          border: 1px solid var(--impaza-border);
          border-radius: 16px;
          background: var(--impaza-card);
          box-shadow: var(--impaza-shadow-sm);
          overflow: hidden;
          margin-bottom: 12px;
        }
        .impaza-form-section-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 12px;
          padding: 14px 16px;
          border-bottom: 1px solid var(--impaza-border);
          background: rgba(248,250,252,.85);
        }
        html[data-theme="dark"] .impaza-form-section-header { background: rgba(2,6,23,.22); }
        .impaza-form-section-title { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .impaza-form-section-icon {
          width: 36px;
          height: 36px;
          border-radius: 12px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          color: var(--impaza-primary);
          background: rgba(99,102,241,.12);
          border: 1px solid rgba(99,102,241,.18);
          flex: 0 0 auto;
        }
        .impaza-form-section-text { min-width: 0; }
        .impaza-form-section-text .title { font-weight: 800; color: var(--impaza-text); font-size: 13px; letter-spacing: -0.01em; line-height: 1.2; }
        .impaza-form-section-text .desc { color: var(--impaza-muted); font-size: 11px; line-height: 1.2; margin-top: 2px; }
        .impaza-form-section-body { padding: 14px 16px; }

        .impaza-stepper {
          display: grid;
          grid-template-columns: repeat(4, minmax(0, 1fr));
          gap: 8px;
          padding: 12px;
          border: 1px solid var(--impaza-border);
          border-radius: 16px;
          background: rgba(248,250,252,.9);
          margin-bottom: 12px;
        }
        html[data-theme="dark"] .impaza-stepper { background: rgba(2,6,23,.22); }
        .impaza-step {
          display: flex;
          align-items: center;
          gap: 10px;
          padding: 10px 10px;
          border-radius: 14px;
          border: 1px solid transparent;
          background: transparent;
          color: var(--impaza-muted);
          text-align: left;
          width: 100%;
        }
        .impaza-step .dot {
          width: 26px;
          height: 26px;
          border-radius: 999px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          border: 1px solid var(--impaza-border);
          background: var(--impaza-card);
          font-weight: 800;
          font-size: 11px;
          color: var(--impaza-muted);
          flex: 0 0 auto;
        }
        .impaza-step .meta { min-width: 0; }
        .impaza-step .meta .k { font-size: 10px; letter-spacing: .06em; text-transform: uppercase; opacity: .8; }
        .impaza-step .meta .t { font-size: 12px; font-weight: 700; color: var(--impaza-text); line-height: 1.1; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .impaza-step.is-active { border-color: rgba(99,102,241,.30); background: rgba(99,102,241,.06); color: var(--impaza-text); }
        .impaza-step.is-active .dot { border-color: rgba(99,102,241,.35); color: var(--impaza-primary); }
        .impaza-step.is-complete { color: var(--impaza-text); }
        .impaza-step.is-complete .dot { background: rgba(16,185,129,.14); border-color: rgba(16,185,129,.22); color: var(--impaza-success); }

        .impaza-dropzone {
          border: 1.5px dashed rgba(148,163,184,.45);
          border-radius: 16px;
          padding: 14px;
          background: rgba(248,250,252,.75);
          position: relative;
          transition: border-color .15s ease, background .15s ease, transform .15s ease;
        }
        html[data-theme="dark"] .impaza-dropzone { background: rgba(2,6,23,.18); border-color: rgba(148,163,184,.22); }
        .impaza-dropzone.is-dragover { border-color: rgba(99,102,241,.55); background: rgba(99,102,241,.08); transform: translateY(-1px); }
        .impaza-dropzone input[type="file"] {
          position: absolute;
          inset: 0;
          opacity: 0;
          cursor: pointer;
          width: 100%;
          height: 100%;
        }
        .impaza-dropzone .dz-inner { display: flex; align-items: center; gap: 12px; }
        .impaza-dropzone .dz-icon {
          width: 40px;
          height: 40px;
          border-radius: 14px;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          background: rgba(99,102,241,.12);
          border: 1px solid rgba(99,102,241,.18);
          color: var(--impaza-primary);
          flex: 0 0 auto;
        }
        .impaza-dropzone .dz-text { min-width: 0; }
        .impaza-dropzone .dz-text .a { font-weight: 800; color: var(--impaza-text); }
        .impaza-dropzone .dz-text .b { font-size: 11px; color: var(--impaza-muted); margin-top: 2px; }
    </style>

    @include('layouts.partials.header_styles')
    @include('layouts.partials.sidebar_styles')
    @include('layouts.partials.formcss')

    <!-- Standalone layout shell (post-AdminLTE) — owns layout once adminlte.css is gone -->
    <link href="{{ asset('css/impaza-shell.css') }}?v={{ @filemtime(public_path('css/impaza-shell.css')) }}" rel="stylesheet">
</head>

<body class="sidebar-mini impaza-app {{ request()->routeIs('faults.*') ? 'page-faults' : '' }}">
    <script>
      // Persist + restore the collapsed sidebar state across pages.
      // Apply saved state before AdminLTE initializes to avoid a flash.
      (function () {
        function isDesktop() {
          return window.matchMedia('(min-width: 992px)').matches;
        }

        function getToggle() {
          return document.getElementById('impazaSidebarToggle');
        }

        function syncSidebarToggleState() {
          var toggle = getToggle();
          if (!toggle) return;
          var expanded = isDesktop()
            ? !document.body.classList.contains('sidebar-collapse')
            : document.body.classList.contains('sidebar-open');
          toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        }

        function persistDesktopSidebarState() {
          try {
            localStorage.setItem(
              'impaza-sidebar',
              document.body.classList.contains('sidebar-collapse') ? 'collapsed' : 'open'
            );
          } catch (e) {}
        }

        try {
          if (isDesktop() &&
              localStorage.getItem('impaza-sidebar') === 'collapsed') {
            document.body.classList.add('sidebar-collapse');
          }
        } catch (e) {}

        document.addEventListener('DOMContentLoaded', function () {
          syncSidebarToggleState();

          window.addEventListener('resize', function () {
            if (isDesktop()) {
              document.body.classList.remove('sidebar-open');
              try {
                if (localStorage.getItem('impaza-sidebar') === 'collapsed') {
                  document.body.classList.add('sidebar-collapse');
                } else {
                  document.body.classList.remove('sidebar-collapse');
                }
              } catch (e) {}
            }
            syncSidebarToggleState();
          });
        });

        // Sidebar toggle (replaces AdminLTE pushmenu): desktop collapses the
        // mini rail; mobile slides the off-canvas sidebar + overlay.
        document.addEventListener('click', function (e) {
          var toggle = e.target.closest('[data-widget="pushmenu"]');
          if (toggle) {
            e.preventDefault();
            if (isDesktop()) {
              document.body.classList.toggle('sidebar-collapse');
              persistDesktopSidebarState();
            } else {
              document.body.classList.toggle('sidebar-open');
            }
            syncSidebarToggleState();
            return;
          }
          if (e.target.classList && e.target.classList.contains('impaza-sidebar-overlay')) {
            document.body.classList.remove('sidebar-open');
            syncSidebarToggleState();
          }
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            document.body.classList.remove('sidebar-open');
            syncSidebarToggleState();
          }
        });

        // Close mobile sidebar when a nav link is tapped
        document.addEventListener('DOMContentLoaded', function () {
          document.querySelectorAll('.main-sidebar .nav-sidebar .nav-link').forEach(function (lnk) {
            lnk.addEventListener('click', function () {
              if (!isDesktop()) {
                document.body.classList.remove('sidebar-open');
                syncSidebarToggleState();
              }
            });
          });
        });
      })();
    </script>

    <div class="wrapper" id="app">
        <!-- Navbar -->
        @include('layouts.header')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.sidebar')
        <!-- /.sidebar -->

        <!-- Mobile sidebar overlay (tap to close) -->
        <div class="impaza-sidebar-overlay" aria-hidden="true"></div>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="min-height: 399px;">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('pageName')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6 d-flex justify-content-end">
                            <ol class="breadcrumb mb-0">
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

    <script>
      (function () {
        var root = document.documentElement;
        var storageKey = 'impaza-theme';

        function currentTheme() {
          try { return window.localStorage.getItem(storageKey) || 'light'; } catch (e) { return 'light'; }
        }

        function setTheme(next) {
          var theme = (next === 'dark') ? 'dark' : 'light';
          root.setAttribute('data-theme', theme);
          try { window.localStorage.setItem(storageKey, theme); } catch (e) {}

          var btn = document.getElementById('impazaThemeToggle');
          if (btn) {
            var icon = btn.querySelector('i');
            if (icon) {
              icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
            btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
          }
        }

        function toggleTheme() {
          setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
        }

        document.addEventListener('DOMContentLoaded', function () {
          setTheme(currentTheme());
          var btn = document.getElementById('impazaThemeToggle');
          if (btn) btn.addEventListener('click', function (e) { e.preventDefault(); toggleTheme(); });
        });
      })();
    </script>

    <script>
      (function () {
        function openSearch() {
          var modalEl = document.getElementById('impazaGlobalSearchModal');
          if (!modalEl || !window.bootstrap || !window.bootstrap.Modal) return;
          var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
          modal.show();
          window.setTimeout(function () {
            var input = document.getElementById('impazaGlobalSearchInput');
            if (input) input.focus();
          }, 80);
        }

        document.addEventListener('keydown', function (e) {
          var isMac = /Mac|iPhone|iPad|iPod/i.test(navigator.platform);
          var mod = isMac ? e.metaKey : e.ctrlKey;
          if (mod && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            openSearch();
          }
        });

        document.addEventListener('DOMContentLoaded', function () {
          var btn = document.getElementById('impazaGlobalSearchOpen');
          if (btn) btn.addEventListener('click', function (e) { e.preventDefault(); openSearch(); });
        });
      })();
    </script>

    <script>
      (function () {
        function parseRgb(input) {
          if (!input) return null;
          var s = String(input).trim();
          var m = s.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i);
          if (m) {
            var hex = m[1];
            if (hex.length === 3) {
              var r3 = parseInt(hex[0] + hex[0], 16);
              var g3 = parseInt(hex[1] + hex[1], 16);
              var b3 = parseInt(hex[2] + hex[2], 16);
              return { r: r3, g: g3, b: b3 };
            }
            return { r: parseInt(hex.slice(0, 2), 16), g: parseInt(hex.slice(2, 4), 16), b: parseInt(hex.slice(4, 6), 16) };
          }
          var rgb = s.match(/rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)/i);
          if (rgb) return { r: parseFloat(rgb[1]), g: parseFloat(rgb[2]), b: parseFloat(rgb[3]) };
          return null;
        }

        function relativeLuminance(rgb) {
          function lin(c) {
            var v = c / 255;
            return v <= 0.03928 ? (v / 12.92) : Math.pow((v + 0.055) / 1.055, 2.4);
          }
          return 0.2126 * lin(rgb.r) + 0.7152 * lin(rgb.g) + 0.0722 * lin(rgb.b);
        }

        function normalizeStatusBadges(scope) {
          var root = scope || document;
          var badges = root.querySelectorAll('.table .badge.rounded-pill[style*="background-color"]');
          Array.prototype.forEach.call(badges, function (b) {
            if (b.dataset.impazaTone === '1') return;
            var cs = window.getComputedStyle(b);
            var bg = cs.backgroundColor;
            var fg = cs.color;
            var rgb = parseRgb(bg);
            if (!rgb) return;
            var lum = relativeLuminance(rgb);
            var wantsLightText = lum < 0.55;
            var isFgDefault = !fg || fg === 'rgb(0, 0, 0)' || fg === 'rgba(0, 0, 0, 1)';
            if (isFgDefault) {
              b.style.color = wantsLightText ? '#FFFFFF' : '#0F172A';
            }
            b.style.borderColor = wantsLightText ? 'rgba(255,255,255,.18)' : 'rgba(15,23,42,.10)';
            b.style.boxShadow = wantsLightText ? '0 10px 18px rgba(2,6,23,.12)' : '0 10px 18px rgba(15,23,42,.06)';
            b.dataset.impazaTone = '1';
          });
        }

        document.addEventListener('DOMContentLoaded', function () {
          normalizeStatusBadges(document);
        });

        document.addEventListener('shown.bs.modal', function (e) {
          try { normalizeStatusBadges(e.target); } catch (_) {}
        });
      })();
    </script>

    <script>
      (function () {
        function qs(root, sel){ return (root || document).querySelector(sel); }
        function qsa(root, sel){ return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

        function setStepperState(key, step) {
          var modal = qs(document, '#createFaultModal');
          if (!modal) return;
          modal.setAttribute('data-impaza-step', String(step));

          var panes = qsa(modal, '[data-impaza-step-pane]');
          panes.forEach(function(p){
            var s = parseInt(p.getAttribute('data-impaza-step-pane') || '0', 10);
            var show = s === step;
            p.style.display = show ? '' : 'none';
            if (show) { p.removeAttribute('hidden'); } else { p.setAttribute('hidden', 'hidden'); }
          });

          var steps = qsa(document, '[data-impaza-stepper="'+key+'"] [data-impaza-step]');
          steps.forEach(function(b){
            var s = parseInt(b.getAttribute('data-impaza-step') || '0', 10);
            b.classList.toggle('is-active', s === step);
            b.classList.toggle('is-complete', s < step);
          });

          var prevBtn = qs(document, '[data-impaza-stepper-prev="'+key+'"]');
          var nextBtn = qs(document, '[data-impaza-stepper-next="'+key+'"]');
          var submitBtn = qs(document, '[data-impaza-stepper-submit="'+key+'"]');

          if (prevBtn) prevBtn.style.display = step > 1 ? '' : 'none';
          if (nextBtn) nextBtn.style.display = step < 4 ? '' : 'none';
          if (submitBtn) submitBtn.style.display = step === 4 ? '' : 'none';

          if (step === 4) {
            try {
              var customerSel = qs(modal, '#customer');
              var linkSel = qs(modal, '#link');
              var contact = qs(modal, 'input[name="contactName"]');
              var phone = qs(modal, 'input[name="phoneNumber"]');
              var rfoSel = qs(modal, 'select[name="suspectedRfo_id"]');
              var remark = qs(modal, 'textarea[name="remark"]');
              var cText = customerSel && customerSel.selectedOptions && customerSel.selectedOptions[0] ? customerSel.selectedOptions[0].textContent : '—';
              var lText = linkSel && linkSel.selectedOptions && linkSel.selectedOptions[0] ? linkSel.selectedOptions[0].textContent : '—';
              var rText = rfoSel && rfoSel.selectedOptions && rfoSel.selectedOptions[0] ? rfoSel.selectedOptions[0].textContent : '—';
              var cVal = (contact && contact.value) ? contact.value : '—';
              var pVal = (phone && phone.value) ? phone.value : '';
              var rm = (remark && remark.value) ? remark.value : '—';
              var set = function(id, val){ var el = qs(modal, id); if (el) el.textContent = val; };
              set('#impazaReviewCustomer', cText);
              set('#impazaReviewLink', lText);
              set('#impazaReviewContact', (cVal === '—' ? '—' : (cVal + (pVal ? (' • ' + pVal) : ''))));
              set('#impazaReviewRfo', rText);
              set('#impazaReviewRemark', rm);
            } catch (e) {}
          }

          var body = qs(modal, '.modal-body');
          if (body) { body.scrollTop = 0; }
        }

        function initFaultCreateStepper() {
          var modal = qs(document, '#createFaultModal');
          if (!modal) return;
          var key = 'UF';
          setStepperState(key, 1);
        }

        document.addEventListener('click', function (e) {
          var t = e.target;
          var btnPrev = t && t.closest ? t.closest('[data-impaza-stepper-prev]') : null;
          var btnNext = t && t.closest ? t.closest('[data-impaza-stepper-next]') : null;
          var btnStep = t && t.closest ? t.closest('[data-impaza-stepper] [data-impaza-step]') : null;

          var modal = qs(document, '#createFaultModal');
          if (!modal) return;
          var key = 'UF';
          var cur = parseInt(modal.getAttribute('data-impaza-step') || '1', 10) || 1;

          if (btnPrev && btnPrev.getAttribute('data-impaza-stepper-prev') === key) {
            e.preventDefault();
            setStepperState(key, Math.max(1, cur - 1));
          }
          if (btnNext && btnNext.getAttribute('data-impaza-stepper-next') === key) {
            e.preventDefault();
            setStepperState(key, Math.min(4, cur + 1));
          }
          if (btnStep && btnStep.closest('[data-impaza-stepper="'+key+'"]')) {
            e.preventDefault();
            var s = parseInt(btnStep.getAttribute('data-impaza-step') || '1', 10) || 1;
            setStepperState(key, Math.max(1, Math.min(4, s)));
          }
        });

        document.addEventListener('shown.bs.modal', function (e) {
          if (e && e.target && e.target.id === 'createFaultModal') {
            initFaultCreateStepper();
          }
        });
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
            var routeName = @json(optional(request()->route())->getName());
            var autoRefreshRoutes = ['home', 'faults.index', 'dashboard.reports', 'call_centre.reports'];

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

              try {
                if (autoRefreshRoutes.indexOf(routeName) === -1) return;
                if (document.hidden) return;
                if (document.querySelector('.modal.show')) return;
                var ae = document.activeElement;
                if (ae && (ae.tagName === 'INPUT' || ae.tagName === 'SELECT' || ae.tagName === 'TEXTAREA')) return;
                var refreshUrl = new URL(window.location.href);
                refreshUrl.searchParams.set('_ar', String(Date.now()));
                window.location.replace(refreshUrl.toString());
              } catch (e) {}
            }, seconds * 1000);
          })();
        </script>

        @include('partials.idle_logout')
</body>

</html>



