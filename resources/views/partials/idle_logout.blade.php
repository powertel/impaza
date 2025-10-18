<script>
(function() {
  // Idle timeout in milliseconds (1 minute for testing)
  var IDLE_TIMEOUT_MS = 30 * 60 * 1000; // change to 30 * 60 * 1000 for 30 minutes
  var lastActivity = Date.now();
  var logoutTriggered = false;

  function resetTimer() {
    lastActivity = Date.now();
  }

  // Events that indicate activity
  ['mousemove','mousedown','keydown','touchstart','scroll'].forEach(function(evt) {
    window.addEventListener(evt, resetTimer, { passive: true });
  });

  // Cross-tab logout broadcast
  window.addEventListener('storage', function(e) {
    if (e.key === 'impaza_force_logout') {
      // Another tab triggered a logout
      redirectToLogin();
    }
  });

  function getCsrfToken() {
    var el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  }

  function redirectToLogin() {
    if (logoutTriggered) return;
    logoutTriggered = true;
    try {
      window.location.href = '/login';
    } catch (e) {
      window.location.reload();
    }
  }

  function triggerLogout() {
    if (logoutTriggered) return;
    logoutTriggered = true;

    // Broadcast to other tabs
    try { localStorage.setItem('impaza_force_logout', String(Date.now())); } catch (e) {}

    // Attempt server logout via POST /logout
    var csrf = getCsrfToken();
    fetch('/logout', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      }
    }).finally(function() {
      // Regardless of request result, send user to login
      redirectToLogin();
    });
  }

  // Periodic check for inactivity
  setInterval(function() {
    var inactive = Date.now() - lastActivity;
    if (inactive >= IDLE_TIMEOUT_MS) {
      triggerLogout();
    }
  }, 5000); // check every 5s
})();
</script>