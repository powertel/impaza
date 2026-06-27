@can('role-edit')
@foreach($roles as $role)
<div class="modal custom-modal fade" id="roleEditModal{{ $role->id }}" tabindex="-1" aria-labelledby="roleEditModalLabel{{ $role->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="roleEditModalLabel{{ $role->id }}"><i class="fas fa-user-cog me-2"></i>Edit Role</h5>
          <div class="modal-subtitle">Update the role name and refine which permissions are granted.</div>
        </div>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.update', $role->id ) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-helper mb-3">
            <i class="fas fa-pen-nib"></i>
            <span>Keep role access focused by reviewing the permission map before saving your changes.</span>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-title">
              <i class="fas fa-id-badge"></i>
              <span>Role Setup</span>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <label for="role_name_edit_{{ $role->id }}" class="form-label">Role Name</label>
                <input type="text" id="role_name_edit_{{ $role->id }}" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $role->name }}" required>
              </div>
            </div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-title">
              <i class="fas fa-key"></i>
              <span>Permission Access</span>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                  <label class="form-label mb-0">Permissions</label>
                  <div class="ms-auto d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-light btn-sm select-all-permissions"><i class="fas fa-check-double me-1"></i>Select All</button>
                    <button type="button" class="btn btn-light btn-sm clear-all-permissions"><i class="fas fa-times-circle me-1"></i>Clear</button>
                  </div>
                </div>
                <input type="text" class="form-control form-control-sm permission-search" placeholder="Search permissions...">
                <div class="border rounded p-3 mt-3 permission-list" style="max-height: 400px; overflow: auto;">
                  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
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
