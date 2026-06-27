<script>
document.addEventListener('DOMContentLoaded', function () {
  [
    '#createFaultModal',
    '[id^="editFaultModal-"]',
    '[id^="showFaultModal-"]',
    '[id^="PicModal-"]'
  ].forEach(function(selector){
    document.querySelectorAll(selector).forEach(function(modal){
      if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
      }
    });
  });
});
</script>

<script>
// Edit modal: populate dependent selects within the opened modal context
$(document).off('change', '.city-select').on('change', '.city-select', function(){
  var CityID = $(this).val();
  var $modal = $(this).closest('.modal');
  var $suburb = $modal.find('.suburb-select');
  var $pop = $modal.find('.pop-select');
  if (CityID) {
    $.ajax({
      url: '/suburb/' + CityID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        $suburb.empty().append('<option selected disabled>Select Suburb</option>');
        $pop.empty().append('<option selected disabled>Select Pop</option>');
        $.each(res, function(key, value){ $suburb.append('<option value="'+key+'">'+value+'</option>'); });
        var sel = $suburb.data('selected');
        if (sel) { $suburb.val(String(sel)); }
      }
    });
  } else {
    $suburb.empty();
    $pop.empty();
  }
});

$(document).off('change', '.suburb-select').on('change', '.suburb-select', function(){
  var suburbID = $(this).val();
  var $modal = $(this).closest('.modal');
  var $pop = $modal.find('.pop-select');
  if (suburbID) {
    $.ajax({
      url: '/pop/' + suburbID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        $pop.empty().append('<option selected disabled>Select Pop</option>');
        $.each(res, function(key, value){ $pop.append('<option value="'+key+'">'+value+'</option>'); });
        var psel = $pop.data('selected');
        if (psel) { $pop.val(String(psel)); }
      }
    });
  } else {
    $pop.empty();
  }
});

$(document).off('change', '.customer-select').on('change', '.customer-select', function(){
  var customerID = $(this).val();
  var $modal = $(this).closest('.modal');
  var $link = $modal.find('.link-select');
  if (customerID) {
    $.ajax({
      url: '/link/' + customerID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        if ($link.hasClass('select2-hidden-accessible')) {
          $link.empty().append('<option value=""></option>');
        } else {
          $link.empty().append('<option selected disabled>Select Link</option>');
        }
        $.each(res, function(key, value){
          var text = (value && typeof value === 'object')
            ? ((value.link || '') + ' - ' + (value.city || '') + ' - ' + (value.suburb || ''))
            : String(value || '');
          $link.append('<option value="'+key+'">'+text+'</option>');
        });
        var lsel = $link.data('selected');
        if (lsel) {
          $link.val(String(lsel));
        }
        if ($link.hasClass('select2-hidden-accessible')) {
          $link.trigger('change.select2');
        }
      }
    });
  } else {
    if ($link.hasClass('select2-hidden-accessible')) {
      $link.empty().append('<option value=""></option>');
    } else {
      $link.empty().append('<option selected disabled>Select Link</option>');
    }
    if ($link.hasClass('select2-hidden-accessible')) {
      $link.trigger('change.select2');
    }
  }
});
</script>

