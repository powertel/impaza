@php
  $flash = null;

  if (Session::has('success')) {
      $flash = ['type' => 'success', 'message' => Session::get('success'), 'title' => 'Success'];
  } elseif (Session::has('error')) {
      $flash = ['type' => 'error', 'message' => Session::get('error'), 'title' => 'Error'];
  } elseif (Session::has('warning')) {
      $flash = ['type' => 'warning', 'message' => Session::get('warning'), 'title' => 'Warning'];
  } elseif (Session::has('info')) {
      $flash = ['type' => 'info', 'message' => Session::get('info'), 'title' => 'Info'];
  }
@endphp

<style>
  .impaza-swal-toast {
    border-radius: 18px !important;
    border: 1px solid rgba(226, 232, 240, 0.95) !important;
    box-shadow: 0 18px 48px rgba(15, 23, 42, 0.16) !important;
    padding: 0.9rem 1rem !important;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px);
  }

  .impaza-swal-toast .swal2-title {
    margin: 0 !important;
    font-size: 0.9rem !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
  }

  .impaza-swal-toast .swal2-html-container {
    margin: 0.2rem 0 0 !important;
    font-size: 0.78rem !important;
    line-height: 1.45 !important;
    color: #475569 !important;
  }

  .impaza-swal-toast .swal2-close {
    color: #94a3b8 !important;
    font-size: 1rem !important;
  }

  .impaza-swal-toast .swal2-timer-progress-bar {
    background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%) !important;
    height: 3px !important;
  }

  .impaza-swal-toast.swal2-icon-success {
    border-left: 4px solid #22c55e !important;
  }

  .impaza-swal-toast.swal2-icon-error {
    border-left: 4px solid #ef4444 !important;
  }

  .impaza-swal-toast.swal2-icon-warning {
    border-left: 4px solid #f59e0b !important;
  }

  .impaza-swal-toast.swal2-icon-info,
  .impaza-swal-toast.swal2-icon-question {
    border-left: 4px solid #3b82f6 !important;
  }

  html[data-theme="dark"] .impaza-swal-toast {
    background: rgba(15, 23, 42, 0.98) !important;
    border-color: rgba(51, 65, 85, 0.92) !important;
    box-shadow: 0 18px 48px rgba(2, 6, 23, 0.45) !important;
  }

  html[data-theme="dark"] .impaza-swal-toast .swal2-title {
    color: #e2e8f0 !important;
  }

  html[data-theme="dark"] .impaza-swal-toast .swal2-html-container {
    color: #cbd5e1 !important;
  }

  html[data-theme="dark"] .impaza-swal-toast .swal2-close {
    color: #94a3b8 !important;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const flash = @json($flash);

    if (!flash || !window.swal) return;

    if (typeof window.swal.fire === 'function') {
      window.swal.fire({
        toast: true,
        position: 'top-end',
        icon: flash.type,
        title: flash.title,
        text: flash.message,
        showConfirmButton: false,
        timer: 3200,
        timerProgressBar: true,
        showCloseButton: true,
        customClass: {
          popup: 'impaza-swal-toast',
        },
      });
      return;
    }

    if (typeof window.swal === 'function') {
      window.swal({
        title: flash.title,
        text: flash.message,
        icon: flash.type,
        button: false,
        timer: 3200,
      });
    }
  });
</script>
