@can('account-manager-create')
<div class="modal fade" id="accountManagerCreateModal" tabindex="-1" aria-labelledby="accountManagerCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accountManagerCreateModalLabel">Create Account Manager</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="accountManagerCreateForm" action="{{ route('account_managers.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
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
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
