

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

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3 position-relative';
      wrapper.innerHTML = `
        <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 mt-2 me-2 remove-item-btn"><i class="fas fa-times"></i> </button>
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">City/Town</label>
            <select name="items[${idx}][city_id]" class="form-select" required>
              <option value="" disabled selected>Select City</option>
              ${Array.from(document.querySelectorAll('#createLinkModal select[name="items\\[0\\]\[city_id\]"] option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Location</label>
            <select name="items[${idx}][suburb_id]" class="form-select" required>
              <option value="" disabled selected>Select Location</option>
              ${Array.from(document.querySelectorAll('#createLinkModal select[name="items\\[0\\]\[suburb_id\]"] option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Pop</label>
            <select name="items[${idx}][pop_id]" class="form-select" required>
              <option value="" disabled selected>Select Pop</option>
              ${Array.from(document.querySelectorAll('#createLinkModal select[name="items\\[0\\]\[pop_id\]"] option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Link Type</label>
            <select name="items[${idx}][linkType_id]" class="form-select" required>
              <option value="" disabled selected>Select Type</option>
              ${Array.from(document.querySelectorAll('#createLinkModal select[name="items\\[0\\]\[linkType_id\]"] option'))
                .map(o => `<option value="${o.value}">${o.text}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Link</label>
            <input type="text" name="items[${idx}][link]" class="form-control" placeholder="e.g. MPLS-001" required>
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
  });
</script>
{{-- Account number uniqueness validation (create/edit modals and repeater) --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const accCheckUrl = "{{ route('customers.check-account-number') }}";
    const nameCheckUrl = "{{ route('customers.check-customer-name') }}";

    const debounceTimers = new WeakMap();

    function setSubmitDisabled(form, disabled) {
      const submitBtn = form?.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = !!disabled;
    }

    function refreshFormSubmitState(form) {
      const hasInvalidAcc = form.querySelectorAll('.account-number-input.is-invalid').length > 0;
      const hasInvalidName = form.querySelectorAll('.customer-name-input.is-invalid').length > 0;
      setSubmitDisabled(form, hasInvalidAcc || hasInvalidName);
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

    // Bind on load for existing inputs (create & edit modals)
    window.bindAccountNumberValidation(document);
    window.bindCustomerNameValidation(document);

    // Also re-bind when a Bootstrap modal is shown, to make sure dynamic content is wired
    document.querySelectorAll('.modal').forEach(function(modalEl) {
      modalEl.addEventListener('shown.bs.modal', function() {
        window.bindAccountNumberValidation(modalEl);
        window.bindCustomerNameValidation(modalEl);
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
      if (!accInputs.length && !nameInputs.length) {
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
        })
      ];

      Promise.all(checks).then(function(results){
        const anyTaken = results.some(function(r){ return r === false; }) ||
          form.querySelectorAll('.account-number-input.is-invalid').length > 0 ||
          form.querySelectorAll('.customer-name-input.is-invalid').length > 0;
        if (anyTaken) {
          setSubmitDisabled(form, false); // keep enabled to allow corrections
          // Focus the first invalid account number field
          const firstInvalid = form.querySelector('.account-number-input.is-invalid');
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
        const firstInvalid = form.querySelector('.customer-name-input.is-invalid, .account-number-input.is-invalid')
          || form.querySelector('.customer-name-input, .account-number-input');
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
  });
</script>
{{-- Cities repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('cityRepeater');
    if (!repeater) return;
    const itemsContainer = repeater.querySelector('.repeater-items');
    const addBtn = document.getElementById('addCityRepeaterItem');
    const removeBtn = document.getElementById('removeCityRepeaterItem');
    let index = itemsContainer.querySelectorAll('.repeater-item').length - 1;

    function createItem(idx) {
      const wrapper = document.createElement('div');
      wrapper.className = 'repeater-item border rounded p-3 mb-3';
      wrapper.innerHTML = `
        <div class="row g-3 align-items-end">
          <div class="col-12">
            <label class="form-label">City/Town</label>
            <input type="text" name="items[${idx}][city]" class="form-control" placeholder="e.g. Lusaka" required>
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
{{-- Positions repeater helpers --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const repeater = document.getElementById('positionRepeater');
    if (!repeater) return;
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
  });
</script>

