<script>
  // Global cascade binding used by link repeater and this modal
  window.bindLinkCascades = function(root){
    const scope = root || document;
    const citySelects = scope.querySelectorAll('.city-sel, select[name$="[city_id]"], select[name="city_id"]');

    function getSuburbSelect(container){
      return container.querySelector('.suburb-sel') || container.querySelector('select[name$="[suburb_id]"]') || container.querySelector('select[name="suburb_id"]');
    }
    function getPopSelect(container){
      return container.querySelector('.pop-sel') || container.querySelector('select[name$="[pop_id]"]') || container.querySelector('select[name="pop_id"]');
    }

    citySelects.forEach(function(citySel){
      if (citySel.dataset.cascadeBound === '1') return;
      citySel.dataset.cascadeBound = '1';
      citySel.addEventListener('change', function(){
        const container = citySel.closest('.repeater-item') || citySel.closest('tr') || citySel.closest('.row') || scope;
        const suburbSel = getSuburbSelect(container);
        const popSel = getPopSelect(container);
        const cityId = citySel.value;
        if (!suburbSel || !popSel) return;
        $(suburbSel).empty().append('<option selected disabled>Select Location</option>');
        $(popSel).empty().append('<option selected disabled>Select Pop</option>');
        if (!cityId) return;
        $.get(`/suburb/${cityId}`, function(res){
          $.each(res, function(key, value){ $(suburbSel).append(`<option value="${key}">${value}</option>`); });
        });
      });
    });

    const suburbSelects = scope.querySelectorAll('.suburb-sel, select[name$="[suburb_id]"], select[name="suburb_id"]');
    suburbSelects.forEach(function(suburbSel){
      if (suburbSel.dataset.cascadeBound === '1') return;
      suburbSel.dataset.cascadeBound = '1';
      suburbSel.addEventListener('change', function(){
        const container = suburbSel.closest('.repeater-item') || suburbSel.closest('tr') || suburbSel.closest('.row') || scope;
        const popSel = getPopSelect(container);
        const suburbId = suburbSel.value;
        if (!popSel) return;
        $(popSel).empty().append('<option selected disabled>Select Pop</option>');
        if (!suburbId) return;
        $.get(`/pop/${suburbId}`, function(res){
          $.each(res, function(key, value){ $(popSel).append(`<option value="${key}">${value}</option>`); });
        });
      });
    });
  };

  // Modal-specific behavior: load links, edit and autosave
  document.addEventListener('DOMContentLoaded', function(){
    const modal = document.getElementById('editExistingLinksModal');
    if (!modal) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrf && window.$) {
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });
    }

    const customerSel = document.getElementById('editLinksCustomer');
    const tbody = document.getElementById('editExistingLinksBody');

    function cityOptionsHtml(){
      return Array.from(document.querySelectorAll('#editLinksCitiesTpl option'))
        .map(o => `<option value="${o.value}">${o.text}</option>`).join('');
    }
    function typeOptionsHtml(){
      return Array.from(document.querySelectorAll('#editLinksLinkTypesTpl option'))
        .map(o => `<option value="${o.value}">${o.text}</option>`).join('');
    }

    function renderRow(item){
      const tr = document.createElement('tr');
      tr.dataset.linkId = item.id;
      tr.innerHTML = `
        <td>
          <input type="text" class="form-control form-control-sm link-name-input" value="${item.link || ''}" data-ignore-id="${item.id}" />
          <div class="invalid-feedback">Link name already exists.</div>
        </td>
        <td>
          <select class="form-select form-select-sm city-sel">
            <option value="" disabled>Select City</option>
            ${cityOptionsHtml()}
          </select>
        </td>
        <td>
          <select class="form-select form-select-sm suburb-sel"><option value="" disabled>Select Location</option></select>
        </td>
        <td>
          <select class="form-select form-select-sm pop-sel"><option value="" disabled>Select Pop</option></select>
        </td>
        <td>
          <select class="form-select form-select-sm type-sel">
            <option value="" disabled>Select Type</option>
            ${typeOptionsHtml()}
          </select>
        </td>
        <td>
          <span class="save-status text-muted small">—</span>
        </td>
      `;
      const citySel = tr.querySelector('.city-sel');
      const suburbSel = tr.querySelector('.suburb-sel');
      const popSel = tr.querySelector('.pop-sel');
      const typeSel = tr.querySelector('.type-sel');
      if (item.city_id) citySel.value = String(item.city_id);
      if (item.linkType_id) typeSel.value = String(item.linkType_id);

      if (item.city_id) {
        $.get(`/suburb/${item.city_id}`, function(res){
          $(suburbSel).empty().append('<option selected disabled>Select Location</option>');
          $.each(res, function(key, value){ $(suburbSel).append(`<option value="${key}">${value}</option>`); });
          if (item.suburb_id) suburbSel.value = String(item.suburb_id);
          if (item.suburb_id) {
            $.get(`/pop/${item.suburb_id}`, function(res){
              $(popSel).empty().append('<option selected disabled>Select Pop</option>');
              $.each(res, function(key, value){ $(popSel).append(`<option value="${key}">${value}</option>`); });
              if (item.pop_id) popSel.value = String(item.pop_id);
            });
          }
        });
      }

      bindRowEvents(tr);
      return tr;
    }

    function setStatus(tr, msg){
      const el = tr.querySelector('.save-status');
      if (el) { el.textContent = msg; }
    }

    function autosave(tr, payload){
      const id = tr.dataset.linkId;
      setStatus(tr, 'Saving…');
      $.post(`/links/${id}/autosave`, payload)
        .done(() => setStatus(tr, 'Saved'))
        .fail(() => setStatus(tr, 'Error'));
    }

    function bindRowEvents(tr){
      const citySel = tr.querySelector('.city-sel');
      const suburbSel = tr.querySelector('.suburb-sel');
      const popSel = tr.querySelector('.pop-sel');
      const typeSel = tr.querySelector('.type-sel');
      const linkInput = tr.querySelector('.link-name-input');

      citySel.addEventListener('change', function(){
        const cityId = this.value;
        $(suburbSel).empty().append('<option selected disabled>Select Location</option>');
        $(popSel).empty().append('<option selected disabled>Select Pop</option>');
        if (!cityId) return;
        $.get(`/suburb/${cityId}`, function(res){
          $.each(res, function(key, value){ $(suburbSel).append(`<option value="${key}">${value}</option>`); });
        });
        autosave(tr, { city_id: cityId });
      });

      suburbSel.addEventListener('change', function(){
        const suburbId = this.value;
        $(popSel).empty().append('<option selected disabled>Select Pop</option>');
        if (!suburbId) return;
        $.get(`/pop/${suburbId}`, function(res){
          $.each(res, function(key, value){ $(popSel).append(`<option value="${key}">${value}</option>`); });
        });
        autosave(tr, { suburb_id: suburbId });
      });

      popSel.addEventListener('change', function(){
        const popId = this.value;
        autosave(tr, { pop_id: popId });
      });

      typeSel.addEventListener('change', function(){
        const typeId = this.value;
        autosave(tr, { linkType_id: typeId });
      });

      const debounceTimers = new WeakMap();
      function debounce(input, fn, delay=300){
        const prev = debounceTimers.get(input);
        if (prev) clearTimeout(prev);
        const t = setTimeout(fn, delay);
        debounceTimers.set(input, t);
      }
      linkInput.addEventListener('input', function(){
        const val = this.value.trim();
        const ignoreId = tr.dataset.linkId;
        debounce(this, function(){
          if (!val) return;
          $.get(`{{ route('links.check-link-name') }}`, { link: val, ignore_id: ignoreId })
            .done(function(res){
              if (!res.available) {
                linkInput.classList.add('is-invalid');
                setStatus(tr, 'Name exists');
              } else {
                linkInput.classList.remove('is-invalid');
                autosave(tr, { link: val });
              }
            })
            .fail(function(){ setStatus(tr, 'Error'); });
        }, 400);
      });
    }

    customerSel.addEventListener('change', function(){
      const custId = this.value;
      $(tbody).empty().append('<tr><td colspan="6" class="text-center text-muted">Loading…</td></tr>');
      $.get(`{{ url('links/customer') }}/${custId}`, function(items){
        $(tbody).empty();
        if (!items || items.length === 0) {
          $(tbody).append('<tr><td colspan="6" class="text-center text-muted">No links for this customer.</td></tr>');
          return;
        }
        items.forEach(function(item){ tbody.appendChild(renderRow(item)); });
        // Ensure cascades bind for the newly rendered rows
        window.bindLinkCascades && window.bindLinkCascades(tbody);
      });
    });
  });
</script>