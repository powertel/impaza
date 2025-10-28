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
                            $("#link").append('<option value="' + key + '">' + value + '</option>');
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
        $.each(res, function(key, value){ $link.append('<option value="'+key+'">'+value+'</option>'); });
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
    var phone = phoneEl.val()||'';
    if(mark){
      if(phone.replace(/\D/g,'').length < 10){ allValid = false; phoneEl.addClass('is-invalid'); } else { phoneEl.removeClass('is-invalid'); }
    }
    $saveBtn.prop('disabled', !allValid);
  }
  $saveBtn.prop('disabled', true);
  // On modal open: compute validity without marking fields invalid
  $('#createFaultModal').on('shown.bs.modal', function(){ computeValidity(false); });
  // When user interacts: compute and mark invalids
  $(document).on('input change', '#createFaultModal input, #createFaultModal select, #createFaultModal textarea', function(){ computeValidity(true); });

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
      dropdownParent: $modal
    });
  }
  var cityID = $modal.find('.city-select').val();
  var suburbSelected = $modal.find('.suburb-select').data('selected');
  var customerID = $modal.find('.customer-select').val();
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
        $.each(res, function(key, value){ $link.append('<option value="'+key+'">'+value+'</option>'); });
        if (linkSelected) { $link.val(String(linkSelected)); }
      }
    });
  }

  // POP options will be populated by the suburb change handler triggered above
});
</script>
