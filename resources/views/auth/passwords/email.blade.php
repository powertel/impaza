@extends('layouts.app')

@section('content')
<style>
  :root {
    --brand-primary: #4C6FFF;
    --brand-primary-dark: #3a57cc;
    --brand-accent: #00A651;
    --bg-1: #eaf2ff;
    --bg-2: #f2f5ff;
    --gradient-a: #9fd0ff;
    --gradient-b: #c2b7ff;
    --gradient-c: #b6e0ff;
    --card-bg: #ffffff;
    --text-main: #0b1b2b;
    --text-muted: #6b7a8c;
    --border: #e6ebf2;
    --input-bg: #ffffff;
    --input-border: #dde3eb;
    --input-focus: #b9c7ff;
    --error: #e75050;
  }
  .login-box { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 6vh 6vw; }
  body.login-page { min-height: 100vh !important; background: linear-gradient(180deg, var(--bg-1) 0%, var(--bg-2) 100%); }
  .login-stage { position: relative; max-width: 1100px; margin: 0 auto; padding: 36px; border-radius: 24px; background: var(--bg-1); }
  .brand-top { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 18px; color: var(--text-main); font-weight: 800; letter-spacing: 0.3px; }
  .login-card { width: 100%; max-width: 440px; margin: 0 auto; background: var(--bg-1); border: 1px solid rgba(76,111,255,0.14); border-radius: 18px; box-shadow: 0 16px 28px rgba(25, 35, 53, 0.14); padding: 26px 26px 20px; overflow: hidden; }
  .form-header { text-align: center; margin-bottom: 14px; }
  .form-header h2 { margin: 0; font-size: 20px; font-weight: 800; color: var(--text-main); }
  .form-header .subtitle { margin: 6px 0 0; font-size: 13px; color: var(--text-muted); }
  .form-group { margin-bottom: 16px; }
  .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: var(--text-main); font-size: 13px; }
  .form-control.custom {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #c7d2e3;
    background: #ffffff;
    color: var(--text-main);
    box-shadow: 0 2px 6px rgba(17,23,34,0.06);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }
  .form-control.custom::placeholder { color: #7c8da1; }
  .form-control.custom:focus { outline: none; border-color: var(--brand-primary); box-shadow: 0 0 0 3px rgba(76,111,255,0.18), 0 2px 6px rgba(17,23,34,0.08); }
  .invalid-feedback { color: var(--error); }
  .btn-primary.modern {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 12px 16px; border: none; border-radius: 12px;
    font-weight: 700; letter-spacing: 0.2px; color: #ffffff;
    background: var(--brand-primary);
    box-shadow: 0 10px 20px rgba(76,111,255,0.30);
    transition: transform 0.06s ease, box-shadow 0.2s ease, filter 0.2s ease;
    backdrop-filter: blur(6px);
  }
  .btn-primary.modern:active { transform: translateY(1px); }
  @media (max-width: 720px) { .login-stage { padding: 22px; } .login-card { padding: 22px; } }
</style>

<div id="app" style="display:none"></div>
<div class="login-stage">
  <div class="brand-top">
    <img src="{{ asset('img/logo_sm.png') }}" alt="iMPAZAMON logo">
    <span>iMPAZAMON</span>
  </div>

  <div class="login-card">
    <div class="form-header">
      <h2>Reset Password</h2>
      <p class="subtitle">Enter your email to receive a password reset link.</p>
    </div>

    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" placeholder="Enter your Email" class="form-control custom @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
          <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
      </div>

      <button type="submit" class="btn btn-primary modern">Send Password Reset Link</button>
    </form>
  </div>
</div>
@endsection

