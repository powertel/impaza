<script>
  (function(){
    function init(scope){
      if (typeof $ === 'undefined' || !$.fn || !$.fn.select2) return;
      const elements = (scope instanceof Element ? scope : document).querySelectorAll('.select2, .js-select2');
      elements.forEach(function(el){
        const modal = el.closest('.modal');
        const opts = {
          width: '100%',
          dropdownParent: modal ? $(modal) : $(document.body),
          placeholder: el.getAttribute('data-placeholder') || '',
          allowClear: el.hasAttribute('data-allow-clear') ? (el.getAttribute('data-allow-clear') !== '0') : true
        };
        if ($(el).data('select2')) { $(el).select2('destroy'); }
        $(el).select2(opts);
      });
    }

    document.addEventListener('DOMContentLoaded', function(){ init(document); });

    document.querySelectorAll('.modal').forEach(function(m){
      m.addEventListener('shown.bs.modal', function(){ init(m); });
      m.addEventListener('hidden.bs.modal', function(){
        if (typeof $ === 'undefined' || !$.fn || !$.fn.select2) return;
        m.querySelectorAll('.select2, .js-select2').forEach(function(el){ if ($(el).data('select2')) { $(el).select2('destroy'); } });
      });
    });

    const mo = new MutationObserver(function(muts){
      muts.forEach(function(mut){
        mut.addedNodes.forEach(function(n){
          if (n.nodeType === 1){
            if (n.classList.contains('modal')){ init(n); }
            const sel = n.querySelectorAll ? n.querySelectorAll('.select2, .js-select2') : [];
            if (sel.length) init(n);
          }
        });
      });
    });
    mo.observe(document.body, { childList: true, subtree: true });
  })();
</script><?php /**PATH /var/www/html/resources/views/partials/select2.blade.php ENDPATH**/ ?>