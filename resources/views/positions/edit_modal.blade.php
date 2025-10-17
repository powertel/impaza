<div class="modal fade" id="positionEditModal" tabindex="-1" role="dialog" aria-labelledby="positionEditModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="positionEditModalLabel">Edit Position</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="positionEditForm" action="#" method="POST">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label class="form-label">Position</label>
            <input type="text" name="position" id="positionEditInput" class="form-control" required>
            @error('position')
              <div class="alert-danger">{{ $message }}</div>
            @enderror
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
          </div>
        </form>
      </div>
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
