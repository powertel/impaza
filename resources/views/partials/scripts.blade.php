

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


// Departments repeater helpers
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
</script>

