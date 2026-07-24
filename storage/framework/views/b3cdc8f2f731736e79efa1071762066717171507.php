

<?php $__env->startSection('title'); ?>
Login
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

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

  /* override layout wrapper to center the auth stage in the viewport */
  .login-box {
    width: 100% !important;
    max-width: none !important;
    min-height: 100vh;
    margin: 0 !important;
    padding: 32px 24px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  body.login-page {
    min-height: 100vh !important;
    background: linear-gradient(180deg, var(--bg-1) 0%, var(--bg-2) 100%);
  }

  .login-stage {
    position: relative;
    width: min(100%, 940px);
    min-height: min(520px, calc(100vh - 64px));
    margin: 0;
    padding: 36px;
    border-radius: 24px;
    background: var(--bg-1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
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
  .brand-top img { width: auto; max-width: 180px; height: auto; object-fit: contain; }

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

  .form-group { margin-bottom: 12px; }
  .form-label { display: block; margin-bottom: 4px; font-weight: 600; color: var(--text-main); font-size: 12px; }
  .form-control.custom {
    width: 100%;
    min-height: 38px;
    padding: 7px 12px;
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

  /* show/hide password toggle */
  .password-wrapper { position: relative; }
  .form-control.custom.password { padding-right: 36px; }
  .toggle-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    color: var(--text-muted);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .toggle-password:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(76,111,255,0.18);
    border-radius: 8px;
  }
  .toggle-password svg { width: 18px; height: 18px; }
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
    width: 100%; min-height: 38px; padding: 8px 16px; border: none; border-radius: 12px;
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

  .footer-links { margin-top: 10px; text-align: center; font-size: 12px; color: var(--text-muted); }
  .footer-links a { color: #6a86ff; text-decoration: none; }
  .footer-links a:hover { text-decoration: underline; }

  @media (max-width: 720px) {
    .login-box { padding: 18px; }
    .login-stage {
      min-height: auto;
      padding: 22px;
    }
    .login-card { padding: 22px; }
  }
</style>

<div id="app" style="display:none"></div>
<div class="login-stage">
  <div class="brand-top">
    <img src="<?php echo e(asset('img/impazamon-v2.png')); ?>" alt="Logo">
  </div>

  <div class="login-card">
    <div class="form-header">
      <h2>Welcome Back!</h2>
      <p class="subtitle">We missed you! Please enter your details.</p>
    </div>

    <form action="<?php echo e(route('login')); ?>" method="post">
      <?php echo csrf_field(); ?>

      <div class="form-group">
        <label for="email" class="form-label">Username or Email</label>
        <input id="email" type="text" placeholder="Enter Username or Email" class="form-control custom <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="username" autofocus>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="password-wrapper">
          <input id="password" type="password" placeholder="Enter Password" class="form-control custom password <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required autocomplete="current-password">
          <button type="button" class="toggle-password" aria-label="Show password" data-toggle-target="password">
            <svg class="eye-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 5c-5 0-9 7-9 7s4 7 9 7 9-7 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/>
            </svg>
            <svg class="eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="display:none">
              <path d="M3 3l18 18-1.5 1.5L16.7 20C14.9 20.6 13.5 21 12 21c-5 0-9-7-9-7a20.8 20.8 0 014.8-5.8L1.5 4.5 3 3zm7.9 7.9a3 3 0 004.1 4.1l-4.1-4.1zM12 3c5 0 9 7 9 7a20.8 20.8 0 01-3.5 4.5l-1.5-1.5A18.8 18.8 0 0019 10s-4-7-7-7c-1.2 0-2.4.3-3.5.8L6.7 2.5A10.8 10.8 0 0112 3z"/>
            </svg>
          </button>
        </div>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <span class="invalid-feedback" role="alert"><strong><?php echo e($message); ?></strong></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <!-- <div class="forgot-block">
        <?php if(Route::has('password.request')): ?>
          <a class="forgot-link" href="<?php echo e(route('password.request')); ?>">Forgot password?</a>
        <?php endif; ?>
      </div> -->

      <button type="submit" class="btn btn-primary modern">Sign in</button>

    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggle = document.querySelector('.toggle-password');
  if (!toggle) return;
  const inputId = toggle.getAttribute('data-toggle-target') || 'password';
  const input = document.getElementById(inputId);
  if (!input) return;
  const eyeOn = toggle.querySelector('.eye-on');
  const eyeOff = toggle.querySelector('.eye-off');
  toggle.addEventListener('click', function() {
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    if (eyeOn && eyeOff) {
      eyeOn.style.display = showing ? '' : 'none';
      eyeOff.style.display = showing ? 'none' : '';
    }
  });
});
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/auth/login.blade.php ENDPATH**/ ?>