<!-- Edit User Modal -->
<div class="modal custom-modal fade" id="editUserModal-{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel-{{ $user->id }}"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-edit-form-{{ $user->id }}" action="{{ route('users.update', $user->id ) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <select class="form-select department-select @error('department_id') is-invalid @enderror" name="department_id" data-selected="{{ $user->department_id }}">
                @foreach($department as $depart)
                  <option value="{{ $depart->id}}" {{ (int)$user->department_id === (int)$depart->id ? 'selected' : '' }}>{{ $depart->department }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select class="form-select section-select @error('section_id') is-invalid @enderror" name="section_id" data-selected="{{ $user->section_id }}">
                @isset($user->section_id)
                  <option selected value="{{ $user->section_id }}">{{ $user->section }}</option>
                @endisset
              </select>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <select class="form-select position-select @error('position_id') is-invalid @enderror" name="position_id" data-selected="{{ $user->position_id }}">
                @isset($user->position_id)
                  <option selected value="{{ $user->position_id }}">{{ $user->position }}</option>
                @endisset
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              {!! Form::select('roles[]', $roles, $user->getRoleNames(), ['class' => 'form-select']) !!}
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" name="user_status">
                @isset($user->user_status)
                  <option selected value="{{ $user->user_status }}">{{ $user->status_name ?? 'Current Status' }}</option>
                @endisset
                @foreach($user_statuses as $status)
                  @if (!isset($user->user_status) || $status->id !== $user->user_status)
                    <option value="{{ $status->id}}">{{ $status->status_name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Region</label>
              <select class="form-select @error('region') is-invalid @enderror" name="region">
                <option value="" {{ empty($user->region) ? 'selected' : '' }}>Not set</option>
                @isset($regions)
                  @foreach($regions as $region)
                    <option value="{{ $region }}" {{ $user->region === $region ? 'selected' : '' }}>{{ $region }}</option>
                  @endforeach
                @endisset
              </select>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password" placeholder="Confirm Password">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="user-edit-form-{{ $user->id }}" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
