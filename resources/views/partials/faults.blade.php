<script>
$(document).off('change', '#city').on('change', '#city', function () {
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
                        $("#pop").empty();
                    }
                }
            });
        } else {
            $("#suburb").empty();
            $("#pop").empty();
        }
    });
    $(document).off('change', '#suburb').on('change', '#suburb', function () {
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
    $(document).off('change', '#customer').on('change', '#customer', function () {
        var customerID = $(this).val();
        if (customerID) {
            $.ajax({
                type: "GET",
                url : '/link/' +customerID,
                dataType: "json",
                success: function (res) {
                    if (res) {
                        $("#link").empty();
                        $("#link").append('<option  selected Disabled>Select Link</option>');
                        $.each(res, function (key, value) {
                            // value is now an object with id, link, city, suburb
                            var text = value.link + ' - ' + (value.city || '') + ' - ' + (value.suburb || '');
                            $("#link").append('<option value="' + value.id + '">' + text + '</option>');
                        });

                    } else {
                        $("#link").empty();
                    }
                }
            });
        } else {
            $("#link").empty();
        }
    });
</script>
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
        $link.empty().append('<option selected disabled>Select Link</option>');
        $.each(res, function(key, value){
          var text = (value && typeof value === 'object')
            ? ((value.link || '') + ' - ' + (value.city || '') + ' - ' + (value.suburb || ''))
            : String(value || '');
          $link.append('<option value="'+key+'">'+text+'</option>');
        });
        var lsel = $link.data('selected');
        if (lsel) { $link.val(String(lsel)); }
      }
    });
  } else {
    $link.empty();
  }
});
</script>

<script>
// Disable Save until all required fields in the create modal are valid
$(function(){
  var $saveBtn = $('button[form="UF"][type="submit"]');
  function computeValidity(mark){
    var requiredSelectors = [
      '#customer', 'input[name="contactName"]', 'input[name="phoneNumber"]',
      '#link', 'select[name="suspectedRfo_id"]',
      'textarea[name="remark"]'
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
    var phoneEl = $('input[name="phoneNumber"]');
    var phone = (phoneEl.val()||'').trim();
    if(mark){
      var ok = /^2637\d{8}$/.test(phone);
      if(!ok){ allValid = false; phoneEl.addClass('is-invalid'); } else { phoneEl.removeClass('is-invalid'); }
    }
    $saveBtn.prop('disabled', !allValid);
  }
  $saveBtn.prop('disabled', true);
  // On modal open: compute validity without marking fields invalid
  $('#createFaultModal').on('shown.bs.modal', function(){ computeValidity(false); });
  // When user interacts: compute and mark invalids
  $(document).on('input change', '#createFaultModal input, #createFaultModal select, #createFaultModal textarea', function(){ computeValidity(true); });

  // Normalize phone input to digits only
  $(document).on('input', 'input[name="phoneNumber"]', function(){
    var v = (this.value||'').replace(/\D+/g,'');
    this.value = v;
  });

  // Enhance the Customer select with Select2 inside the modal
  if($('#customer').length){
    $('#customer').select2({
      placeholder: 'Select Customer',
      width: '100%',
      dropdownParent: $('#createFaultModal')
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
