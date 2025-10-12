@extends('layouts.app')

@section('title')
Login
@endsection

@section('content')

<style>
  :root {
    --brand-primary: #4C6FFF; /* primary accent for actions */
    --brand-primary-dark: #3a57cc;
    --brand-accent: #00A651; /* subtle green accent */
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

  /* override layout wrapper to allow full-width stage */
  .login-box { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 6vh 6vw; }

  body.login-page {
    min-height: 100vh !important;
    background: linear-gradient(180deg, var(--bg-1) 0%, var(--bg-2) 100%);
  }

  .login-stage {
    position: relative;
    max-width: 1100px;
    margin: 0 auto;
    padding: 36px;
    border-radius: 24px;
    background: var(--bg-1);

  }
  .login-stage::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 24px;

    pointer-events: none;
  }

  .brand-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 18px;
    color: var(--text-main);
    font-weight: 800;
    letter-spacing: 0.3px;
  }
  .brand-top img { width: 28px; height: 28px; object-fit: contain; border-radius: 6px; }

  .login-card {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
    background: var(--bg-1);
    border: 1px solid rgba(76,111,255,0.14);
    border-radius: 18px;
    box-shadow: 0 16px 28px rgba(25, 35, 53, 0.14);
    padding: 26px 26px 20px;
    overflow: hidden; /* ensures corners render cleanly (fixes bottom-left) */
  }

  .form-header { text-align: center; margin-bottom: 14px; }
  .form-header h2 { margin: 0; font-size: 20px; font-weight: 800; color: var(--text-main); }
  .form-header .subtitle { margin: 6px 0 0; font-size: 13px; color: var(--text-muted); }

  .form-group { margin-bottom: 16px; }
  .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: var(--text-main); font-size: 13px; }
  .form-control.custom {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #c7d2e3; /* stronger contrast */
    background: #ffffff; /* solid white for visibility */
    color: var(--text-main);
    box-shadow: 0 2px 6px rgba(17,23,34,0.06); /* subtle elevation */
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  }
  .form-control.custom::placeholder { color: #7c8da1; }
  .form-control.custom:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 3px rgba(76,111,255,0.18), 0 2px 6px rgba(17,23,34,0.08);
    background: #ffffff;
  }
  .invalid-feedback { color: var(--error); }

  .row-actions { display: flex; align-items: center; justify-content: space-between; margin: 8px 0 16px; }
  .remember { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); }
  /* Align and style checkbox to match other inputs */
  .remember .form-check-input {
    width: 18px;
    height: 18px;
    margin: 0; /* remove default top offset */
    border: 1px solid rgba(221,227,235,0.9);
    border-radius: 4px;
    background: rgba(255,255,255,0.92);
    accent-color: var(--brand-primary); /* modern browsers use brand color */
    box-shadow: inset 0 1px 2px rgba(17,23,34,0.06);
  }
  .remember .form-check-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(76,111,255,0.18);
  }
  .forgot-block { margin: 8px 0 16px; }
  .forgot-link { font-size: 13px; color: #6a86ff; text-decoration: none; }
  .forgot-link:hover { text-decoration: underline; }

  .btn-primary.modern {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 12px 16px; border: none; border-radius: 12px;
    font-weight: 700; letter-spacing: 0.2px; color: #ffffff;
    background: var(--brand-primary);
    box-shadow: 0 10px 20px rgba(76,111,255,0.30);
    transition: transform 0.06s ease, box-shadow 0.2s ease, filter 0.2s ease;
    backdrop-filter: blur(6px);
  }
  .btn-primary.modern:hover { filter: brightness(1.05); box-shadow: 0 14px 26px rgba(76,111,255,0.42); }
  .btn-primary.modern:active { transform: translateY(1px); }

  .oauth {
    margin-top: 12px;
  }
  .btn-google {
    width: 100%;
    background: #ffffff;
    color: var(--text-main);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-weight: 600;
    box-shadow: 0 8px 18px rgba(17,23,34,0.06);
  }
  .btn-google:hover { box-shadow: 0 10px 20px rgba(17,23,34,0.08); }
  .btn-google svg { width: 18px; height: 18px; }

  .footer-links { margin-top: 12px; text-align: center; font-size: 13px; color: var(--text-muted); }
  .footer-links a { color: #6a86ff; text-decoration: none; }
  .footer-links a:hover { text-decoration: underline; }

  @media (max-width: 720px) {
    .login-stage { padding: 22px; }
    .login-card { padding: 22px; }
  }
</style>

<div id="app" style="display:none"></div>
<div class="login-stage">
  <div class="brand-top">
    <img src="{{ asset('img/logo_sm.png') }}" alt="iMPAZAMON logo">
    <span>iMPAZAMON</span>
  </div>

  <div class="login-card">
    <div class="form-header">
      <h2>Welcome Back!</h2>
      <p class="subtitle">We missed you! Please enter your details.</p>
    </div>

    <form action="{{ route('login') }}" method="post">
      @csrf

      <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" placeholder="Enter your Email" class="form-control custom @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
          <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" placeholder="Enter Password" class="form-control custom @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
        @error('password')
          <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
      </div>

      <div class="forgot-block">
        @if (Route::has('password.request'))
          <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
      </div>

      <button type="submit" class="btn btn-primary modern">Sign in</button>

    </form>
  </div>
</div>

@endsection
