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
                        $("#suburb").append('<option value="' + key + '">' + value + '</option>');
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
// Disable Save until all required fields in the create modal are valid
$(function(){
  var $saveBtn = $('button[form="UF"][type="submit"]');
  function checkValidity(){
    var requiredSelectors = [
      '#customer', '#city', 'input[name="contactName"]', 'input[name="phoneNumber"]', 'input[name="contactEmail"]',
      '#suburb', '#link', '#pop', 'select[name="serviceType"]', 'select[name="suspectedRfo_id"]',
      'input[name="address"]', 'select[name="accountManager_id"]', 'textarea[name="remark"]'
    ];
    var allValid = true;
    requiredSelectors.forEach(function(sel){
      var $el = $(sel);
      if(!$el.length){ allValid = false; return; }
      if($el.is('select')){
        var val = $el.val();
        if(!val){ allValid = false; $el.addClass('is-invalid'); } else { $el.removeClass('is-invalid'); }
      } else {
        var val = ($el.val()||'').trim();
        if(!val){ allValid = false; $el.addClass('is-invalid'); } else { $el.removeClass('is-invalid'); }
      }
    });
    var email = $('input[name="contactEmail"]').val()||'';
    if(email && !/^\S+@\S+\.\S+$/.test(email)){ allValid = false; $('input[name="contactEmail"]').addClass('is-invalid'); }
    var phone = $('input[name="phoneNumber"]').val()||'';
    if(phone.replace(/\D/g,'').length < 10){ allValid = false; $('input[name="phoneNumber"]').addClass('is-invalid'); }
    $saveBtn.prop('disabled', !allValid);
  }
  $saveBtn.prop('disabled', true);
  $('#createFaultModal').on('shown.bs.modal', checkValidity);
  $(document).on('input change', '#createFaultModal input, #createFaultModal select, #createFaultModal textarea', checkValidity);
});
</script>