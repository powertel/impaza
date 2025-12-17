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
                <div class="password-wrapper">
    <input id="newpassword" type="password" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" required autocomplete="new-password" placeholder="Enter new password" minlength="8" maxlength="30" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+">
                  <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="newpassword">
                    <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                    <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                  </button>
                </div>
                <div class="password-strength-meter"><div class="strength-bar"></div></div>
                <div class="password-strength-text"></div>
                <small class="text-muted">Minimum 8 characters with uppercase, lowercase, number, and special character.</small>
                @error('newpassword')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
              </div>
              <div class="mb-3">
                <label for="newpassword_confirmation" class="form-label">Confirm New Password</label>
                <div class="password-wrapper">
    <input id="newpassword_confirmation" type="password" class="form-control" name="newpassword_confirmation" required autocomplete="new-password" placeholder="Confirm new password" minlength="8" maxlength="30" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+">
                  <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="newpassword_confirmation">
                    <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                    <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/></svg>
                  </button>
                </div>
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
