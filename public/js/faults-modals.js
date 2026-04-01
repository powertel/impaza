(function(){
  document.addEventListener('DOMContentLoaded', function () {
    var modals = document.querySelectorAll('[id^="showFaultModal-"]');
    modals.forEach(function(modalEl){
      function handleShown(){
        var idSuffix = modalEl.id.replace('showFaultModal-', '');
        var scroller = document.getElementById('remarksScroller-' + idSuffix);
        if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
      }
      if (window.$ && typeof window.$.fn.on === 'function') {
        // Bootstrap 4 jQuery events or native events captured by jQuery
        window.$(modalEl).on('shown.bs.modal', handleShown);
      } else {
        // Bootstrap 5 native event fallback
        modalEl.addEventListener('shown.bs.modal', handleShown);
      }
    });
  });
})();