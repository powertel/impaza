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

