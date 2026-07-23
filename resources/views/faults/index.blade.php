@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('Faults')
Faults
@endsection

@include('partials.css')

@section('styles')
<style>
  .faults-page .content {
    padding-inline: 6px;
  }

  .faults-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
  }

  .faults-kpi-link {
    text-decoration: none;
    color: inherit;
    display: block;
    min-width: 0;
  }

  .faults-kpi-card {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 108px;
    border-radius: 18px;
    border: 1px solid var(--impaza-border);
    background: var(--impaza-card);
    box-shadow: var(--impaza-shadow-sm);
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .faults-kpi-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--faults-kpi-color, var(--impaza-primary));
  }

  .faults-kpi-link:hover .faults-kpi-card,
  .faults-kpi-link:focus-visible .faults-kpi-card {
    transform: translateY(-2px);
    box-shadow: var(--impaza-shadow);
    border-color: color-mix(in srgb, var(--faults-kpi-color, var(--impaza-primary)) 26%, var(--impaza-border));
  }

  .faults-kpi-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    min-width: 0;
  }

  .faults-kpi-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .faults-kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .92rem;
    color: var(--faults-kpi-color, var(--impaza-primary));
    background: color-mix(in srgb, var(--faults-kpi-color, var(--impaza-primary)) 12%, transparent);
    flex: 0 0 auto;
  }

  .faults-kpi-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .faults-kpi-title {
    font-size: .86rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.2;
  }

  .faults-kpi-value {
    font-size: 1.65rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
    flex: 0 0 auto;
  }

  .faults-panel {
    border: 1px solid var(--impaza-border);
    border-radius: 22px;
    background: var(--impaza-card);
    box-shadow: var(--impaza-shadow-sm);
    overflow: visible;
  }

  .faults-panel-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
    border-bottom: 1px solid var(--impaza-border);
    background: color-mix(in srgb, var(--impaza-primary) 4%, var(--impaza-card));
  }

  .faults-panel-copy {
    min-width: 0;
  }

  .faults-panel-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--impaza-text);
    letter-spacing: -.01em;
  }

  .faults-panel-subtitle {
    margin-top: 4px;
    color: var(--impaza-muted);
    font-size: .74rem;
    line-height: 1.4;
  }

  .faults-panel-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 10px;
  }

  .faults-toolbar {
    padding: 18px 20px;
    border-bottom: 1px solid var(--impaza-border);
    background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
  }

  .faults-toolbar-grid {
    display: grid;
    grid-template-columns: 116px minmax(170px, 1fr) minmax(150px, 1fr) minmax(240px, 1.35fr) auto auto;
    gap: 12px;
    align-items: center;
  }

  .faults-toolbar-field {
    min-width: 0;
  }

  .faults-toolbar .input-group-text,
  .faults-toolbar .form-control,
  .faults-toolbar .form-select {
    min-height: 36px;
    border-color: var(--impaza-border) !important;
    background-color: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card)) !important;
    color: var(--impaza-text) !important;
    box-shadow: none;
  }

  .faults-toolbar .input-group-text {
    color: var(--impaza-muted);
  }

  .faults-toolbar .form-control::placeholder {
    color: var(--impaza-muted);
    opacity: 1;
  }

  .faults-toolbar .form-control:focus,
  .faults-toolbar .form-select:focus {
    background-color: var(--impaza-card) !important;
    color: var(--impaza-text) !important;
    border-color: rgba(99, 102, 241, .55) !important;
    box-shadow: 0 0 0 .2rem rgba(99, 102, 241, .12);
  }

  html[data-theme="dark"] .faults-toolbar .form-control,
  html[data-theme="dark"] .faults-toolbar .form-select,
  html[data-theme="dark"] .faults-toolbar .input-group-text {
    background-color: #0f172a !important;
    color: #e2e8f0 !important;
    border-color: #1e293b !important;
  }

  html[data-theme="dark"] .faults-toolbar .form-select {
    color-scheme: dark;
    -webkit-text-fill-color: #e2e8f0;
  }

  html[data-theme="dark"] .faults-toolbar .form-control::placeholder {
    color: #94a3b8;
  }

  html[data-theme="dark"] .faults-toolbar .form-select option {
    background-color: #0f172a;
    color: #e2e8f0;
  }

  .faults-toolbar-field .input-group-text {
    min-width: 42px;
    justify-content: center;
  }

  .faults-toolbar-search .input-group-text {
    min-width: 42px;
  }

  .faults-table-shell {
    padding: 18px 20px 14px;
  }

  .faults-table-wrap {
    border: 0;
    border-radius: 18px;
    background: transparent;
    box-shadow: none;
    overflow: auto;
  }

  .faults-table thead th {
    white-space: nowrap;
    padding: 10px 11px;
    font-size: .7rem;
    border-bottom-color: color-mix(in srgb, var(--impaza-border) 40%, transparent);
  }

  .faults-table tbody td {
    padding: 9px 11px;
    vertical-align: middle;
    border-top-color: color-mix(in srgb, var(--impaza-border) 40%, transparent);
  }

  html[data-theme="dark"] .faults-table thead th {
    border-bottom-color: color-mix(in srgb, var(--impaza-border) 18%, transparent);
  }

  html[data-theme="dark"] .faults-table tbody td {
    border-top-color: color-mix(in srgb, var(--impaza-border) 18%, transparent);
  }

  .faults-table .faults-ref {
    display: inline-flex;
    flex-direction: column;
    gap: 3px;
  }

  .faults-table .faults-ref a {
    font-weight: 700;
  }

  .faults-table .faults-cell-main {
    font-weight: 600;
    color: var(--impaza-text);
    line-height: 1.28;
  }

  .faults-table .faults-cell-sub {
    margin-top: 3px;
    font-size: .72rem;
    line-height: 1.3;
    color: var(--impaza-muted);
  }

  .faults-table .faults-status-link {
    text-decoration: none;
  }

  .faults-table .faults-status-link .impaza-badge {
    min-height: 24px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .01em;
    box-shadow: none;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .faults-table .faults-status-link:hover .impaza-badge,
  .faults-table .faults-status-link:focus-visible .impaza-badge {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
  }

  .faults-age-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 24px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(99, 102, 241, .08);
    color: var(--impaza-primary);
    border: 1px solid rgba(99, 102, 241, .14);
    font-weight: 700;
    font-size: .68rem;
    white-space: nowrap;
  }

  .faults-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 6px;
  }

  .faults-actions .btn {
    min-height: 30px;
    border-radius: 999px;
    padding-inline: 10px;
    font-weight: 600;
  }

  .faults-table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 2px 2px;
  }

  .faults-table-footer .pagination {
    margin-bottom: 0;
  }

  html[data-theme="dark"] .faults-kpi-link:hover .faults-kpi-card,
  html[data-theme="dark"] .faults-kpi-link:focus-visible .faults-kpi-card {
    box-shadow: var(--impaza-shadow);
  }

  @media (max-width: 1399.98px) {
    .faults-toolbar-grid {
      grid-template-columns: 110px minmax(150px, 1fr) minmax(140px, 1fr) minmax(220px, 1.2fr) auto auto;
    }
  }

  @media (max-width: 1199.98px) {
    .faults-kpi-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .faults-toolbar-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .faults-toolbar-search,
    .faults-toolbar-grid .faults-toolbar-submit,
    .faults-toolbar-grid .faults-toolbar-reset {
      grid-column: span 2;
    }
  }

  @media (max-width: 991.98px) {
    .faults-panel-header {
      flex-direction: column;
      align-items: stretch;
    }

    .faults-panel-actions {
      justify-content: flex-start;
    }

    .faults-table-shell {
      padding-inline: 16px;
    }

    .faults-toolbar {
      padding-inline: 16px;
    }
  }

  @media (max-width: 767.98px) {
    .faults-kpi-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .faults-kpi-body {
      padding: 14px 16px;
    }

    .faults-kpi-value {
      font-size: 1.45rem;
    }

    .faults-toolbar-grid {
      grid-template-columns: 1fr;
    }

    .faults-toolbar-search,
    .faults-toolbar-grid .faults-toolbar-submit,
    .faults-toolbar-grid .faults-toolbar-reset {
      grid-column: auto;
    }

    .faults-table-footer {
      flex-direction: column;
      align-items: flex-start;
    }

    .faults-table-shell {
      padding: 14px 14px 12px;
    }

    .faults-table-wrap {
      overflow: visible;
      background: transparent;
      border-radius: 0;
    }

    .faults-table {
      min-width: 0 !important;
      border-collapse: separate;
      border-spacing: 0 10px;
    }

    .faults-table thead {
      display: none;
    }

    .faults-table tbody {
      display: block;
    }

    .faults-table tbody tr {
      display: block;
      border: 1px solid var(--impaza-border);
      border-radius: 16px;
      background: color-mix(in srgb, var(--impaza-primary) 2%, var(--impaza-card));
      box-shadow: var(--impaza-shadow-sm);
      overflow: hidden;
    }

    .faults-table tbody td {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      width: 100%;
      padding: 9px 14px;
      border: 0;
      text-align: right;
    }

    .faults-table tbody td + td {
      border-top: 1px solid color-mix(in srgb, var(--impaza-border) 85%, transparent);
    }

    .faults-table tbody td::before {
      content: attr(data-label);
      flex: 0 0 42%;
      text-align: left;
      font-size: .68rem;
      font-weight: 700;
      letter-spacing: .04em;
      text-transform: uppercase;
      color: var(--impaza-muted);
    }

    .faults-table .faults-ref,
    .faults-table .faults-cell-main,
    .faults-table .faults-cell-sub {
      text-align: right;
    }

    .faults-table .faults-ref {
      align-items: flex-end;
    }

    .faults-table .faults-status-link,
    .faults-table .faults-age-pill {
      margin-left: auto;
    }

    .faults-actions {
      width: 100%;
      justify-content: flex-end;
    }

    .faults-table td.text-end {
      text-align: right !important;
    }
  }
