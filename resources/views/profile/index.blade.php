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
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">My Profile</h4>
            <!-- @if ($user)
              <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#changePasswordModal">
                <i class="fas fa-key"></i> Change Password
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
              <li class="list-group-item">
                <span class="text-muted">Name</span>
                <div class="font-weight-bold">{{ optional($user)->name ?? 'Guest' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Email</span>
                <div class="font-weight-bold">{{ optional($user)->email ?? '—' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Phone</span>
                <div class="font-weight-bold">{{ optional($user)->phonenumber ?? '—' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Department</span>
                @php($dept = optional($user)->department_id ? optional(\App\Models\Department::find($user->department_id))->department : null)
                <div class="font-weight-bold">{{ $dept ?? '—' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Section</span>
                @php($section = optional($user)->section_id ? optional(\App\Models\Section::find($user->section_id))->section : null)
                <div class="font-weight-bold">{{ $section ?? '—' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Position</span>
                @php($position = optional($user)->position_id ? optional(\App\Models\Position::find($user->position_id))->position : null)
                <div class="font-weight-bold">{{ $position ?? '—' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Roles</span>
                <div>
                  @if ($user && $user->getRoleNames())
                    @foreach($user->getRoleNames() as $role)
                      <span class="badge badge-success mr-1">{{ $role }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Region</span>
                <div class="font-weight-bold">{{ optional($user)->region ?? '—' }}</div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Standby</span>
                <div>
                  <span class="badge badge-{{ $user && $user->weekly_standby ? 'primary' : 'secondary' }} mr-2">Weekly: {{ $user && $user->weekly_standby ? 'Yes' : 'No' }}</span>
                  <span class="badge badge-{{ $user && $user->weekend_standby ? 'primary' : 'secondary' }}">Weekend: {{ $user && $user->weekend_standby ? 'Yes' : 'No' }}</span>
                </div>
              </li>
              <li class="list-group-item">
                <span class="text-muted">Email Verified</span>
                <div class="font-weight-bold">{{ optional($user)->email_verified_at ? optional($user)->email_verified_at->format('Y-m-d H:i') : 'Not verified' }}</div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit Profile</h4>
          </div>
          <div class="card-body">
            <form class="form-horizontal" method="POST" action="{{ route('user.postProfile') }}">
              @csrf
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="name">Name</label>
                  <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ optional($user)->name }}" required placeholder="Name">
                  @error('name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
                <div class="form-group col-md-6">
                  <label for="phone">Phone Number</label>
                  <input type="text" name="phonenumber" id="phone" class="form-control @error('phonenumber') is-invalid @enderror" placeholder="Phone Number" value="{{ optional($user)->phonenumber }}" required>
                  @error('phonenumber')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="email">Email</label>
                  <input type="email" id="email" class="form-control" value="{{ optional($user)->email }}" disabled>
                </div>
                <div class="form-group col-md-6">
                  <label for="department">Department</label>
                  <input type="text" id="department" class="form-control" value="{{ $dept ?? '—' }}" disabled>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="section">Section</label>
                  <input type="text" id="section" class="form-control" value="{{ $section ?? '—' }}" disabled>
                </div>
                <div class="form-group col-md-6">
                  <label for="position">Position</label>
                  <input type="text" id="position" class="form-control" value="{{ $position ?? '—' }}" disabled>
                </div>
              </div>
              <div class="card-footer bg-white px-0">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Profile</button>
                @if ($user)
                <button type="button" class="btn btn-outline-secondary ml-2" data-toggle="modal" data-target="#changePasswordModal">
                  <i class="fas fa-key"></i> Change Password
                </button>
                @endif
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordLabel"><i class="fas fa-key"></i> Change Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="{{ route('user.password.update') }}">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label for="newpassword">New Password</label>
              <input id="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" required autocomplete="new-password" placeholder="Enter new password">
              @error('newpassword')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
            <div class="form-group">
              <label for="newpassword_confirmation">Confirm New Password</label>
              <input id="newpassword_confirmation" type="password" class="form-control @error('newpassword_confirmation') is-invalid @enderror" name="newpassword_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
              @error('newpassword_confirmation')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Update Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection