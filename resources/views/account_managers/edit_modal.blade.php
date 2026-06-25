@can('account-manager-edit')
@foreach($account_managers as $acc_manager)
<div class="modal custom-modal fade" id="accountManagerEditModal{{ $acc_manager->id }}" tabindex="-1" aria-labelledby="accountManagerEditModalLabel{{ $acc_manager->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="accountManagerEditModalLabel{{ $acc_manager->id }}"><i class="fas fa-pen-to-square me-2"></i>Edit Account Manager</h5>
          <div class="text-muted small mt-1">Update the user assigned as the account manager for this ownership record.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('account_managers.update', $acc_manager->id ) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changing the assigned user updates who appears as the owner for linked customer accounts.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-user-tie"></i></span>
              <div>
                <div class="fault-modal-section-title">Manager Assignment</div>
                <div class="fault-modal-section-subtitle">Choose the user who should own this account manager record.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-0">
                <label class="form-label">Account Manager</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                  <option value="" disabled>Select User</option>
                  @isset($users)
                    @foreach($users as $user)
                      <option value="{{ $user->id }}" {{ (int)$acc_manager->user_id === (int)$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
@endforeach
@endcan
