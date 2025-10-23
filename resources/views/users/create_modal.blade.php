<!-- Create User Modal -->
<div class="modal custom-modal fade" id="createUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel"><i class="fas fa-user-plus me-2"></i>Create User</h5>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="user-create-form" action="{{ route('users.store') }}" method="POST">
          {{ csrf_field() }}
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name" value="{{ old('name') }}">
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}">
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <select class="form-select department-select @error('department_id') is-invalid @enderror" name="department_id">
                <option selected disabled>Select Department</option>
                @foreach($department as $depart)
                  @if (old('department_id')==$depart->id)
                    <option value="{{ $depart->id}}" selected>{{ $depart->department }}</option>
                  @else
                    <option value="{{ $depart->id}}">{{ $depart->department }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select class="form-select section-select @error('section_id') is-invalid @enderror" name="section_id">
                <option selected disabled>Select Section</option>
                @foreach($section as $sect)
                  @if (old('section_id')==$sect->id)
                    <option value="{{ $sect->id}}" selected>{{ $sect->section }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <select class="form-select position-select @error('position_id') is-invalid @enderror" name="position_id">
                <option selected disabled>Select Position</option>
                @foreach($position as $pos)
                  @if (old('position_id')==$pos->id)
                    <option value="{{ $pos->id}}" selected>{{ $pos->position }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              {!! Form::select('roles[]', $roles, [], ['class' => 'form-select']) !!}
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select @error('user_status') is-invalid @enderror" name="user_status">
                <option selected disabled>Select Status</option>
                @foreach($user_statuses as $status)
                  @if (old('user_status')==$status->id)
                    <option value="{{ $status->id}}" selected>{{ $status->status_name }}</option>
                  @else
                    <option value="{{ $status->id}}">{{ $status->status_name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Region</label>
              <select class="form-select @error('region') is-invalid @enderror" name="region">
                <option value="" disabled {{ old('region', $currentUserRegion ?? '') ? '' : 'selected' }}>Select Region</option>
                @isset($regions)
                  @foreach($regions as $region)
                    <option value="{{ $region }}" {{ old('region', $currentUserRegion ?? '') === $region ? 'selected' : '' }}>{{ $region }}</option>
                  @endforeach
                @endisset
              </select>
              <small class="text-muted">Defaults to your region: {{ $currentUserRegion ?? 'Not set' }}</small>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control @error('phonenumber') is-invalid @enderror" name="phonenumber" placeholder="e.g. 263776123456" value="{{ old('phonenumber') }}">
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
        <button type="submit" form="user-create-form" class="btn btn-primary"><i class="fas fa-save"></i>Save</button>
      </div>
    </div>
  </div>
</div>
