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
  .footer-links { margin-top: 12px; text-align: center; font-size: 13px; color: var(--text-muted); }
  .footer-links a { color: #6a86ff; text-decoration: none; }
  .footer-links a:hover { text-decoration: underline; }
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
      <h2>Verify Your Email Address</h2>
      <p class="subtitle">Please check your email for a verification link.</p>
    </div>

    @if (session('resent'))
      <div class="alert alert-success" role="alert">
        {{ __('A fresh verification link has been sent to your email address.') }}
      </div>
    @endif

    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
      @csrf
      <button type="submit" class="btn btn-primary modern">{{ __('Resend Verification Email') }}</button>
    </form>

    <div class="footer-links">
      <p class="mt-3">{{ __('Alternatively, you can') }} <a href="{{ route('user.password.change') }}">{{ __('change your password') }}</a> {{ __('or go back to') }} <a href="{{ route('home') }}">{{ __('dashboard') }}</a>.</p>
    </div>
  </div>
</div>
@endsection
