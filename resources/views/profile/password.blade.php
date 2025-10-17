@extends('layouts.admin')

@section('title')
Change Password
@endsection
@include('partials.css')
@section('content')
<style>
  :root {
    --brand-primary: #4C6FFF;
    --brand-primary-dark: #3a57cc;
    --bg-1: #eaf2ff;
    --text-main: #0b1b2b;
    --text-muted: #6b7a8c;
    --error: #e75050;
  }
  .login-stage { position: relative; max-width: 900px; margin: 0 auto; padding: 24px; border-radius: 18px; background: var(--bg-1); }
  .login-card { width: 100%; max-width: 520px; margin: 0 auto; background: var(--bg-1); border: 1px solid rgba(76,111,255,0.14); border-radius: 16px; box-shadow: 0 16px 28px rgba(25, 35, 53, 0.14); padding: 24px; }
  .form-header { text-align: center; margin-bottom: 12px; }
  .form-header h2 { margin: 0; font-size: 20px; font-weight: 800; color: var(--text-main); }
  .form-header .subtitle { margin: 6px 0 0; font-size: 13px; color: var(--text-muted); }
  .form-group { margin-bottom: 16px; }
  .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: var(--text-main); font-size: 13px; }
  .form-control.custom {
    width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #c7d2e3; background: #ffffff; color: var(--text-main);
    box-shadow: 0 2px 6px rgba(17,23,34,0.06); transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }
  .form-control.custom::placeholder { color: #7c8da1; }
  .form-control.custom:focus { outline: none; border-color: var(--brand-primary); box-shadow: 0 0 0 3px rgba(76,111,255,0.18), 0 2px 6px rgba(17,23,34,0.08); }
  .invalid-feedback { color: var(--error); }
  .btn-primary.modern {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 12px 16px; border: none; border-radius: 12px;
    font-weight: 700; letter-spacing: 0.2px; color: #ffffff; background: var(--brand-primary);
    box-shadow: 0 10px 20px rgba(76,111,255,0.30); transition: transform 0.06s ease, box-shadow 0.2s ease, filter 0.2s ease;
  }
  .btn-primary.modern:active { transform: translateY(1px); }
</style>
<section class="content">
  <div class="login-stage">
    <div class="login-card">
      <div class="form-header">
        <h2>Change Password</h2>
        <p class="subtitle">Please enter and confirm your new password.</p>
      </div>
      <form method="POST" action="{{ route('user.password.update') }}">
        @csrf
        <div class="form-group">
          <label for="newpassword" class="form-label">New Password</label>
          <input id="newpassword" type="password" placeholder="Enter new password" class="form-control custom @error('newpassword') is-invalid @enderror" name="newpassword" required autocomplete="new-password">
          @error('newpassword')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>
        <div class="form-group">
          <label for="newpassword_confirmation" class="form-label">Confirm New Password</label>
          <input id="newpassword_confirmation" type="password" placeholder="Confirm new password" class="form-control custom" name="newpassword_confirmation" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary modern">Update Password</button>
      </form>
    </div>
  </div>
</section>
@endsection
