@extends('layouts.admin')

@section('title')
Account Managers
@endsection

@include('partials.css')
@section('styles')
<style>
  .account-managers-page .am-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .account-managers-page .am-summary-card {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 104px;
    border-radius: 18px;
    border: 1px solid var(--impaza-border);
    background: var(--impaza-card);
    box-shadow: var(--impaza-shadow-sm);
    overflow: hidden;
  }

  .account-managers-page .am-summary-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--summary-color, var(--impaza-primary));
  }

  .account-managers-page .am-summary-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .account-managers-page .am-summary-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .account-managers-page .am-summary-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--summary-color, var(--impaza-primary));
    background: color-mix(in srgb, var(--summary-color, var(--impaza-primary)) 12%, transparent);
    font-size: .95rem;
    flex: 0 0 auto;
  }

  .account-managers-page .am-summary-label {
    font-size: .72rem;
    color: var(--impaza-muted);
  }

  .account-managers-page .am-summary-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .account-managers-page .am-summary-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .account-managers-page .am-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(240px, 1fr) auto auto;
  }

  .account-managers-page .am-toolbar .toolbar-search-form,
  .account-managers-page .am-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .account-managers-page .am-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  @media (max-width: 991.98px) {
    .account-managers-page .am-summary-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .account-managers-page .am-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .account-managers-page .am-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .account-managers-page .am-summary-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .account-managers-page .am-toolbar {
      grid-template-columns: 1fr;
    }

    .account-managers-page .am-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection
@section('content')
@php
  $managerCount = $account_managers->count();
  $assignedUserCount = $account_managers->pluck('user_id')->filter()->unique()->count();
  $namedManagerCount = $account_managers->pluck('name')->filter()->unique()->count();
@endphp

<section class="content workflow-faults-page account-managers-page">

<div class="am-summary-grid">
  <div class="am-summary-card" style="--summary-color:#6366F1;">
    <div class="am-summary-body">
      <div class="am-summary-copy">
        <span class="am-summary-icon"><i class="fas fa-user-tie"></i></span>
        <div>
          <div class="am-summary-label">Total Managers</div>
          <div class="am-summary-title">Assigned owners</div>
        </div>
      </div>
      <div class="am-summary-value">{{ $managerCount }}</div>
    </div>
  </div>
  <div class="am-summary-card" style="--summary-color:#0EA5E9;">
    <div class="am-summary-body">
      <div class="am-summary-copy">
        <span class="am-summary-icon"><i class="fas fa-users"></i></span>
        <div>
          <div class="am-summary-label">Linked Users</div>
          <div class="am-summary-title">User mappings</div>
        </div>
      </div>
      <div class="am-summary-value">{{ $assignedUserCount }}</div>
    </div>
  </div>
  <div class="am-summary-card" style="--summary-color:#10B981;">
    <div class="am-summary-body">
      <div class="am-summary-copy">
        <span class="am-summary-icon"><i class="fas fa-layer-group"></i></span>
        <div>
          <div class="am-summary-label">Named Records</div>
          <div class="am-summary-title">Visible roster</div>
        </div>
      </div>
      <div class="am-summary-value">{{ $namedManagerCount }}</div>
    </div>
  </div>
</div>

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <div>
            <h3 class="card-title">Manage Account Managers</h3>
            <div class="page-lead">Search, review, and maintain account manager ownership records using the same modern workspace pattern as the other business pages.</div>
        </div>
        <div class="card-tools">
            <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $managerCount }} total records</span>
            @can('account-manager-create')
            <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#accountManagerCreateModal">
                <i class="fas fa-plus-circle me-1"></i>Create Account Manager
            </button>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="faults-toolbar">
        <div class="filter-toolbar am-toolbar">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="accountManagersPageSize" class="form-select">
                        <option value="10">Show 10</option>
                        <option value="20" selected>Show 20</option>
                        <option value="50">Show 50</option>
                        <option value="100">Show 100</option>
                        <option value="all">Show All</option>
                    </select>
                </div>
            </div>
            <form class="toolbar-search-form" onsubmit="return false;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="accountManagersSearch" class="form-control" placeholder="Search Account managers">
                </div>
            </form>
            <button type="button" class="btn btn-primary btn-sm" id="accountManagersApplyFilters"><i class="fas fa-search me-1"></i> Search</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="accountManagersResetFilters"><i class="fas fa-rotate-left me-1"></i> Reset</button>
        </div>
    </div>
    <div class="card-body">
        <div class="faults-table-shell">
        <div class="table-responsive">
            <table class="table table-hover align-middle js-paginated-table" data-page-size="20" data-page-size-control="#accountManagersPageSize" data-pager="#accountManagersPager" data-search="#accountManagersSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Account Manager</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($account_managers as $acc_manager)
                    <tr>
                        <td data-label="No."><span class="record-chip">#{{ ++$i }}</span></td>
                        <td data-label="Account Manager">
                            <div class="record-meta">
                                <span class="record-main">{{ $acc_manager->name ?? '—' }}</span>
                                <span class="record-sub">Customer owner</span>
                            </div>
                        </td>
                        <td class="text-end" data-label="Action(s)">
                          <div class="workflow-actions">
                            <form action="{{ route('account_managers.destroy',$acc_manager->id) }}" method="POST">
                                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#accountManagerViewModal{{ $acc_manager->id }}">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                                @can('account-manager-edit')
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#accountManagerEditModal{{ $acc_manager->id }}">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                @endcan

                                @csrf
                                @method('DELETE')
                                @can('account-manager-delete')
                                <button type="button" class="btn btn-outline-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' >
                                <i class="fas fa-trash me-1"></i>  Delete
                                </button> 
                                @endcan
                            </form>
                          </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div class="workflow-pagination">
              <small class="table-note">Use the toolbar above to narrow down ownership records instantly.</small>
              <div id="accountManagersPager" class="m-0"></div>
            </div>

        </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@include('account_managers.create_modal')
@include('account_managers.edit_modal')
@include('account_managers.view_modal')
@endsection

@section('scripts')
<script>
  document.getElementById('accountManagersApplyFilters')?.addEventListener('click', function () {
    const search = document.getElementById('accountManagersSearch');
    if (search) {
      search.dispatchEvent(new Event('input', { bubbles: true }));
      search.focus();
    }
  });

  document.getElementById('accountManagersResetFilters')?.addEventListener('click', function () {
    const search = document.getElementById('accountManagersSearch');
    const pageSize = document.getElementById('accountManagersPageSize');
    if (search) {
      search.value = '';
      search.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (pageSize) {
      pageSize.value = '20';
      pageSize.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });
</script>
@endsection