</style>
@endsection

@section('content')
<section class="content faults-page">
@php
  $perPage = request('per_page', 20);
  $statusFilter = request('status', 'all');
  $ageFilter = request('age', 'all');
  $compactStatusLabel = function ($label) {
      return match (strtolower(trim((string) $label))) {
          'fault has been restored', 'resolved' => 'Fault Restored',
          'fault is under rectification', 'under rectification' => 'Under Rectification',
          'waiting for assessment', 'waiting assessment' => 'Waiting Assessment',
          'fault has been assessed', 'assessed' => 'Assessed',
          'fault has been rectified', 'rectified' => 'Rectified',
          'fault has been cleared by ct', 'cleared by ct' => 'Cleared by CT',
          'fault has been refered', 'fault has been referred', 'referred' => 'Referred',
          'fault has been parked', 'parked' => 'Parked',
          'fault has been revoked', 'revoked' => 'Revoked',
          'fault  escalated to chief technician', 'fault escalated to chief technician', 'escalated to chief technician' => 'Escalated',
          'impacted by pop outage' => 'POP Outage',
          'open' => 'Open',
          default => (string) $label,
      };
  };
@endphp

<div class="faults-kpi-grid mb-4">
  <a href="#" class="faults-kpi-link faultsAgeStat" data-age="" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#94A3B8;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-clipboard-list"></i></span>
          <div>
            <div class="faults-kpi-label">All Open Faults</div>
            <div class="faults-kpi-title">View all open faults</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_total'] ?? 0) }}</div>
      </div>
    </div>
  </a>

  <a href="#" class="faults-kpi-link faultsAgeStat" data-age="today" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#2563EB;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-calendar-day"></i></span>
          <div>
            <div class="faults-kpi-label">Logged Today</div>
            <div class="faults-kpi-title">View today's faults</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_today'] ?? 0) }}</div>
      </div>
    </div>
  </a>

  <a href="#" class="faults-kpi-link faultsAgeStat" data-age="lt72" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#10B981;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-hourglass-half"></i></span>
          <div>
            <div class="faults-kpi-label">Within 72 Hours</div>
            <div class="faults-kpi-title">View within 72 hours</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_lt72'] ?? 0) }}</div>
      </div>
    </div>
  </a>

  <a href="#" class="faults-kpi-link faultsAgeStat" data-age="gt72" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#F59E0B;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-hourglass-end"></i></span>
          <div>
            <div class="faults-kpi-label">Over 72 Hours</div>
            <div class="faults-kpi-title">View overdue faults</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_gt72'] ?? 0) }}</div>
      </div>
    </div>
  </a>
