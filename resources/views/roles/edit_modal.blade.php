@can('role-edit')
@foreach($roles as $role)
<div class="modal fade" id="roleEditModal{{ $role->id }}" tabindex="-1" aria-labelledby="roleEditModalLabel{{ $role->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleEditModalLabel{{ $role->id }}">Edit Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.update', $role->id ) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label for="role_name_edit_{{ $role->id }}" class="form-label">Role Name</label>
              <input type="text" id="role_name_edit_{{ $role->id }}" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $role->name }}" required>
            </div>
            <div class="col-12">
              <div class="d-flex align-items-center mb-2">
                <label class="form-label mb-0 me-2">Permissions</label>
                <div class="ms-auto">
                  <button type="button" class="btn btn-light btn-sm select-all-permissions"><i class="fas fa-check-double"></i> Select All</button>
                  <button type="button" class="btn btn-light btn-sm clear-all-permissions"><i class="fas fa-times-circle"></i> Clear</button>
                </div>
              </div>
              <input type="text" class="form-control form-control-sm permission-search" placeholder="Search permissions...">
              <div class="border rounded p-3 mt-3 permission-list" style="max-height: 400px; overflow: auto;">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                  @foreach($permission as $value)
                    @php $checked = isset($rolePermissionsMap[$role->id]) && in_array($value->id, $rolePermissionsMap[$role->id]); @endphp
                    <div class="col permission-item">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permission[]" id="perm_edit_{{ $role->id }}_{{ $value->id }}" value="{{ $value->id }}" {{ $checked ? 'checked' : '' }}>
                        <label class="form-check-label ms-2" for="perm_edit_{{ $role->id }}_{{ $value->id }}"><strong>{{ $value->name }}</strong></label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-success btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button> 
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endcan
