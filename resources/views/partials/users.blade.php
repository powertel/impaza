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

function renderLoginHistoryLoading($tbody) {
  $tbody.html('<tr><td colspan="2" class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...</td></tr>');
}

function renderLoginHistoryEmpty($tbody) {
  $tbody.html('<tr><td colspan="2" class="text-center text-muted py-3">No login activity recorded yet.</td></tr>');
}

function showLoginHistoryError(msg) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({ icon: 'error', title: 'Login history', text: msg || 'Failed to load login history.' });
    return;
  }
}

function loadLoginHistoryPage(userId, url, append) {
  var $tbody = $('#loginHistoryBody-' + userId);
  var $btn = $('.login-history-load-more[data-user-id="' + userId + '"]');

  if (!append) {
    renderLoginHistoryLoading($tbody);
  }

  $btn.prop('disabled', true);

  $.ajax({
    url: url,
    type: 'GET',
    dataType: 'json',
    success: function(res) {
      var html = (res && res.html) ? String(res.html) : '';
      var nextUrl = (res && res.next_page_url) ? String(res.next_page_url) : '';

      if (!append) {
        $tbody.html(html);
      } else {
        $tbody.append(html);
      }

      if ($tbody.find('tr').length === 0) {
        renderLoginHistoryEmpty($tbody);
      }

      if (nextUrl) {
        $btn.data('nextUrl', nextUrl).show();
      } else {
        $btn.data('nextUrl', '').hide();
      }
    },
    error: function() {
      if (!append) {
        renderLoginHistoryEmpty($tbody);
      }
      showLoginHistoryError('Failed to load login history.');
    },
    complete: function() {
      $btn.prop('disabled', false);
    }
  });
}

$(document).off('shown.bs.modal', '.modal[id^="showUserModal-"]').on('shown.bs.modal', '.modal[id^="showUserModal-"]', function(){
  var $modal = $(this);
  var $tbody = $modal.find('tbody[id^="loginHistoryBody-"]');
  var userId = $tbody.data('userId');
  if (!userId) return;

  var $btn = $modal.find('.login-history-load-more[data-user-id="' + userId + '"]');
  var initialUrl = $btn.data('initialUrl');
  if (!initialUrl) return;

  $btn.data('nextUrl', initialUrl).show();
  loadLoginHistoryPage(userId, initialUrl, false);
});

$(document).off('click', '.login-history-load-more').on('click', '.login-history-load-more', function(){
  var $btn = $(this);
  var userId = $btn.data('userId');
  var nextUrl = $btn.data('nextUrl') || $btn.data('initialUrl');
  if (!userId || !nextUrl) return;
  loadLoginHistoryPage(userId, nextUrl, true);
});
</script>
