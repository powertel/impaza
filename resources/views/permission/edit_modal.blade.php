<div class="modal custom-modal fade" id="editPermModal-{{ $permission->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editPermModalLabel-{{ $permission->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('permission.update', $permission->id) }}">
        @csrf @method('PUT')
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0" id="editPermModalLabel-{{ $permission->id }}"><i class="fas fa-pencil me-2 text-primary"></i>Edit Permission</h5>
            <div class="small text-muted mt-1">
              Currently: <span class="font-monospace">{{ $permission->name }}</span>
              @if($permission->roles && $permission->roles->count() > 0)
                · Assigned to {{ $permission->roles->count() }} role(s)
              @endif
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning py-2 px-3 small">
            <i class="fas fa-triangle-exclamation me-1"></i>
            Renaming a permission preserves existing role/user assignments under the new name. Ensure code that checks the old name is also updated.
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Permission Name *</label>
            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
              value="{{ old('name', $permission->name) }}" required maxlength="125"
              placeholder="e.g. material-create">
            @error('name')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
            <div class="form-text small">Pattern <code class="font-monospace">[module]-[action]</code>, e.g. <code>stores-process</code>.</div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Guard *</label>
            <select name="guard_name" class="form-select form-select-sm @error('guard_name') is-invalid @enderror" required>
              <option value="web" {{ old('guard_name', $permission->guard_name ?? 'web') === 'web' ? 'selected' : '' }}>web (default)</option>
              <option value="api" {{ old('guard_name', $permission->guard_name ?? 'web') === 'api' ? 'selected' : '' }}>api</option>
            </select>
            @error('guard_name')
              <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
          </div>
          <div class="small text-muted">
            <i class="fas fa-clock-rotate-left me-1"></i>
            Created: {{ optional($permission->created_at)->format('d M Y, H:i') }}
            @if($permission->updated_at && optional($permission->updated_at)->ne($permission->created_at))
              · Last updated: {{ optional($permission->updated_at)->format('d M Y, H:i') }}
            @endif
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i class="fas fa-save me-1"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
