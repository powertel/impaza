@extends('layouts.admin')

@section('title')
 Permissions
@endsection
@include('partials.css')
@section('styles')
<style>
  .permissions-page .permissions-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto;
  }

  .permissions-page .permissions-toolbar-search {
    width: 100%;
    min-width: 0;
  }

  .permissions-page .permissions-toolbar-search .input-group {
    width: 100%;
  }

  @media (max-width: 991.98px) {
    .permissions-page .permissions-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .permissions-page .permissions-toolbar-search {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .permissions-page .permissions-toolbar {
      grid-template-columns: 1fr;
    }

    .permissions-page .permissions-toolbar-search {
      grid-column: auto;
    }
  }
</style>
@endsection
@section('content')
@php
  $permissionCount = $permissions->count();
  $permissionGroups = $permissions->groupBy(fn ($permission) => explode('-', $permission->name)[0] ?? 'general')->count();
  $recentPermissions = $permissions->filter(fn ($permission) => optional($permission->created_at)->gte(now()->subDays(30)))->count();
@endphp
<section class="content workflow-faults-page permissions-page">
    <div class="workspace-summary-grid">
        <div class="workspace-summary-card" style="--summary-color:#6366F1;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-key"></i></span>
                    <div>
                        <div class="workspace-summary-label">Total Permissions</div>
                        <div class="workspace-summary-title">Access catalog</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $permissionCount }}</div>
            </div>
        </div>
        <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-layer-group"></i></span>
                    <div>
                        <div class="workspace-summary-label">Permission Groups</div>
                        <div class="workspace-summary-title">Module prefixes</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $permissionGroups }}</div>
            </div>
        </div>
        <div class="workspace-summary-card" style="--summary-color:#10B981;">
            <div class="workspace-summary-body">
                <div class="workspace-summary-copy">
                    <span class="workspace-summary-icon"><i class="fas fa-sparkles"></i></span>
                    <div>
                        <div class="workspace-summary-label">Recent Additions</div>
                        <div class="workspace-summary-title">Last 30 days</div>
                    </div>
                </div>
                <div class="workspace-summary-value">{{ $recentPermissions }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Permission Directory</h3>
                <div class="page-lead">Search and review the full list of available permissions from the same modern workspace pattern used across the refreshed sidebar modules.</div>
            </div>
            <div class="card-tools">
                <span class="record-chip"><i class="fas fa-key"></i> {{ $permissionCount }} total records</span>
            </div>
        </div>

        <div class="faults-toolbar">
            <div class="filter-toolbar permissions-toolbar">
                <div class="faults-toolbar-field">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <select id="permissionPageSize" class="form-select" aria-label="Rows per page">
                            <option value="10">Show 10</option>
                            <option value="20" selected>Show 20</option>
                            <option value="50">Show 50</option>
                            <option value="100">Show 100</option>
                            <option value="all">Show All</option>
                        </select>
                    </div>
                </div>

                <div class="permissions-toolbar-search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="permissionSearch" class="form-control" placeholder="Search permission name or module prefix">
                    </div>
                </div>

                <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="document.getElementById('permissionSearch').value=''; document.getElementById('permissionSearch').dispatchEvent(new Event('input', { bubbles: true }));">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#permissionPageSize" data-pager="#permissionPager" data-search="#permissionSearch">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Permission</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission )
                        <tr>
                            <td><span class="age-ticker">#{{ $permission->id }}</span></td>
                            <td>
                                <div class="workspace-cell-main">{{ $permission->name }}</div>
                                <div class="workspace-cell-sub">{{ ucfirst(explode('-', $permission->name)[0] ?? 'General') }} module permission</div>
                            </td>
                            <td>
                                <div class="workspace-cell-main">{{ optional($permission->created_at)->format('d M Y') }}</div>
                                <div class="workspace-cell-sub">{{ optional($permission->created_at)->format('h:i a') }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="permissionPager" class="mt-3"></div>
            </div>
        </div>
    </div>
</section>
@endsection

