@extends('layouts.admin')

@section('title')
Change Password
@endsection
@include('partials.css')
@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-transparent border-0 text-center">
            <h5 class="mb-0">Change Password</h5>
            <small class="text-muted">Enter and confirm your new password</small>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('user.password.update') }}">
              @csrf
              <div class="mb-3">
                <label for="newpassword" class="form-label">New Password</label>
                <input id="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" required autocomplete="new-password" placeholder="Enter new password">
                @error('newpassword')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
              <div class="mb-3">
                <label for="newpassword_confirmation" class="form-label">Confirm New Password</label>
                <input id="newpassword_confirmation" type="password" class="form-control" name="newpassword_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i>Update Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
