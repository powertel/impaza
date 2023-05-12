<script>
             function Confirm_Delete()
       {
        event.preventDefault();
        new swal({
            title: "Are you sure you want to delete this record?",
            text: "If you delete this, it will be gone forever.",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel","Yes!"],
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '##3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                $('#theForm').submit();
                swal.fire('Deleted!','Your file has been deleted.','success');
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

