@extends('layouts.admin')
@section('title')
Roles
@endsection
@section('scripts')
    @include('partials.scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[id^="roleCreateModal"], [id^="roleShowModal"], [id^="roleEditModal"]').forEach(function (modal) {
          if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
          }
        });

        function initPermissionModal(modal) {
          const search = modal.querySelector('.permission-search');
          const items = modal.querySelectorAll('.permission-list .form-check');
          const selectAllBtn = modal.querySelector('.select-all-permissions');
          const clearAllBtn = modal.querySelector('.clear-all-permissions');
          if (search) {
            search.addEventListener('input', function () {
              const term = this.value.toLowerCase();
              items.forEach(item => {
                const label = item.querySelector('.form-check-label').textContent.toLowerCase();
                item.style.display = label.includes(term) ? '' : 'none';
              });
            });
          }
          if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function () {
              modal.querySelectorAll('.permission-list input[type="checkbox"]').forEach(cb => cb.checked = true);
            });
          }
          if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function () {
              modal.querySelectorAll('.permission-list input[type="checkbox"]').forEach(cb => cb.checked = false);
            });
          }
        }

        document.querySelectorAll('.modal').forEach(function (m) {
          m.addEventListener('shown.bs.modal', function () { initPermissionModal(this); }, { once: true });
        });

        const rolesSearch = document.getElementById('rolesSearch');
        const rolesSearchButton = document.getElementById('rolesSearchButton');
        const rolesResetButton = document.getElementById('rolesResetButton');
        if (rolesSearchButton && rolesSearch) {
          rolesSearchButton.addEventListener('click', function () {
            rolesSearch.dispatchEvent(new Event('input', { bubbles: true }));
            rolesSearch.focus();
          });
        }
        if (rolesResetButton && rolesSearch) {
          rolesResetButton.addEventListener('click', function () {
            rolesSearch.value = '';
            rolesSearch.dispatchEvent(new Event('input', { bubbles: true }));
          });
        }
      });
    </script>
@endsection
@include('partials.css')
@section('styles')
<style>
  .roles-page .roles-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(260px, 1fr) auto auto;
  }

  .roles-page .roles-toolbar-search {
    width: 100%;
    min-width: 0;
  }

  .roles-page .roles-toolbar-search .input-group {
    width: 100%;
  }

  @media (max-width: 991.98px) {
    .roles-page .roles-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .roles-page .roles-toolbar-search {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .roles-page .roles-toolbar {
      grid-template-columns: 1fr;
    }

    .roles-page .roles-toolbar-search {
      grid-column: auto;
    }
  }
</style>
@endsection
@section('content')
@php
  $roleCount = $roles->count();
  $permissionAssignments = $roles->sum(fn ($role) => $role->permissions->count());
  $rolesWithPermissions = $roles->filter(fn ($role) => $role->permissions->isNotEmpty())->count();
@endphp
<section class="content workflow-faults-page roles-page">
  <div class="workspace-summary-grid">
    <div class="workspace-summary-card" style="--summary-color:#6366F1;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-user-tag"></i></span>
          <div>
            <div class="workspace-summary-label">Total Roles</div>
            <div class="workspace-summary-title">Access groups</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $roleCount }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#0EA5E9;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-shield-alt"></i></span>
          <div>
            <div class="workspace-summary-label">Permission Links</div>
            <div class="workspace-summary-title">Assigned capabilities</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $permissionAssignments }}</div>
      </div>
    </div>
    <div class="workspace-summary-card" style="--summary-color:#10B981;">
      <div class="workspace-summary-body">
        <div class="workspace-summary-copy">
          <span class="workspace-summary-icon"><i class="fas fa-check-circle"></i></span>
          <div>
            <div class="workspace-summary-label">Configured Roles</div>
            <div class="workspace-summary-title">With permissions mapped</div>
          </div>
        </div>
        <div class="workspace-summary-value">{{ $rolesWithPermissions }}</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Manage Roles</h3>
        <div class="page-lead">Search, review, edit, and govern role permissions from one modern workspace with the same responsive controls used across the updated modules.</div>
      </div>
      <div class="card-tools">
        <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $roleCount }} total records</span>
        @can('role-create')
          <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#roleCreateModal">
            <i class="fas fa-plus-circle me-1"></i> Create Role
          </button>
        @endcan
      </div>
    </div>

    <div class="faults-toolbar">
      <div class="filter-toolbar roles-toolbar">
        <div class="faults-toolbar-field">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-list"></i></span>
            <select id="rolesPageSize" class="form-select" aria-label="Rows per page">
              <option value="10">Show 10</option>
              <option value="20" selected>Show 20</option>
              <option value="50">Show 50</option>
              <option value="100">Show 100</option>
              <option value="all">Show All</option>
            </select>
          </div>
        </div>

        <div class="roles-toolbar-search">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="rolesSearch" class="form-control" placeholder="Search roles or permission names">
          </div>
        </div>

        <button type="button" id="rolesSearchButton" class="btn btn-primary btn-sm px-3">
          <i class="fas fa-search me-1"></i> Search
        </button>

        <button type="button" id="rolesResetButton" class="btn btn-outline-secondary btn-sm px-3">
          <i class="fas fa-rotate-left me-1"></i> Reset
        </button>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle js-paginated-table" data-page-size="20" data-page-size-control="#rolesPageSize" data-pager="#rolesPager" data-search="#rolesSearch">
          <thead>
            <tr>
              <th style="width:60px">#</th>
              <th>Role</th>
              <th>Permissions</th>
              <th class="text-end" width="280px">Action(s)</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($roles as $role)
              <tr>
                <td><span class="age-ticker">#{{ ++$i }}</span></td>
                <td>
                  <div class="workspace-cell-main">{{ $role->name }}</div>
                  <div class="workspace-cell-sub">{{ $role->permissions->count() }} permission{{ $role->permissions->count() === 1 ? '' : 's' }} assigned</div>
                </td>
                <td>
                  <div class="workspace-chip-stack">
                    @forelse ($role->permissions as $perm)
                      <span class="badge rounded-pill" style="background: rgba(245, 158, 11, .14); color: #B45309;"><i class="fas fa-shield-alt me-1"></i>{{ $perm->name }}</span>
                    @empty
                      <span class="workspace-cell-sub">No permissions assigned</span>
                    @endforelse
                  </div>
                </td>
                <td>
                  <div class="workspace-actions">
                    <form name="theForm" action="{{ route('roles.destroy',$role->id) }}" method="POST">
                      <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#roleShowModal{{ $role->id }}">
                        <i class="fas fa-eye me-1"></i> View
                      </button>
                      @can('role-edit')
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#roleEditModal{{ $role->id }}">
                          <i class="fas fa-pen me-1"></i> Edit
                        </button>
                      @endcan
                      @csrf
                      @method('DELETE')
                      @can('role-delete')
                        <button type="button" class="btn btn-outline-danger btn-sm show_confirm" data-toggle="tooltip" title="Delete">
                          <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                      @endcan
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div id="rolesPager" class="mt-3"></div>
      </div>
    </div>
  </div>
</section>

@include('roles.create_modal')
@include('roles.show_modal')
@include('roles.edit_modal')

@endsection

