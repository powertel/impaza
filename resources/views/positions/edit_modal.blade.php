<div class="modal custom-modal fade" id="positionEditModal" tabindex="-1" role="dialog" aria-labelledby="positionEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="positionEditModalLabel"><i class="fas fa-pen-to-square me-2"></i>Edit Position</h5>
          <div class="text-muted small mt-1">Update the selected role name using the same modern modal pattern as the other organization modules.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-briefcase"></i> Role Update</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="positionEditForm" action="#" method="POST">
        <div class="modal-body">
          @csrf
          @method('PUT')
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changing the position label updates how the role appears across organization management views.</div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-briefcase"></i></span>
              <div>
                <div class="fault-modal-section-title">Position Name</div>
                <div class="fault-modal-section-subtitle">Enter the updated role name for this record.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-0">
                <label class="form-label">Position</label>
                <input type="text" name="position" id="positionEditInput" class="form-control" required>
                @error('position')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('scripts')
  @parent
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      $('#positionEditModal').on('show.bs.modal', function(e){
        var button = $(e.relatedTarget);
        var id = button.data('id');
        var name = button.data('position');
        $('#positionEditInput').val(name);
        $('#positionEditForm').attr('action', '/positions/' + id);
      });
    });
  </script>
@endsection
