<script>
$('#department').on('change',function () {
        var departmentID = $(this).val();
        if (departmentID) {
            $.ajax({
                url : '/section/' +departmentID,
                type: "GET",
                dataType: "json",
                success: function (res) {
                    if (res) {
                        $("#section").empty();
                        $("#position").empty();
                        $("#section").append('<option  selected Disabled>Select Section</option>');
                        $("#position").append('<option  selected Disabled>Select Position</option>');
                        $.each(res, function (key, value) {
                            $("#section").append('<option value="' + key + '">' + value + '</option>');
                        });

                    } else {
                        $("#section").append('<option value="' + key + '">' + value + '</option>');
                    }
                }
            });
        } else {
            $("#section").empty();
            $("#department").empty();
        }
    });
    $('#section').on('change', function () {
        var sectionID = $(this).val();
        if (sectionID) {
            $.ajax({
                url : '/position/' +sectionID,
                type: "GET",
                dataType: "json",
                success: function (res) {
                    if (res) {
                        $("#position").empty();
                        $("#position").append('<option  selected Disabled>Select Position</option>');
                        $.each(res, function (key, value) {
                            $("#position").append('<option value="' + key + '">' + value + '</option>');
                        });

                    } else {
                        $("#position").empty();
                    }
                }
            });
        } else {
            $("#position").empty();
        }
    });
</script>

