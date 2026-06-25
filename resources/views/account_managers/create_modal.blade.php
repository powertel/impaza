@can('account-manager-create')
<div class="modal custom-modal fade" id="accountManagerCreateModal" tabindex="-1" aria-labelledby="accountManagerCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="accountManagerCreateModalLabel"><i class="fas fa-user-tie me-2"></i>Create Account Manager</h5>
          <div class="text-muted small mt-1">Assign a user as an account manager using the same modern business modal style.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="accountManagerCreateForm" action="{{ route('account_managers.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Select the user who should own customer relationships as an account manager.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-user-tie"></i></span>
              <div>
                <div class="fault-modal-section-title">Manager Assignment</div>
                <div class="fault-modal-section-subtitle">Choose the user who should be available for account ownership.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-0">
                <label class="form-label">Account Manager</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                  <option value="" disabled selected>Select User</option>
                  @isset($users)
                    @foreach($users as $user)
                      <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                  @endisset
                </select>
                @error ('user_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