<script>
// Create modal: initialize controls and keep submit disabled until required fields are valid
$(function(){
  var $modal = $('#createFaultModal');
  var $saveBtn = $('button[form="UF"][type="submit"]');
  function computeCreateValidity(mark){
    var requiredSelectors = [
      '#createFaultModal #customer',
      '#createFaultModal input[name="contactName"]',
      '#createFaultModal input[name="phoneNumber"]',
      '#createFaultModal #link',
      '#createFaultModal select[name="suspectedRfo_id"]',
      '#createFaultModal textarea[name="remark"]'
    ];
    var allValid = true;
    requiredSelectors.forEach(function(sel){
      var $el = $(sel);
      if(!$el.length){ allValid = false; return; }
      var isSelect = $el.is('select');
      var val = isSelect ? $el.val() : ($el.val()||'').trim();
      var empty = !val;
      if(empty){ allValid = false; }
      if(mark){
        if(empty){ $el.addClass('is-invalid'); } else { $el.removeClass('is-invalid'); }
      } else {
        // On initial open, do not mark invalid
        $el.removeClass('is-invalid');
      }
    });
    var phoneEl = $('#createFaultModal input[name="phoneNumber"]');
    var phone = (phoneEl.val()||'').trim();
    if (phone && !/^2637\d{8}$/.test(phone)) {
      allValid = false;
      if (mark) {
        phoneEl.addClass('is-invalid');
      }
    } else if (mark) {
      phoneEl.removeClass('is-invalid');
    }
    $saveBtn.prop('disabled', !allValid);
  }

  function initCreateSelects() {
    [
      { selector: '#customer', placeholder: 'Select Customer' },
      { selector: '#link', placeholder: 'Select Link' },
      { selector: '#suspectedRFO', placeholder: 'Select RFO' }
    ].forEach(function(item){
      var $el = $modal.find(item.selector);
      if ($el.length && !$el.hasClass('select2-hidden-accessible')) {
        $el.select2({
          placeholder: item.placeholder,
          width: '100%',
          dropdownParent: $modal
        });
      }
    });
  }

  $saveBtn.prop('disabled', true);
  $modal.on('shown.bs.modal', function(){
    initCreateSelects();
    computeCreateValidity(false);
  });

  $(document).on('input change', '#createFaultModal input, #createFaultModal select, #createFaultModal textarea', function(){
    computeCreateValidity(true);
  });

  $(document).on('input', '#createFaultModal input[name="phoneNumber"]', function(){
    var v = (this.value||'').replace(/\D+/g,'');
    this.value = v;
  });

  var dz = document.querySelector('#createFaultModal [data-impaza-dropzone]');
  var attachmentInput = document.getElementById('attachment');
  if (dz && attachmentInput) {
    function stop(e){ e.preventDefault(); e.stopPropagation(); }
    dz.addEventListener('dragenter', function(e){ stop(e); dz.classList.add('is-dragover'); });
    dz.addEventListener('dragover', function(e){ stop(e); dz.classList.add('is-dragover'); });
    dz.addEventListener('dragleave', function(e){ stop(e); dz.classList.remove('is-dragover'); });
    dz.addEventListener('drop', function(e){
      stop(e);
      dz.classList.remove('is-dragover');
      var files = e.dataTransfer && e.dataTransfer.files ? e.dataTransfer.files : null;
      if (!files || !files.length) return;
      if (window.DataTransfer) {
        var dt = new DataTransfer();
        dt.items.add(files[0]);
        attachmentInput.files = dt.files;
      }
      attachmentInput.dispatchEvent(new Event('change', { bubbles: true }));
    });
  }
});
</script>