</div>

<div class="card faults-panel">
  <div class="faults-panel-header">
    <div class="faults-panel-copy">
      <h3 class="faults-panel-title">Manage and Track Faults</h3>
      <div class="faults-panel-subtitle">Search, filter, review, edit, and export faults from one responsive workspace.</div>
    </div>
    <div class="faults-panel-actions">
      @can('fault-create')
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3"
                data-bs-toggle="modal"
                data-bs-target="#createFaultModal">
          <i class="fas fa-plus-circle me-1"></i> Log Fault
        </button>
      @endcan
      <div class="btn-group">
        <button class="btn btn-outline-secondary btn-sm rounded-pill dropdown-toggle px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-download me-1"></i> Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('faults.export.csv', request()->only('q','status','age')) }}">
              <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('faults.export.pdf', request()->only('q','status','age')) }}">
              <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="faults-toolbar">
    <form method="GET" action="{{ route('faults.index') }}" class="m-0">
      <div class="faults-toolbar-grid">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="faultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
              <option value="10"  {{ (int) $perPage === 10 ? 'selected' : '' }}>Show 10</option>
              <option value="20"  {{ (int) $perPage === 20 ? 'selected' : '' }}>Show 20</option>
              <option value="50"  {{ (int) $perPage === 50 ? 'selected' : '' }}>Show 50</option>
              <option value="100" {{ (int) $perPage === 100 ? 'selected' : '' }}>Show 100</option>
            </select>
          </div>
        </div>

        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-filter"></i></span>
            <select name="status" id="faultsStatusFilter" class="form-select form-select-sm" aria-label="Status filter">
              <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="lt4" {{ $statusFilter === 'lt4' ? 'selected' : '' }}>Open Faults</option>
              @foreach(($openStatuses ?? collect()) as $st)
                <option value="{{ $st->id }}" {{ $statusFilter == (string) $st->id ? 'selected' : '' }}>
                  {{ $compactStatusLabel($st->description) }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-clock"></i></span>
            <select name="age" id="faultsAgeFilter" class="form-select form-select-sm" aria-label="Age filter">
              <option value="all" {{ $ageFilter === 'all' ? 'selected' : '' }}>All Ages</option>
              <option value="today" {{ $ageFilter === 'today' ? 'selected' : '' }}>Today</option>
              <option value="lt72" {{ $ageFilter === 'lt72' ? 'selected' : '' }}>Within 72 Hours</option>
              <option value="gt72" {{ $ageFilter === 'gt72' ? 'selected' : '' }}>Over 72 Hours</option>
            </select>
          </div>
        </div>

        <div class="faults-toolbar-field faults-toolbar-search">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" value="{{ request('q', '') }}" class="form-control" placeholder="Search faults, customers, sites, managers...">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
          <i class="fas fa-search me-1"></i> Search
        </button>
        <a href="{{ route('faults.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
          <i class="fas fa-rotate-left me-1"></i> Reset
        </a>
      </div>
    </form>
  </div>

  <div class="faults-table-shell">
    <div class="table-responsive impaza-table-wrap faults-table-wrap">
      <table class="table table-hover align-middle impaza-table faults-table" id="faults-list">
        <thead>
          <tr>
            <th>Ref. No.</th>
            <th>Customer</th>
            <th>Link</th>
            <th>Switch</th>
            <th>Port</th>
            <th>Assigned To</th>
            <th>Date Reported</th>
            <th>Status</th>
            <th>Age</th>
            <th class="text-end">Action(s)</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($faults as $fault)
            @php
              $rowStatus = trim((string) ($fault->description ?? ''));
              $statusLabel = $compactStatusLabel($rowStatus);
              $statusColor = \App\Models\Status::STATUS_COLOR[$rowStatus] ?? '#64748B';
              $ageText = $faultAges[$fault->id] ?? '';
              $ageStart = $faultAgeStart[$fault->id] ?? null;
              $ageEnd = $faultAgeEnd[$fault->id] ?? null;
              $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
            @endphp
            <tr>
              <td data-label="Ref. No.">
                <div class="faults-ref">
                  <a href="{{ route('faults.index', ['q' => $fault->fault_ref_number]) }}">{{ $fault->fault_ref_number }}</a>
                  <span class="faults-cell-sub">Fault record</span>
                </div>
              </td>
              <td data-label="Customer">
                <div class="faults-cell-main">{{ Str::limit($fault->customer, 26) }}</div>
              </td>
              <td data-label="Link">
                <div class="faults-cell-main">{{ Str::limit($fault->link, 34) }}</div>
              </td>
              <td data-label="Switch">
                <div class="faults-cell-main">{{ $latestRemark->switch_name ?? 'N/A' }}</div>
              </td>
              <td data-label="Port">
                <div class="faults-cell-main">{{ $latestRemark->port ?? 'N/A' }}</div>
              </td>
              <td data-label="Assigned To">
                <div class="faults-cell-main {{ $fault->assignedTo ? '' : 'text-muted' }}">{{ $fault->assignedTo ?: 'Not yet assigned' }}</div>
              </td>
              <td data-label="Date Reported">
                <div class="faults-cell-main">{{ Carbon\Carbon::parse($fault->created_at)->format('d M Y') }}</div>
                <div class="faults-cell-sub">{{ Carbon\Carbon::parse($fault->created_at)->format('h:i a') }}</div>
              </td>
              <td class="text-nowrap" data-label="Status">
                <a href="{{ route('faults.index', ['status' => $fault->status_id]) }}" class="faults-status-link">
                  <x-status-badge :label="$statusLabel" :color="$statusColor" :soft="true" />
                </a>
              </td>
              <td data-label="Age">
                <span class="faults-age-pill fault-age" data-age-start="{{ $ageStart }}" data-age-end="{{ $ageEnd }}">{{ $ageText }}</span>
              </td>
              <td class="text-end" data-label="Actions">
                <div class="faults-actions">
                  @can('fault-edit')
                    @if ($fault->status_id != 6)
                      <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editFaultModal-{{ $fault->id }}">
                        <i class="fas fa-edit me-1"></i> Edit
                      </button>
                    @else
                      <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Editing locked after initial stage">
                        <i class="fas fa-lock me-1"></i> Locked
                      </button>
                    @endif
                  @endcan

                  <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                    <i class="fas fa-eye me-1"></i> View
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center text-muted py-5">No faults to display</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="faults-table-footer">
      <small class="text-muted">
        Showing {{ $faults->firstItem() ?? 0 }} to {{ $faults->lastItem() ?? 0 }} of {{ $faults->total() }} results
      </small>
      {{ $faults->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

@include('faults.create')

@foreach ($faults as $fault)
  @if ($fault->status_id != 6)
    @include('faults.edit', [
        'fault' => $fault,
        'customers' => $customer,
        'cities' => $city,
        'suburbs' => $location,
        'pops' => $pop,
        'links' => $link,
        'accountManagers' => $accountManager,
        'suspectedRFO' => $suspectedRFO,
        'remarks' => ($remarksByFault[$fault->id] ?? collect())
    ])
  @endif
  @include('faults.show', [
      'fault' => $fault,
      'remarks' => ($remarksByFault[$fault->id] ?? collect()),
      'ageText' => ($faultAges[$fault->id] ?? ''),
      'ageStart' => ($faultAgeStart[$fault->id] ?? null),
      'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
  ])
@endforeach
</section>
@endsection

@section('scripts')
    @include('partials.faults')
    <script>
      document.getElementById('faultsPageSize')?.addEventListener('change', function(){
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', this.value);
        params.delete('page');
        window.location.search = params.toString();
      });
      // Auto-submit on filter change
      document.getElementById('faultsStatusFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
      document.getElementById('faultsAgeFilter')?.addEventListener('change', function(){
        // Update select coloration prior to submit
        try {
          const el = this;
          el.classList.remove('age-select-all','age-select-today','age-select-lt72','age-select-gt72');
          const map = { all: 'age-select-all', today: 'age-select-today', lt72: 'age-select-lt72', gt72: 'age-select-gt72' };
          if (map[el.value]) el.classList.add(map[el.value]);
        } catch (e) { /* noop */ }
        this.form?.submit();
      });
      // Initial age select coloration
      (function(){
        const el = document.getElementById('faultsAgeFilter');
        if (!el) return;
        el.classList.remove('age-select-all','age-select-today','age-select-lt72','age-select-gt72');
        const map = { all: 'age-select-all', today: 'age-select-today', lt72: 'age-select-lt72', gt72: 'age-select-gt72' };
        if (map[el.value]) el.classList.add(map[el.value]);
      })();

      // Clickable age stats cards -> set status=lt4 and age, preserve q/per_page
      document.querySelectorAll('.faultsAgeStat').forEach(function(el){
        el.addEventListener('click', function(e){
          e.preventDefault();
          const params = new URLSearchParams(window.location.search);
          params.set('status', this.getAttribute('data-status'));
          const age = this.getAttribute('data-age');
          if (!age) params.delete('age'); else params.set('age', age);
          params.delete('page');
          window.location.search = params.toString();
        });
      });

      function fmtAge(ms){
        const totalMinutes = Math.floor(ms / 60000);
        const days = Math.floor(totalMinutes / (60*24));
        const hours = Math.floor((totalMinutes - days*60*24) / 60);
        const minutes = totalMinutes - days*60*24 - hours*60;
        let s = '';
        if (days > 0) s += days + 'd ';
        s += hours + 'h ' + minutes + 'm';
        return s;
      }
      function updateAges(){
        const now = Date.now();
        document.querySelectorAll('.fault-age').forEach(function(el){
          const start = el.getAttribute('data-age-start');
          const end = el.getAttribute('data-age-end');
          if (!start) return;
          const startMs = Date.parse(start);
          const endMs = end ? Date.parse(end) : null;
          const diff = (endMs ? endMs : now) - startMs;
          if (diff < 0) return;
          el.textContent = fmtAge(diff);
        });
      }
      updateAges();
      setInterval(updateAges, 60000);
    </script>
@endsection


