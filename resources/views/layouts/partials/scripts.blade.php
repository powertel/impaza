<script>
(function(){
  function initPaginatedTable(table){
    if(!table || table.dataset.paginatedInit === 'true') return;
    table.dataset.paginatedInit = 'true';
    var pageSizeControlSel = table.getAttribute('data-page-size-control');
    var pageSizeControl = pageSizeControlSel ? document.querySelector(pageSizeControlSel) : null;
    var pageSize = parseInt(table.getAttribute('data-page-size')||'20',10);
    var pagerSel = table.getAttribute('data-pager');
    var searchSel = table.getAttribute('data-search');
    var pager = pagerSel ? document.querySelector(pagerSel) : null;
    var searchInput = searchSel ? document.querySelector(searchSel) : null;

    var tbody = table.tBodies[0];
    if(!tbody) return;
    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
    var filtered = rows.slice();
    var current = 1;

    function render(){
      rows.forEach(function(r){ r.style.display = 'none'; });
      var start = (current-1)*pageSize;
      var pageRows = filtered.slice(start, start+pageSize);
      pageRows.forEach(function(r){ r.style.display = ''; });
      renderPager();
    }

    function renderPager(){
      if(!pager) return;
      var totalPages = Math.ceil(filtered.length / pageSize) || 1;
      current = Math.max(1, Math.min(current, totalPages));
      pager.innerHTML = '';
      var wrapper = document.createElement('div');
      wrapper.className = 'd-flex align-items-center justify-content-between';

      var info = document.createElement('div');
      info.className = 'text-muted';
      info.textContent = 'Showing ' + ((filtered.length===0)?0:((current-1)*pageSize+1)) + ' to ' + Math.min(current*pageSize, filtered.length) + ' of ' + filtered.length + ' entries';

      var ul = document.createElement('ul');
      ul.className = 'pagination pagination-sm mb-0';

      function addItem(label, disabled, active, handler){
        var li = document.createElement('li');
        li.className = 'page-item' + (disabled? ' disabled':'') + (active? ' active':'');
        var a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = label;
        a.addEventListener('click', function(e){ e.preventDefault(); if(!disabled){ handler(); }});
        li.appendChild(a);
        ul.appendChild(li);
      }

      addItem('«', current===1, false, function(){ current = 1; render(); });
      addItem('‹', current===1, false, function(){ current = Math.max(1, current-1); render(); });

      // Page numbers (compact after 10 pages)
      var maxPagesToShow = 10;
      var totalPages = Math.ceil(filtered.length / pageSize) || 1;
      for(var p = 1; p <= totalPages; p++){
        if(p <= maxPagesToShow || p === totalPages){
          (function(pageNum){
            addItem(String(pageNum), false, current===pageNum, function(){ current = pageNum; render(); });
          })(p);
        } else if(p === maxPagesToShow + 1){
          var ell = document.createElement('li'); ell.className = 'page-item disabled'; var a = document.createElement('a'); a.className='page-link'; a.textContent='…'; ell.appendChild(a); ul.appendChild(ell);
        }
      }

      addItem('›', current===totalPages, false, function(){ current = Math.min(totalPages, current+1); render(); });
      addItem('»', current===totalPages, false, function(){ current = totalPages; render(); });

      wrapper.appendChild(info);
      wrapper.appendChild(ul);
      pager.appendChild(wrapper);
    }

    function applyFilter(){
      if(!searchInput){ filtered = rows.slice(); current = 1; render(); return; }
      var q = (searchInput.value||'').toLowerCase();
      filtered = rows.filter(function(r){ return r.textContent.toLowerCase().indexOf(q) !== -1; });
      current = 1;
      render();
    }

    if(searchInput && !searchInput.dataset.filterBound){
      searchInput.dataset.filterBound = 'true';
      searchInput.addEventListener('input', applyFilter);
    }

    if(pageSizeControl && !pageSizeControl.dataset.pageSizeBound){
      pageSizeControl.dataset.pageSizeBound = 'true';
      pageSizeControl.addEventListener('change', function(){
        var val = pageSizeControl.value;
        if(val === 'all') {
          pageSize = filtered.length || rows.length || 1;
        } else {
          var n = parseInt(val,10);
          pageSize = isNaN(n) ? 20 : Math.max(1, n);
        }
        current = 1;
        render();
      });
    }

    render();
  }

  function initAll(){
    var tables = document.querySelectorAll('table.js-paginated-table');
    Array.prototype.forEach.call(tables, initPaginatedTable);
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
</script>