<script>
// Initialize edit modal selects on open
$(document).off('shown.bs.modal', '.modal[id^="editFaultModal-"]').on('shown.bs.modal', '.modal[id^="editFaultModal-"]', function(){
  var $modal = $(this);
  // Apply Select2 to customer select in the edit modal
  var $customerSel = $modal.find('.customer-select');
  if($customerSel.length && !$customerSel.hasClass('select2-hidden-accessible')){
    $customerSel.select2({
      placeholder: 'Select Customer',
      width: '100%',
      dropdownParent: $modal,
      minimumResultsForSearch: Infinity
    });
    var currentVal = $customerSel.data('selected') || $customerSel.find('option:selected').val();
    if (currentVal) { $customerSel.val(String(currentVal)).trigger('change.select2'); }
  }
  var cityID = $modal.find('.city-select').val();
  var suburbSelected = $modal.find('.suburb-select').data('selected');
  var customerID = $modal.find('.customer-select').val() || $modal.find('input[name="customer_id"]').val();
  var linkSelected = $modal.find('.link-select').data('selected');
  var popSelected = $modal.find('.pop-select').data('selected');

  if (cityID) {
    $.ajax({
      url: '/suburb/' + cityID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        var $suburb = $modal.find('.suburb-select');
        $suburb.empty().append('<option selected disabled>Select Suburb</option>');
        $.each(res, function(key, value){ $suburb.append('<option value="'+key+'">'+value+'</option>'); });
        if (suburbSelected) { $suburb.val(String(suburbSelected)).trigger('change'); }
      }
    });
  }

  if (customerID) {
    $.ajax({
      url: '/link/' + customerID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        var $link = $modal.find('.link-select');
        $link.empty().append('<option selected disabled>Select Link</option>');
        $.each(res, function(key, value){
          var text = (value && typeof value === 'object')
            ? ((value.link || '') + ' - ' + (value.city || '') + ' - ' + (value.suburb || ''))
            : String(value || '');
          $link.append('<option value="'+key+'">'+text+'</option>');
        });
        if (linkSelected) { $link.val(String(linkSelected)); }
      }
    });
  }

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var remark = document.querySelector('#createFaultModal textarea[name="remark"]');
  var attachmentInput = document.getElementById('attachment');
  var previewImg = document.getElementById('attachmentPreview');
  var previewContainer = document.getElementById('attachmentPreviewContainer');
  if (remark && attachmentInput) {
    function setFile(file){
      if (!file) return;
      if (window.DataTransfer) {
        var dt = new DataTransfer();
        dt.items.add(file);
        attachmentInput.files = dt.files;
      }
      if (file.type && file.type.indexOf('image') === 0 && previewImg && previewContainer) {
        var reader = new FileReader();
        reader.onload = function(e){
          previewImg.src = e.target.result;
          previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }
    remark.addEventListener('paste', function(e){
      if (!e.clipboardData || !e.clipboardData.items) return;
      var items = e.clipboardData.items;
      for (var i = 0; i < items.length; i++) {
        var item = items[i];
        if (item.kind === 'file') {
          var file = item.getAsFile();
          if (file) {
            setFile(file);
            break;
          }
        }
      }
    });
    attachmentInput.addEventListener('change', function(){
      var file = this.files && this.files[0] ? this.files[0] : null;
      if (!file) {
        if (previewContainer) previewContainer.style.display = 'none';
        if (previewImg) previewImg.src = '';
        return;
      }
      setFile(file);
    });
    $('#createFaultModal').on('hidden.bs.modal', function(){
      if (attachmentInput) {
        attachmentInput.value = '';
        if (window.DataTransfer) {
          var dt = new DataTransfer();
          attachmentInput.files = dt.files;
        }
      }
      if (previewContainer) previewContainer.style.display = 'none';
      if (previewImg) previewImg.src = '';
    });
  }
  function setEditFile(faultId, file){
    if (!file) return;
    var input = document.querySelector('.edit-attachment[data-fault-id="'+faultId+'"]');
    var preview = document.querySelector('.edit-attachment-preview[data-fault-id="'+faultId+'"]');
    var container = document.querySelector('.edit-attachment-preview-container[data-fault-id="'+faultId+'"]');
    if (input && window.DataTransfer) {
      var dt2 = new DataTransfer();
      dt2.items.add(file);
      input.files = dt2.files;
    }
    if (file.type && file.type.indexOf('image') === 0 && preview && container) {
      var reader2 = new FileReader();
      reader2.onload = function(e){
        preview.src = e.target.result;
        container.style.display = 'block';
      };
      reader2.readAsDataURL(file);
    }
  }
  document.querySelectorAll('.edit-remark').forEach(function(el){
    el.addEventListener('paste', function(e){
      if (!e.clipboardData || !e.clipboardData.items) return;
      var items = e.clipboardData.items;
      for (var i = 0; i < items.length; i++) {
        var item = items[i];
        if (item.kind === 'file') {
          var file = item.getAsFile();
          if (file) {
            var fid = el.getAttribute('data-fault-id');
            setEditFile(fid, file);
            break;
          }
        }
      }
    });
  });
  document.querySelectorAll('.edit-attachment').forEach(function(el){
    el.addEventListener('change', function(){
      var fid = el.getAttribute('data-fault-id');
      var file = this.files && this.files[0] ? this.files[0] : null;
      if (!file) {
        var container = document.querySelector('.edit-attachment-preview-container[data-fault-id="'+fid+'"]');
        var preview = document.querySelector('.edit-attachment-preview[data-fault-id="'+fid+'"]');
        if (container) container.style.display = 'none';
        if (preview) preview.src = '';
        return;
      }
      setEditFile(fid, file);
    });
  });
  $(document).on('hidden.bs.modal', '.modal[id^="editFaultModal-"]', function(){
    var $modal = $(this);
    $modal.find('.edit-attachment').each(function(){
      this.value = '';
      if (window.DataTransfer) {
        var dt3 = new DataTransfer();
        this.files = dt3.files;
      }
    });
    $modal.find('.edit-attachment-preview-container').each(function(){
      this.style.display = 'none';
    });
    $modal.find('.edit-attachment-preview').each(function(){
      this.src = '';
    });
  });
});
</script>
