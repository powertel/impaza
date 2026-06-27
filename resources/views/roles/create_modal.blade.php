@can('role-create')
<div class="modal custom-modal fade" id="roleCreateModal" tabindex="-1" aria-labelledby="roleCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="roleCreateModalLabel"><i class="fas fa-user-tag me-2"></i>Create Role</h5>
          <div class="modal-subtitle">Configure a new access group and map the permissions it should control.</div>
        </div>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-helper mb-3">
            <i class="fas fa-shield-alt"></i>
            <span>Use the search box and quick actions to assign only the capabilities this role should have.</span>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-title">
              <i class="fas fa-id-badge"></i>
              <span>Role Setup</span>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <label for="role_name_create" class="form-label">Role Name</label>
                <input type="text" id="role_name_create" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Administrator" value="{{ old('name') }}" required>
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
                      <div class="col permission-item">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permission[]" id="perm_create_{{ $value->id }}" value="{{ $value->id }}">
                          <label class="form-check-label ms-2" for="perm_create_{{ $value->id }}"><strong>{{ $value->name }}</strong></label>
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
@endcan
