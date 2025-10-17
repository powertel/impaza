@can('account-manager-edit')
@foreach($account_managers as $acc_manager)
<div class="modal fade" id="accountManagerEditModal{{ $acc_manager->id }}" tabindex="-1" aria-labelledby="accountManagerEditModalLabel{{ $acc_manager->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accountManagerEditModalLabel{{ $acc_manager->id }}">Edit Account Manager</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('account_managers.update', $acc_manager->id ) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
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
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endcan
