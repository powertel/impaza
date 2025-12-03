@extends('layouts.admin')

@section('title')
Profile
@endsection
@include('partials.css')
@section('content')

<section class="content">
  <div class="container-fluid">
    @php($user = auth()->user())
    <div class="row justify-content-center">
      <div class="col-lg-4 col-md-5 mb-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
          <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
            <h5 class="mb-0">My Profile</h5>
            <!-- @if ($user)
              <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fas fa-key me-1"></i> Change
              </button>
            @endif -->
          </div>
          <div class="card-body">
            <div class="text-center mb-3">
              <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:80px; height:80px; font-size:1.6rem;">
                {{ optional($user)->name ? strtoupper(substr($user->name,0,1)) : 'U' }}
              </div>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted">Name</small>
                  <div class="fw-semibold">{{ optional($user)->name ?? 'Guest' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted">Email</small>
                  <div class="fw-semibold">{{ optional($user)->email ?? '—' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted">Phone</small>
                  <div class="fw-semibold">{{ optional($user)->phonenumber ?? '—' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                @php($dept = optional($user)->department_id ? optional(\App\Models\Department::find($user->department_id))->department : null)
                <div>
                  <small class="text-muted">Department</small>
                  <div class="fw-semibold">{{ $dept ?? '—' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                @php($section = optional($user)->section_id ? optional(\App\Models\Section::find($user->section_id))->section : null)
                <div>
                  <small class="text-muted">Section</small>
                  <div class="fw-semibold">{{ $section ?? '—' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                @php($position = optional($user)->position_id ? optional(\App\Models\Position::find($user->position_id))->position : null)
                <div>
                  <small class="text-muted">Position</small>
                  <div class="fw-semibold">{{ $position ?? '—' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="w-100">
                  <small class="text-muted">Roles</small>
                  <div>
                    @if ($user && $user->getRoleNames())
                      @foreach($user->getRoleNames() as $role)
                        <span class="badge bg-success me-1">{{ $role }}</span>
                      @endforeach
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted">Region</small>
                  <div class="fw-semibold">{{ optional($user)->region ?? '—' }}</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted">Standby</small>
                  <div>
                    <span class="badge {{ $user && $user->weekly_standby ? 'bg-primary' : 'bg-secondary' }} me-2">Weekly: {{ $user && $user->weekly_standby ? 'Yes' : 'No' }}</span>
                    <span class="badge {{ $user && $user->weekend_standby ? 'bg-primary' : 'bg-secondary' }}">Weekend: {{ $user && $user->weekend_standby ? 'Yes' : 'No' }}</span>
                  </div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <small class="text-muted">Email Verified</small>
                  <div class="fw-semibold">{{ optional($user)->email_verified_at ? optional($user)->email_verified_at->format('Y-m-d H:i') : 'Not verified' }}</div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-8 col-md-7">
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-transparent border-0">
            <h5 class="mb-0"><i class="fas fa-user-edit me-1"></i>Edit Profile</h5>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('user.postProfile') }}">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ optional($user)->name }}" required placeholder="Name">
                  @error('name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="text" name="phonenumber" id="phone" class="form-control @error('phonenumber') is-invalid @enderror" placeholder="e.g 263786533333" value="{{ optional($user)->phonenumber }}" required>
                  @error('phonenumber')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" id="email" class="form-control" value="{{ optional($user)->email }}" disabled>
                </div>
                <div class="col-md-6">
                  <label for="department" class="form-label">Department</label>
                  <input type="text" id="department" class="form-control" value="{{ $dept ?? '—' }}" disabled>
                </div>
                <div class="col-md-6">
                  <label for="section" class="form-label">Section</label>
                  <input type="text" id="section" class="form-control" value="{{ $section ?? '—' }}" disabled>
                </div>
                <div class="col-md-6">
                  <label for="position" class="form-label">Position</label>
                  <input type="text" id="position" class="form-control" value="{{ $position ?? '—' }}" disabled>
                </div>
              </div>
              <div class="d-flex justify-content-start align-items-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update Profile</button>
                @if ($user)
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                  <i class="fas fa-key me-1"></i>Change Password
                </button>
                @endif
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordLabel"><i class="fas fa-key me-1"></i>Change Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('user.password.update') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="newpassword" class="form-label">New Password</label>
              <input id="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" required autocomplete="new-password" placeholder="Enter new password">
              @error('newpassword')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="mb-3">
              <label for="newpassword_confirmation" class="form-label">Confirm New Password</label>
              <input id="newpassword_confirmation" type="password" class="form-control @error('newpassword_confirmation') is-invalid @enderror" name="newpassword_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
              @error('newpassword_confirmation')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
