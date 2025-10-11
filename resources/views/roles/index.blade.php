@extends('layouts.admin')
@section('title')
Roles
@endsection
@section('scripts')
    @include('partials.scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
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
      });
    </script>
@endsection
@include('partials.css')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Roles</h3>
        <div class="card-tools">
            @can('role-create')
               <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#roleCreateModal"><i class="fas fa-plus-circle"></i> {{_('Add New Role')}} </button>  
            @endcan
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th width="280px">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($roles as $role)
                    <tr>
                        <td>{{++$i}}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach ($role->permissions as $perm)
                                <span class="badge bg-warning text-dark rounded-pill me-1 mb-1"><i class="fas fa-shield-alt"></i> {{ $perm->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <form name="theForm" action="{{ route('roles.destroy',$role->id) }}" method="POST">
                                <button type="button" class="btn btn-info btn-sm d-inline-flex align-items-center" style="padding:2px 8px; color:#fff;" data-bs-toggle="modal" data-bs-target="#roleShowModal{{ $role->id }}"><i class="fas fa-eye me-1"></i> View</button>
                                @can('role-edit')
                                <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center" style="padding:2px 8px; color:#fff;" data-bs-toggle="modal" data-bs-target="#roleEditModal{{ $role->id }}"><i class="fas fa-pen me-1"></i> Edit</button>
                                @endcan

                                @csrf
                                @method('DELETE')
                                @can('role-delete')
                                <button type="button" class="btn btn-danger btn-sm show_confirm d-inline-flex align-items-center" data-toggle="tooltip" title='Delete' style="padding:2px 8px; color:#fff;"><i class="fas fa-trash-alt me-1"></i> Delete</button> 
                                @endcan
                            </form>
                        </td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

@include('roles.create_modal')
@include('roles.show_modal')
@include('roles.edit_modal')

@endsection
