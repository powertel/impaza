(function(){
  // Cross-version Bootstrap modal bridge: supports BS4 (jQuery plugin) and BS5 (bootstrap.Modal)
  function getModalElementFromTrigger(trigger){
    var targetSel = trigger.getAttribute('data-bs-target') || trigger.getAttribute('data-target') || trigger.getAttribute('href');
    if (!targetSel) return null;
    if (targetSel.indexOf('#') === 0) { return document.querySelector(targetSel); }
    return null;
  }

  function dispatchEvent(el, name){
    try {
      var evt = new Event(name, { bubbles: true });
      el.dispatchEvent(evt);
    } catch(_) {
      // IE fallback not required for our environment
    }
  }

  function ensureBackdrop(){
    if (document.querySelector('.modal-backdrop')) return;
    var backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(backdrop);
    document.body.classList.add('modal-open');
  }
  function removeBackdrop(){
    var backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) backdrop.remove();
    document.body.classList.remove('modal-open');
  }

  function showModal(el){
    if (!el) return;
    // BS4 jQuery plugin
    if (window.$ && typeof window.$(el).modal === 'function') {
      window.$(el).modal('show');
      return;
    }
    // BS5
    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
      window.bootstrap.Modal.getOrCreateInstance(el).show();
      return;
    }
    // Fallback toggling (ensures visibility even if no bootstrap JS available)
    el.classList.add('show');
    el.style.display = 'block';
    el.setAttribute('aria-modal', 'true');
    el.removeAttribute('aria-hidden');
    ensureBackdrop();
    dispatchEvent(el, 'shown.bs.modal');
  }

  function hideModal(el){
    if (!el) return;
    // BS4 jQuery plugin
    if (window.$ && typeof window.$(el).modal === 'function') {
      window.$(el).modal('hide');
      return;
    }
    // BS5
    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
      window.bootstrap.Modal.getOrCreateInstance(el).hide();
      return;
    }
    // Fallback toggling
    el.classList.remove('show');
    el.style.display = 'none';
    el.removeAttribute('aria-modal');
    el.setAttribute('aria-hidden','true');
    removeBackdrop();
    dispatchEvent(el, 'hidden.bs.modal');
  }

  // Bridge click handlers for open/close across BS4/BS5
  document.addEventListener('click', function(e){
    var trigger = e.target.closest('[data-bs-toggle="modal"], [data-toggle="modal"]');
    if (trigger) {
      var modalEl = getModalElementFromTrigger(trigger);
      if (modalEl) {
        e.preventDefault();
        showModal(modalEl);
      }
      return; // don't process dismiss for toggle click
    }
    var dismissEl = e.target.closest('[data-bs-dismiss="modal"], [data-dismiss="modal"]');
    if (dismissEl) {
      var modal = dismissEl.closest('.modal');
      if (modal) {
        e.preventDefault();
        hideModal(modal);
      }
    }
  }, true);

  // Make sure custom bindings listening for Bootstrap events still run in fallback
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.modal').forEach(function(modalEl){
      if (modalEl.dataset._bridgeBound === 'true') return;
      // Nothing to do here: the bridge dispatches shown/hidden events in fallback.
      modalEl.dataset._bridgeBound = 'true';
    });
  });
})();