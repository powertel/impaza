@extends('layouts.admin')
@section('title')
Users
@endsection
@include('partials.css')
@section('styles')
<style>
  .users-page .users-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto auto;
  }

  .users-page .toolbar-search-form,
  .users-page .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .users-page .users-status-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .users-page .users-status-primary {
    color: #111827 !important;
  }

  @media (max-width: 991.98px) {
    .users-page .users-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .users-page .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .users-page .users-toolbar {
      grid-template-columns: 1fr;
    }

    .users-page .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection
@section('content')
@php
  $perPage = request('per_page', 20);
  $visibleUsers = collect($users->items());
  $enabledUsers = $visibleUsers->where('is_access', 0)->count();
  $disabledUsers = $visibleUsers->where('is_access', 1)->count();
@endphp

<section class="content workflow-faults-page users-page">
  <div class="workspace-summary-grid">
    <div class="workspace-summary-card" style="--summary-color:#6366F1;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-users"></i></span>
          <div>
            <div class="workspace-summary-label">Total Users</div>
            <div class="workspace-summary-title">Directory coverage</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $users->total() }}</div>
      </div>
    </div>

    <div class="workspace-summary-card" style="--summary-color:#10B981;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-check"></i></span>
          <div>
            <div class="workspace-summary-label">Access Enabled</div>
            <div class="workspace-summary-title">Visible on this page</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $enabledUsers }}</div>
      </div>
    </div>

    <div class="workspace-summary-card" style="--summary-color:#F59E0B;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-lock"></i></span>
          <div>
            <div class="workspace-summary-label">Access Disabled</div>
            <div class="workspace-summary-title">Needs review</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $disabledUsers }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Users</h3>
        <div class="page-lead">Search, review, edit, and manage platform access from one responsive workspace with dark-theme friendly controls.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-user-friends"></i> {{ $users->total() }} total records</span>
        @can('user-create')
          <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus-circle me-1"></i> Create User
          </button>
        @endcan
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar users-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="usersPageSize" class="form-select" aria-label="Rows per page">
              <option value="10" {{ (int)$perPage===10 ? 'selected' : '' }}>Show 10</option>
              <option value="20" {{ (int)$perPage===20 ? 'selected' : '' }}>Show 20</option>
              <option value="50" {{ (int)$perPage===50 ? 'selected' : '' }}>Show 50</option>
              <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>Show 100</option>
            </select>
          </div>
        </div>

        <form id="usersSearchForm" method="GET" action="{{ route('users.index') }}" class="toolbar-search-form">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search users, emails, roles, departments, or sections">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
          </div>
        </form>

        <button type="submit" form="usersSearchForm" class="btn btn-primary btn-sm px-3">
          <i class="fas fa-search me-1"></i> Search
        </button>

        <a href="{{ route('users.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm px-3">
          <i class="fas fa-rotate-left me-1"></i> Reset
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover faults-table" id="usersTable">
          <thead>
            <tr>
              <th>No.</th>
              <th>User</th>
              <th>Roles</th>
              <th>Department</th>
              <th>Section</th>
              <th>Status</th>
              <th class="text-end">Action(s)</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
              <tr>
                <td>
                  <span class="age-ticker">#{{ $users->firstItem() + $loop->index }}</span>
                </td>
                <td>
                  <div class="workspace-cell-main">{{ $user->name }}</div>
                  <div class="workspace-cell-sub">{{ $user->email }}</div>
                  <div class="workspace-cell-sub">Last login: {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never' }}</div>
                </td>
                <td>
                  <div class="workspace-chip-stack">
                    @forelse($user->getRoleNames() as $v)
                      <span class="badge rounded-pill" style="background: rgba(14, 165, 233, .12); color: #0369A1;">{{ $v }}</span>
                    @empty
                      <span class="workspace-cell-sub">No roles assigned</span>
                    @endforelse
                  </div>
                </td>
                <td>
                  <div class="workspace-cell-main">{{ $user->department ?: 'Not assigned' }}</div>
                  <div class="workspace-cell-sub">Department mapping</div>
                </td>
                <td>
                  <div class="workspace-cell-main">{{ $user->section ?: 'Not assigned' }}</div>
                  <div class="workspace-cell-sub">Section coverage</div>
                </td>
                <td class="text-nowrap">
                  <div class="users-status-stack">
                    <span class="badge rounded-pill users-status-primary" style="background-color: {{ App\Models\UserStatus::STATUS_COLOR[$user->status_name] ?? '#CBD5E1' }};">
                      {{ $user->status_name }}
                    </span>
                    <span class="badge rounded-pill {{ ((int)($user->is_access ?? 0) === 0) ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                      {{ ((int)($user->is_access ?? 0) === 0) ? 'Enabled' : 'Disabled' }}
                    </span>
                  </div>
                </td>
                <td>
                  <div class="workspace-actions">
                    <form action="{{ route('users.destroy',$user->id) }}" method="POST">
                      <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showUserModal-{{ $user->id }}">
                        <i class="fas fa-eye me-1"></i> View
                      </button>
                      @can('user-edit')
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
                          <i class="fas fa-edit me-1"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm {{ ((int)($user->is_access ?? 0) === 0) ? 'btn-outline-danger' : 'btn-outline-success' }}" data-bs-toggle="modal" data-bs-target="#accessUserModal-{{ $user->id }}">
                          <i class="fas fa-user-lock me-1"></i> {{ ((int)($user->is_access ?? 0) === 0) ? 'Disable' : 'Enable' }}
                        </button>
                      @endcan
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="fas fa-info-circle me-1"></i> No users found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="faults-table-footer">
        <small class="text-muted">
          @if($users->count())
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
          @else
            Showing 0 results
          @endif
        </small>
        {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
      </div>
      <div id="usersPager" class="mt-2"></div>
    </div>
  </div>

@include('users.create_modal', ['roles' => $roles, 'department' => $department, 'section' => $section, 'position' => $position, 'user_statuses' => $user_statuses, 'regions' => $regions, 'currentUserRegion' => $currentUserRegion])

@foreach ($users as $user)
  @include('users.show_modal', ['user' => $user])
  @include('users.edit_modal', ['user' => $user, 'department' => $department, 'section' => $section, 'position' => $position, 'roles' => $roles, 'user_statuses' => $user_statuses, 'regions' => $regions])
  @include('users.access_modal', ['user' => $user])
  @include('users.change_password_modal', ['user' => $user])
@endforeach

</section>
@endsection

@section('scripts')
@include('partials.users')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="createUserModal"], [id^="showUserModal-"], [id^="editUserModal-"], [id^="accessUserModal-"], [id^="changePasswordModal-"]').forEach(function(modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    const sel = document.getElementById('usersPageSize');
    if (sel) {
        sel.addEventListener('change', function(ev) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', ev.target.value);
            params.delete('page');
            window.location.assign(window.location.pathname + '?' + params.toString());
        });
    }
});
</script>
@endsection
