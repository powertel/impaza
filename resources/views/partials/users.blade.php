<script>
// Department -> Section -> Position cascading for modals, scoped by modal context
$(document).off('change', '.department-select').on('change', '.department-select', function(){
  var departmentID = $(this).val();
  var $modal = $(this).closest('.modal');
  var $section = $modal.find('.section-select');
  var $position = $modal.find('.position-select');
  if (departmentID) {
    $.ajax({
      url: '/section/' + departmentID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        $section.empty().append('<option selected disabled>Select Section</option>');
        $position.empty().append('<option selected disabled>Select Position</option>');
        $.each(res, function(key, value){ $section.append('<option value="'+key+'">'+value+'</option>'); });
        var sel = $section.data('selected');
        if (sel) { $section.val(String(sel)).trigger('change'); }
      }
    });
  } else {
    $section.empty();
    $position.empty();
  }
});

$(document).off('change', '.section-select').on('change', '.section-select', function(){
  var sectionID = $(this).val();
  var $modal = $(this).closest('.modal');
  var $position = $modal.find('.position-select');
  if (sectionID) {
    $.ajax({
      url: '/position/' + sectionID,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        $position.empty().append('<option selected disabled>Select Position</option>');
        $.each(res, function(key, value){ $position.append('<option value="'+key+'">'+value+'</option>'); });
        var psel = $position.data('selected');
        if (psel) { $position.val(String(psel)); }
      }
    });
  } else {
    $position.empty();
  }
});

// Initialize edit modal cascading selects on open
$(document).off('shown.bs.modal', '.modal[id^="editUserModal-"]').on('shown.bs.modal', '.modal[id^="editUserModal-"]', function(){
  var $modal = $(this);
  var depId = $modal.find('.department-select').val();
  var sectionSelected = $modal.find('.section-select').data('selected');
  var positionSelected = $modal.find('.position-select').data('selected');
  if (depId) {
    $.ajax({
      url: '/section/' + depId,
      type: 'GET',
      dataType: 'json',
      success: function(res){
        var $section = $modal.find('.section-select');
        $section.empty().append('<option selected disabled>Select Section</option>');
        $.each(res, function(key, value){ $section.append('<option value="'+key+'">'+value+'</option>'); });
        if (sectionSelected) { $section.val(String(sectionSelected)).trigger('change'); }
      }
    });
  }
  // Position will be populated by the section change handler triggered above
});
</script>
