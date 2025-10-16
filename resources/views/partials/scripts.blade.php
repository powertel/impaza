

<script>
      $(document).ready(function () {
            
            $("img").click(function () {
                var img=$(this).attr('src'); 
            $("#show_it").attr('src',img);
                $('#PicModal').modal('show');
            });
        });
       
      
   
       </script>

<script type="text/javascript">

    $('.show_confirm').click(function(event) {
var form =  $(this).closest("form");
var name = $(this).data("name");
event.preventDefault();
new swal({
    title: `Are you sure you want to delete this record?`,
    text: "If you delete this, it will be gone forever.",
    icon: "warning",
    buttons: true,
    showCancelButton: true,
    dangerMode: true,}).then((willDelete) => {
  if (willDelete.isConfirmed) {
    form.submit();

    new swal('Deleted','','success')
  } 
   else{
    new swal('File not Deleted','','info')
    location.reload();
            } 

});});
</script>
<script>
    function inlineSave(){

       new swal({
            icon: 'success',
            title: 'Saved',
            showConfirmButton: false,
            timer: 1000
        })
       };
       </script>
<script>
     function submitResult(){
        event.preventDefault();
        new swal ({
                    title: 'Do you want to save the changes?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    denyButtonText: `Don't save`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '##3085d6'
        }).then((submitResult) => {
        if (submitResult.isConfirmed) {
             $('#UF').submit();
            swal.fire('Saved!',  '', 'success')
        } else if (submitResult.isDenied) {
            $('#UF').submit();
            swal.fire('Changes are not saved', '', 'info');
        }
        else{
            location.reload();
        }
        });
            }
        </script>
<script>
$('#city').on('change',function () {
        var CityID = $(this).val();
        if (CityID) {
            $.ajax({
                url : '/suburb/' +CityID,
                type: "GET",
                dataType: "json",
                success: function (res) {
                    if (res) {
                        $("#suburb").empty();
                        $("#pop").empty();
                        $("#suburb").append('<option  selected Disabled>Select Suburb</option>');
                        $("#pop").append('<option  selected Disabled>Select Pop</option>');
                        $.each(res, function (key, value) {
                            $("#suburb").append('<option value="' + key + '">' + value + '</option>');
                        });

                    } else {
                        $("#suburb").empty();
                    }
                }
            });
        } else {
            $("#suburb").empty();
            $("#city").empty();
        }
    });
    $('#suburb').on('change', function () {
        var suburbID = $(this).val();
        if (suburbID) {
            $.ajax({
                url : '/pop/' +suburbID,
                type: "GET",
                dataType: "json",
                success: function (res) {
                    if (res) {
                        $("#pop").empty();
                        $("#pop").append('<option  selected Disabled>Select Pop</option>');
                        $.each(res, function (key, value) {
                            $("#pop").append('<option value="' + key + '">' + value + '</option>');
                        });

                    } else {
                        $("#pop").empty();
                    }
                }
            });
        } else {
            $("#pop").empty();
        }
    });
</script>



<script type="text/javascript">
    $('#customer').on('change',function () {

    });
</script>


{{-- Departments repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('departmentRepeater');
    if (!repeater) return;
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addRepeaterItem');
    const removeBtn = document.getElementById('removeRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-12">
            <label class="form-label">Department</label>
            <input type="text" name="items[${idx}][department]" class="form-control" placeholder="e.g. Operations" required>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      itemsContainer.appendChild(createItem(index));
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
  });
</script>

{{-- Remarks chat submission (AJAX, keeps modal open) --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    async function postForm(url, form) {
      const fd = new FormData(form);
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: fd,
      });
      const text = await res.text();
      let json = {};
      try { json = JSON.parse(text); } catch (e) {}
      if (!res.ok) {
        console.error('Remark save failed', text);
        if (window.swal) { new swal('Save failed', 'Please try again', 'error'); }
      }
      return json;
    }

    function renderRemarkBubble(r) {
      const isSelf = (r?.name && r.name === (window.currentUserName || ''));
      const container = document.createElement('div');
      container.className = `chat-msg ${isSelf ? 'chat-msg-self' : 'chat-msg-other'}`;
      const created = r?.created_at ? new Date(r.created_at) : new Date();
      const meta = document.createElement('div');
      meta.className = 'chat-msg-meta';
      meta.innerHTML = `<strong>${r?.name || 'You'}</strong> <span class="text-muted">• ${created.toLocaleString()}</span>${r?.activity ? `<span class="ms-2 badge bg-light text-dark">${r.activity}</span>` : ''}`;
      const body = document.createElement('div');
      body.className = 'chat-msg-body';
      body.textContent = r?.remark || '';
      container.appendChild(meta);
      container.appendChild(body);
      if (r?.file_path) {
        const imgWrap = document.createElement('div');
        imgWrap.className = 'mt-2';
        const img = document.createElement('img');
        img.className = 'img-fluid rounded';
        img.style.height = '100px';
        img.style.width = 'auto';
        img.alt = 'Attachment';
        img.title = 'Attachment';
        img.src = `/storage/${r.file_path}`;
        imgWrap.appendChild(img);
        container.appendChild(imgWrap);
      }
      return container;
    }

    // Capture submit for all remark forms
    document.querySelectorAll('.js-remark-form').forEach(form => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const targetSel = form.dataset.remarksTarget;
        const list = targetSel ? document.querySelector(targetSel) : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Sending...'; }
        const url = form.getAttribute('action');
        const json = await postForm(url, form);
        if (json && json.status === 'ok' && json.remark) {
          const bubble = renderRemarkBubble(json.remark);
          if (list) {
            list.appendChild(bubble);
            list.scrollTop = list.scrollHeight;
          }
          // Reset inputs
          const ta = form.querySelector('textarea[name="remark"]');
          if (ta) ta.value = '';
          const file = form.querySelector('input[type="file"][name="attachment"]');
          if (file) file.value = '';
        }
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Send'; }
      });
    });
  });
</script>
{{-- Customers repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('customerRepeater');
    if (!repeater) return;
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addCustomerRepeaterItem');
    const removeBtn = document.getElementById('removeCustomerRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Customer</label>
            <input type="text" name="items[${idx}][customer]" class="form-control customer-name-input" placeholder="e.g. Acme Corp" required>
            <div class="invalid-feedback">This customer name already exists.</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Account Number</label>
            <input type="text" name="items[${idx}][account_number]" class="form-control account-number-input" placeholder="e.g. 123456789" required>
            <div class="invalid-feedback">This account number already exists.</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Account Manager</label>
            <select name="items[${idx}][account_manager_id]" class="form-select">
              <option value="">None</option>
              @isset($accountManagers)
                @foreach($accountManagers as $am)
                  <option value="{{ $am->user_id }}">{{ $am->name ?? ('User #'.$am->user_id) }}</option>
                @endforeach
              @endisset
            </select>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      const item = createItem(index);
      itemsContainer.appendChild(item);
      window.bindAccountNumberValidation?.(item);
      window.bindCustomerNameValidation?.(item);
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
  });
</script>

{{-- Links repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('linkRepeater');
    if (!repeater) return;
    if (repeater.dataset.initialized === '1') return; // guard against double-binding
    repeater.dataset.initialized = '1';
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addLinkRepeaterItem');
    const removeBtn = document.getElementById('removeLinkRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length;

    // Bind cascading City -> Location -> Pop inside repeater items
    function bindLinkCascades(scope) {
      const itemScopes = (scope && scope.querySelectorAll) ? scope.querySelectorAll('.repeater-item') : [];
      itemScopes.forEach(function(item){
        const citySel = item.querySelector('select[name*="[city_id]"]');
        const suburbSel = item.querySelector('select[name*="[suburb_id]"]');
        const popSel = item.querySelector('select[name*="[pop_id]"]');
        if (!citySel || !suburbSel || !popSel) return;

        // Avoid duplicate bindings
        if (citySel.dataset.bound === '1') return;
        citySel.dataset.bound = '1';
        suburbSel.dataset.bound = '1';

        citySel.addEventListener('change', function(){
          const cityId = this.value;
          $(suburbSel).empty().append('<option selected disabled>Select Location</option>');
          $(popSel).empty().append('<option selected disabled>Select Pop</option>');
          if (!cityId) return;
          $.ajax({
            url: '/suburb/' + cityId,
            type: 'GET',
            dataType: 'json',
            success: function(res){
              if (res) {
                $.each(res, function(key, value){
                  $(suburbSel).append('<option value="'+key+'">'+value+'</option>');
                });
              }
            }
          });
        });

        suburbSel.addEventListener('change', function(){
          const suburbId = this.value;
          $(popSel).empty().append('<option selected disabled>Select Pop</option>');
          if (!suburbId) return;
          $.ajax({
            url: '/pop/' + suburbId,
            type: 'GET',
            dataType: 'json',
            success: function(res){
              if (res) {
                $.each(res, function(key, value){
                  $(popSel).append('<option value="'+key+'">'+value+'</option>');
                });
              }
            }
          });
        });
      });
    }
    // expose globally for modal shown rebinds
    window.bindLinkCascades = bindLinkCascades;

    // Simple (non-repeater) cascading for edit forms and modals
    function bindSimpleLinkCascades(scope) {
      const root = scope || document;
      const citySel = root.querySelector('select[name="city_id"], #city');
      const suburbSel = root.querySelector('select[name="suburb_id"], #suburb');
      const popSel = root.querySelector('select[name="pop_id"], #pop');
      if (!citySel || !suburbSel || !popSel) return;

      if (citySel.dataset.simpleBound === '1') return; // avoid duplicates
      citySel.dataset.simpleBound = '1';
      suburbSel.dataset.simpleBound = '1';

      citySel.addEventListener('change', function(){
        const cityId = this.value;
        $(suburbSel).empty().append('<option selected disabled>Select Location</option>');
        $(popSel).empty().append('<option selected disabled>Select Pop</option>');
        if (!cityId) return;
        $.ajax({
          url: '/suburb/' + cityId,
          type: 'GET',
          dataType: 'json',
          success: function(res){
            if (res) {
              $.each(res, function(key, value){
                $(suburbSel).append('<option value="'+key+'">'+value+'</option>');
              });
            }
          }
        });
      });

      suburbSel.addEventListener('change', function(){
        const suburbId = this.value;
        $(popSel).empty().append('<option selected disabled>Select Pop</option>');
        if (!suburbId) return;
        $.ajax({
          url: '/pop/' + suburbId,
          type: 'GET',
          dataType: 'json',
          success: function(res){
            if (res) {
              $.each(res, function(key, value){
                $(popSel).append('<option value="'+key+'">'+value+'</option>');
              });
            }
          }
        });
      });
    }
    window.bindSimpleLinkCascades = bindSimpleLinkCascades;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3 position-relative';
      wrapper.innerHTML = `
        <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 remove-item-btn"><i class="fas fa-times"></i> </button>
        <div class="row g-3 align-items-end">
          <!-- Row 1: Link -->
          <div class="col-md-6">
            <label class="form-label">Link</label>
            <input type="text" name="items[${idx}][link]" class="form-control link-name-input" placeholder="e.g. HRE-ZB-Magetsi" required>
          </div>
          <div class="col-md-6 d-none d-md-block"></div>

          <!-- Row 2: JCC Number, Service Type, Capacity -->
          <div class="w-100"></div>
          <div class="col-md-4">
            <label class="form-label">JCC Number</label>
            <input type="text" name="items[${idx}][jcc_number]" class="form-control jcc-number-input" placeholder="e.g. JCC-12345">
            <div class="invalid-feedback">JCC number already exists.</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Service Type</label>
            <select name="items[${idx}][service_type]" class="form-select">
              <option value="" selected disabled>Select Service Type</option>
              <option value="Internet">Internet</option>
              <option value="VPN">VPN</option>
              <option value="Carrier Services">Carrier Services</option>
              <option value="E-Vending">E-Vending</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Capacity</label>
            <input type="text" name="items[${idx}][capacity]" class="form-control" placeholder="e.g. 100Mbps">
          </div>

          <!-- Row 3: City/Town, Location, Pop -->
          <div class="w-100"></div>
          <div class="col-md-3">
            <label class="form-label">City/Town</label>
            <select name="items[${idx}][city_id]" class="form-select" required>
              <option value="" disabled selected>Select City</option>
              ${Array.from(document.querySelectorAll('#linkCitiesTemplate option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Location</label>
            <select name="items[${idx}][suburb_id]" class="form-select" required>
              <option value="" disabled selected>Select Location</option>
              ${Array.from(document.querySelectorAll('#linkSuburbsTemplate option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Pop</label>
            <select name="items[${idx}][pop_id]" class="form-select" required>
              <option value="" disabled selected>Select Pop</option>
              ${Array.from(document.querySelectorAll('#linkPopsTemplate option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3 d-none d-md-block"></div>

          <!-- Row 4: Link Type -->
          <div class="w-100"></div>
          <div class="col-md-3">
            <label class="form-label">Link Type</label>
            <select name="items[${idx}][linkType_id]" class="form-select" required>
              <option value="" disabled selected>Select Type</option>
              ${Array.from(document.querySelectorAll('#linkTypesTemplate option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      const item = createItem(index);
      itemsContainer.appendChild(item);
      bindLinkCascades(itemsContainer);
      if (window.bindLinkNameValidation) window.bindLinkNameValidation(item);
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
    // Per-item remove support
    repeater.addEventListener('click', function(e){
      const btn = e.target.closest('.remove-item-btn');
      if (!btn) return;
      const item = btn.closest('.repeater-item');
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1 && item) {
        item.remove();
        index = itemsContainer.querySelectorAll('.repeater-item').length; // re-sync index
      }
    });
    // Initial bind for existing items
    bindLinkCascades(repeater);
  });
</script>
{{-- Account number uniqueness validation (create/edit modals and repeater) --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const accCheckUrl = "{{ route('customers.check-account-number') }}";
    const nameCheckUrl = "{{ route('customers.check-customer-name') }}";
    const linkCheckUrl = "{{ route('links.check-link-name') }}";
    const jccCheckUrl = "{{ route('links.check-jcc-number') }}";

    const debounceTimers = new WeakMap();

    function setSubmitDisabled(form, disabled) {
      const submitBtn = form?.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = !!disabled;
    }

    function refreshFormSubmitState(form) {
      const hasInvalidAcc = form.querySelectorAll('.account-number-input.is-invalid').length > 0;
      const hasInvalidName = form.querySelectorAll('.customer-name-input.is-invalid').length > 0;
      const hasInvalidLink = form.querySelectorAll('.link-name-input.is-invalid').length > 0;
      const hasInvalidJcc = form.querySelectorAll('.jcc-number-input.is-invalid').length > 0;
      setSubmitDisabled(form, hasInvalidAcc || hasInvalidName || hasInvalidLink || hasInvalidJcc);
    }

    function validateInput(input) {
      const value = (input.value || '').trim();
      const form = input.closest('form');
      const ignoreId = input.dataset.ignoreId || '';
      if (value === '') {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        refreshFormSubmitState(form);
        return;
      }

      $.get(accCheckUrl, { account_number: value, ignore_id: ignoreId })
        .done(function(res) {
          if (res && res.available === true) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.remove('d-block');
          } else {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.add('d-block');
          }
          refreshFormSubmitState(form);
        })
        .fail(function() {
          // On error, do not block user; clear validity
          input.classList.remove('is-invalid');
          input.classList.remove('is-valid');
          const fb = input.parentElement.querySelector('.invalid-feedback');
          if (fb) fb.classList.remove('d-block');
          refreshFormSubmitState(form);
        });
    }

    function bindValidationToInput(input) {
      function debouncedValidate() {
        const existing = debounceTimers.get(input);
        if (existing) clearTimeout(existing);
        const t = setTimeout(() => validateInput(input), 350);
        debounceTimers.set(input, t);
      }
      input.addEventListener('input', debouncedValidate);
      input.addEventListener('blur', () => validateInput(input));
    }

    window.bindAccountNumberValidation = function(root) {
      const scope = root || document;
      const inputs = scope.querySelectorAll('.account-number-input');
      inputs.forEach(bindValidationToInput);
    };

    // Customer name validation
    function validateNameInput(input) {
      const value = (input.value || '').trim();
      const form = input.closest('form');
      const ignoreId = input.dataset.ignoreId || '';
      if (value === '') {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        refreshFormSubmitState(form);
        return;
      }

      $.get(nameCheckUrl, { customer: value, ignore_id: ignoreId })
        .done(function(res) {
          if (res && res.available === true) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.remove('d-block');
          } else {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.add('d-block');
          }
          refreshFormSubmitState(form);
        })
        .fail(function() {
          input.classList.remove('is-invalid');
          input.classList.remove('is-valid');
          const fb = input.parentElement.querySelector('.invalid-feedback');
          if (fb) fb.classList.remove('d-block');
          refreshFormSubmitState(form);
        });
    }

    function bindNameValidationToInput(input) {
      function debouncedValidate() {
        const existing = debounceTimers.get(input);
        if (existing) clearTimeout(existing);
        const t = setTimeout(() => validateNameInput(input), 350);
        debounceTimers.set(input, t);
      }
      input.addEventListener('input', debouncedValidate);
      input.addEventListener('blur', () => validateNameInput(input));
    }

    window.bindCustomerNameValidation = function(root) {
      const scope = root || document;
      const inputs = scope.querySelectorAll('.customer-name-input');
      inputs.forEach(bindNameValidationToInput);
    };

    // Link name validation
    function validateLinkInput(input) {
      const value = (input.value || '').trim();
      const form = input.closest('form');
      const ignoreId = input.dataset.ignoreId || '';
      if (value === '') {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        refreshFormSubmitState(form);
        return;
      }

      $.get(linkCheckUrl, { link: value, ignore_id: ignoreId })
        .done(function(res) {
          if (res && res.available === true) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.remove('d-block');
          } else {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.add('d-block');
          }
          refreshFormSubmitState(form);
        })
        .fail(function() {
          input.classList.remove('is-invalid');
          input.classList.remove('is-valid');
          const fb = input.parentElement.querySelector('.invalid-feedback');
          if (fb) fb.classList.remove('d-block');
          refreshFormSubmitState(form);
        });
    }

    function bindLinkValidationToInput(input) {
      function debouncedValidate() {
        const existing = debounceTimers.get(input);
        if (existing) clearTimeout(existing);
        const t = setTimeout(() => validateLinkInput(input), 350);
        debounceTimers.set(input, t);
      }
      input.addEventListener('input', debouncedValidate);
      input.addEventListener('blur', () => validateLinkInput(input));
    }

    window.bindLinkNameValidation = function(root) {
      const scope = root || document;
      const inputs = scope.querySelectorAll('.link-name-input');
      inputs.forEach(bindLinkValidationToInput);
    };

    // JCC number validation (optional field)
    function validateJccInput(input) {
      const value = (input.value || '').trim();
      const form = input.closest('form');
      const ignoreId = input.dataset.ignoreId || '';
      if (value === '') {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        refreshFormSubmitState(form);
        return;
      }

      $.get(jccCheckUrl, { jcc_number: value, ignore_id: ignoreId })
        .done(function(res) {
          if (res && res.available === true) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.remove('d-block');
          } else {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.classList.add('d-block');
          }
          refreshFormSubmitState(form);
        })
        .fail(function() {
          input.classList.remove('is-invalid');
          input.classList.remove('is-valid');
          const fb = input.parentElement.querySelector('.invalid-feedback');
          if (fb) fb.classList.remove('d-block');
          refreshFormSubmitState(form);
        });
    }

    function bindJccValidationToInput(input) {
      function debouncedValidate() {
        const existing = debounceTimers.get(input);
        if (existing) clearTimeout(existing);
        const t = setTimeout(() => validateJccInput(input), 350);
        debounceTimers.set(input, t);
      }
      input.addEventListener('input', debouncedValidate);
      input.addEventListener('blur', () => validateJccInput(input));
    }

    window.bindJccNumberValidation = function(root) {
      const scope = root || document;
      const inputs = scope.querySelectorAll('.jcc-number-input');
      inputs.forEach(bindJccValidationToInput);
    };

    // Bind on load for existing inputs (create & edit modals)
    window.bindAccountNumberValidation(document);
    window.bindCustomerNameValidation(document);
    window.bindLinkNameValidation(document);
    window.bindJccNumberValidation && window.bindJccNumberValidation(document);

    // Also re-bind when a Bootstrap modal is shown, to make sure dynamic content is wired
    document.querySelectorAll('.modal').forEach(function(modalEl) {
      modalEl.addEventListener('shown.bs.modal', function() {
        window.bindAccountNumberValidation(modalEl);
        window.bindCustomerNameValidation(modalEl);
        window.bindLinkNameValidation(modalEl);
        if (window.bindJccNumberValidation) window.bindJccNumberValidation(modalEl);
        if (window.bindLinkCascades) window.bindLinkCascades(modalEl);
        if (window.bindSimpleLinkCascades) window.bindSimpleLinkCascades(modalEl);
      });
    });

    // Final guard: check on submit and block if any are taken
    function checkAvailabilityValue(value, ignoreId) {
      return new Promise(function(resolve) {
        $.get(accCheckUrl, { account_number: value, ignore_id: ignoreId })
          .done(function(res){
            resolve(!!(res && res.available === true));
          })
          .fail(function(){
            // Fail-closed: treat as not available to prevent reload and keep modal open
            resolve(false);
          });
      });
    }

    function checkNameAvailabilityValue(value, ignoreId) {
      return new Promise(function(resolve) {
        $.get(nameCheckUrl, { customer: value, ignore_id: ignoreId })
          .done(function(res){
            resolve(!!(res && res.available === true));
          })
          .fail(function(){
            resolve(false);
          });
      });
    }

    function checkLinkAvailabilityValue(value, ignoreId) {
      return new Promise(function(resolve) {
        $.get(linkCheckUrl, { link: value, ignore_id: ignoreId })
          .done(function(res){
            resolve(!!(res && res.available === true));
          })
          .fail(function(){
            resolve(false);
          });
      });
    }

    function checkJccAvailabilityValue(value, ignoreId) {
      return new Promise(function(resolve) {
        $.get(jccCheckUrl, { jcc_number: value, ignore_id: ignoreId })
          .done(function(res){
            resolve(!!(res && res.available === true));
          })
          .fail(function(){
            resolve(false);
          });
      });
    }

    function handleFormSubmit(e) {
      const form = e.target;
      // First use native HTML5 validation
      if (!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity();
        return;
      }

      const accInputs = form.querySelectorAll('.account-number-input');
      const nameInputs = form.querySelectorAll('.customer-name-input');
      const linkInputs = form.querySelectorAll('.link-name-input');
      const jccInputs = form.querySelectorAll('.jcc-number-input');
      if (!accInputs.length && !nameInputs.length && !linkInputs.length && !jccInputs.length) {
        // No relevant fields; let native submission proceed
        return;
      }

      e.preventDefault();
      setSubmitDisabled(form, true);

      const checks = [
        ...Array.from(accInputs).map(function(input){
          const value = (input.value || '').trim();
          const ignoreId = input.dataset.ignoreId || '';
          if (!value) return Promise.resolve(true);
          return checkAvailabilityValue(value, ignoreId).then(function(available){
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (available) {
              input.classList.remove('is-invalid');
              input.classList.add('is-valid');
              if (fb) fb.classList.remove('d-block');
            } else {
              input.classList.add('is-invalid');
              input.classList.remove('is-valid');
              if (fb) fb.classList.add('d-block');
            }
            return available;
          });
        }),
        ...Array.from(nameInputs).map(function(input){
          const value = (input.value || '').trim();
          const ignoreId = input.dataset.ignoreId || '';
          if (!value) return Promise.resolve(true);
          return checkNameAvailabilityValue(value, ignoreId).then(function(available){
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (available) {
              input.classList.remove('is-invalid');
              input.classList.add('is-valid');
              if (fb) fb.classList.remove('d-block');
            } else {
              input.classList.add('is-invalid');
              input.classList.remove('is-valid');
              if (fb) fb.classList.add('d-block');
            }
            return available;
          });
        }),
        ...Array.from(linkInputs).map(function(input){
          const value = (input.value || '').trim();
          const ignoreId = input.dataset.ignoreId || '';
          if (!value) return Promise.resolve(true);
          return checkLinkAvailabilityValue(value, ignoreId).then(function(available){
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (available) {
              input.classList.remove('is-invalid');
              input.classList.add('is-valid');
              if (fb) fb.classList.remove('d-block');
            } else {
              input.classList.add('is-invalid');
              input.classList.remove('is-valid');
              if (fb) fb.classList.add('d-block');
            }
            return available;
          });
        }),
        ...Array.from(jccInputs).map(function(input){
          const value = (input.value || '').trim();
          const ignoreId = input.dataset.ignoreId || '';
          if (!value) return Promise.resolve(true); // optional field
          return checkJccAvailabilityValue(value, ignoreId).then(function(available){
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (available) {
              input.classList.remove('is-invalid');
              input.classList.add('is-valid');
              if (fb) fb.classList.remove('d-block');
            } else {
              input.classList.add('is-invalid');
              input.classList.remove('is-valid');
              if (fb) fb.classList.add('d-block');
            }
            return available;
          });
        })
      ];

      Promise.all(checks).then(function(results){
        const anyTaken = results.some(function(r){ return r === false; }) ||
          form.querySelectorAll('.account-number-input.is-invalid').length > 0 ||
          form.querySelectorAll('.customer-name-input.is-invalid').length > 0 ||
          form.querySelectorAll('.link-name-input.is-invalid').length > 0 ||
          form.querySelectorAll('.jcc-number-input.is-invalid').length > 0;
        if (anyTaken) {
          setSubmitDisabled(form, false); // keep enabled to allow corrections
          // Focus the first invalid field
          const firstInvalid = form.querySelector('.account-number-input.is-invalid, .customer-name-input.is-invalid, .link-name-input.is-invalid, .jcc-number-input.is-invalid');
          if (firstInvalid) firstInvalid.focus();
          return; // do not submit
        }
        // Re-check native validity before final submit
        if (!form.checkValidity()) {
          form.reportValidity();
          setSubmitDisabled(form, false);
          return;
        }
        form.submit();
      }).catch(function(){
        // If endpoint fails, keep form open and allow corrections
        setSubmitDisabled(form, false);
        const firstInvalid = form.querySelector('.customer-name-input.is-invalid, .account-number-input.is-invalid, .link-name-input.is-invalid, .jcc-number-input.is-invalid')
          || form.querySelector('.customer-name-input, .account-number-input, .link-name-input, .jcc-number-input');
      if (firstInvalid) firstInvalid.focus();
      // Do not submit on failure of availability check
    });
    }

    document.querySelectorAll('.modal form').forEach(function(form){
      form.addEventListener('submit', handleFormSubmit);
    });
  });
</script>
{{-- Sections repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('sectionRepeater');
    if (!repeater) return;
    if (repeater.dataset.bound === 'true') return; // guard against duplicate bindings
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addSectionRepeaterItem');
    const removeBtn = document.getElementById('removeSectionRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-12">
            <label class="form-label">Section</label>
            <input type="text" name="items[${idx}][section]" class="form-control" placeholder="e.g. Network Ops" required>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      itemsContainer.appendChild(createItem(index));
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
    repeater.dataset.bound = 'true';
  });
</script>
{{-- Cities repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('cityRepeater');
    if (!repeater) return;
    if (repeater.dataset.bound === 'true') return; // guard against duplicate bindings
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addCityRepeaterItem');
    const removeBtn = document.getElementById('removeCityRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label">City/Town</label>
            <input type="text" name="items[${idx}][city]" class="form-control" placeholder="e.g. Harare" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Region</label>
            <select name="items[${idx}][region]" class="form-select" required>
              <option value="" disabled selected>Select Region</option>
              <option value="North">North</option>
              <option value="West">West</option>
              <option value="East">East</option>
              <option value="South">South</option>
            </select>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      itemsContainer.appendChild(createItem(index));
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
    repeater.dataset.bound = 'true';
  });
</script>
{{-- Locations repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('locationRepeater');
    if (!repeater) return;
    if (repeater.dataset.bound === 'true') return; // guard against duplicate bindings
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addLocationRepeaterItem');
    const removeBtn = document.getElementById('removeLocationRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-md-12">
            <label class="form-label">Location</label>
            <input type="text" name="items[${idx}][suburb]" class="form-control" placeholder="Location" required>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      itemsContainer.appendChild(createItem(index));
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
    repeater.dataset.bound = 'true';
  });
</script>
{{-- POPs create modal helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const citySel = document.getElementById('popCreateCity');
    const suburbSel = document.getElementById('popCreateSuburb');
    const repeater = document.querySelector('.js-repeater-pops');
    const list = repeater ? repeater.querySelector('.js-repeater-list') : null;
    const addBtn = document.querySelector('.js-repeater-add');

    // Filter suburb options by selected city (using data-city attribute)
    function filterSuburbs() {
      if (!citySel || !suburbSel) return;
      const cityId = citySel.value;
      const options = suburbSel.querySelectorAll('option');
      let hasVisible = false;
      options.forEach(function(opt){
        const c = opt.getAttribute('data-city');
        if (!c) return; // skip placeholder
        const visible = String(c) === String(cityId);
        opt.style.display = visible ? '' : 'none';
        if (visible) hasVisible = true;
      });
      // Reset selection if current selection is hidden
      if (suburbSel.value) {
        const selectedOpt = suburbSel.querySelector('option[value="'+suburbSel.value+'"]');
        if (selectedOpt && selectedOpt.style.display === 'none') {
          suburbSel.value = '';
        }
      }
      // If no visible options, ensure placeholder is selected
      if (!hasVisible) {
        suburbSel.value = '';
      }
    }

    citySel?.addEventListener('change', filterSuburbs);
    // Initialize filter on load if a city is preselected
    if (citySel && citySel.value) { filterSuburbs(); }

    // Simple repeater: add/remove POP inputs
    let index = list ? list.querySelectorAll('.js-repeater-item').length : 0;
    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'list-group-item d-flex align-items-center gap-2 js-repeater-item';
      wrapper.innerHTML = `
        <div class="flex-grow-1">
          <input type="text" class="form-control" name="items[${idx}][pop]" placeholder="Pop name" required>
        </div>
        <button type="button" class="btn btn-sm btn-danger js-repeater-remove" title="Remove">&times;</button>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function(){
      if (!list) return;
      const item = createItem(index);
      list.appendChild(item);
      index += 1;
    });

    list?.addEventListener('click', function(e){
      const btn = e.target.closest('.js-repeater-remove');
      if (!btn) return;
      const item = btn.closest('.js-repeater-item');
      if (!item) return;
      // keep at least one row
      const items = list.querySelectorAll('.js-repeater-item');
      if (items.length > 1) {
        item.remove();
      }
    });

    // Validate city & suburb selected before submit
    const createForm = document.querySelector('.js-pops-create-form');
    createForm?.addEventListener('submit', function(e){
      if (!citySel?.value || !suburbSel?.value) {
        e.preventDefault();
        alert('Please select City/Town and Location first.');
      }
    });

    // Re-apply filter when modal opens (ensure visibility state correct)
    const popCreateModal = document.getElementById('popCreateModal');
    popCreateModal?.addEventListener('shown.bs.modal', function(){
      filterSuburbs();
    });
  });
</script>
{{-- Location view modal helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.js-location-view-modal');
    modals.forEach(function(modal){
      if (modal.dataset.bound === 'true') return;
      modal.addEventListener('shown.bs.modal', function(ev) {
        const suburbId = modal.getAttribute('data-suburb-id');
        const tbody = modal.querySelector(`#viewPopsBody${suburbId}`);
        if (!tbody) return;
        // Show loading state
        tbody.innerHTML = `<tr><td colspan="2" class="text-muted">Loading POPs...</td></tr>`;
        // Fetch pops for this suburb (location)
        $.ajax({
          url: `/pop/${suburbId}`,
          type: 'GET',
          dataType: 'json',
          success: function(res){
            const entries = Object.entries(res || {});
            if (!entries.length) {
              tbody.innerHTML = `<tr><td colspan="2" class="text-muted">No POPs found for this location.</td></tr>`;
              return;
            }
            let i = 0;
            tbody.innerHTML = entries.map(function([id, name]){ i++; return `<tr><td>${i}</td><td>${name}</td></tr>`; }).join('');
          },
          error: function(){
            tbody.innerHTML = `<tr><td colspan="2" class="text-danger">Failed to load POPs.</td></tr>`;
          }
        });
      });
      modal.dataset.bound = 'true';
    });
  });
</script>
{{-- Positions repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('positionRepeater');
    if (!repeater) return;
    if (repeater.dataset.bound === 'true') return; // guard against duplicate bindings
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addPositionRepeaterItem');
    const removeBtn = document.getElementById('removePositionRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-12">
            <label class="form-label">Position</label>
            <input type="text" name="items[${idx}][position]" class="form-control" placeholder="e.g. Senior Engineer" required>
          </div>
        </div>
      `;
      return wrapper;
    }

    addBtn?.addEventListener('click', function() {
      index += 1;
      itemsContainer.appendChild(createItem(index));
    });

    removeBtn?.addEventListener('click', function() {
      const items = itemsContainer.querySelectorAll('.repeater-item');
      if (items.length > 1) {
        items[items.length - 1].remove();
        index -= 1;
      }
    });
    repeater.dataset.bound = 'true';
  });
</script>

