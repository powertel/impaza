<!-- Access (Enable/Disable) Modal -->
<div class="modal fade" id="accessUserModal-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="accessUserModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog  modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accessUserModalLabel-{{ $user->id }}"><i class="fas fa-user-lock me-2"></i>Account Access</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-access-form-{{ $user->id }}" action="{{ route('users.access', $user->id) }}" method="POST">
          @csrf
          @method('PATCH')
          <p class="mb-2">
            Current: <strong>{{ ((int)($user->is_access ?? 0) === 0) ? 'Enabled' : 'Disabled' }}</strong>
          </p>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="is_access" id="enableUser-{{ $user->id }}" value="0" {{ ((int)($user->is_access ?? 0) === 0) ? 'checked' : '' }}>
            <label class="form-check-label" for="enableUser-{{ $user->id }}">
              Enable (allow login)
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="is_access" id="disableUser-{{ $user->id }}" value="1" {{ ((int)($user->is_access ?? 0) === 1) ? 'checked' : '' }}>
            <label class="form-check-label" for="disableUser-{{ $user->id }}">
              Disable (block login)
            </label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" form="user-access-form-{{ $user->id }}" class="btn btn-outline-primary">
          <i class="fas fa-save me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>