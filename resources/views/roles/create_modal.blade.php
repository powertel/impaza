@can('role-create')
<div class="modal fade" id="roleCreateModal" tabindex="-1" aria-labelledby="roleCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleCreateModalLabel">Create Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label for="role_name_create" class="form-label">Role Name</label>
              <input type="text" id="role_name_create" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Administrator" value="{{ old('name') }}" required>
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
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan