
<!-- Change Password Modal -->
<div class="modal custom-modal fade" id="changePasswordModal-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePasswordModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel-{{ $user->id }}"><i class="fas fa-key me-2"></i>Change Password</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-change-password-form-{{ $user->id }}" action="{{ route('users.change-password', $user->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">New Password</label>
              <input type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" required minlength="6" maxlength="30">
              @error('newpassword')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12">
              <label class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" name="newpassword_confirmation" required minlength="6" maxlength="30">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" form="user-change-password-form-{{ $user->id }}" class="btn btn-outline-warning">
          <i class="fas fa-save me-1"></i> Update Password
        </button>
      </div>
    </div>
  </div>
</div>
