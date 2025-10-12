<div class="modal fade" id="positionCreateModal" tabindex="-1" role="dialog" aria-labelledby="positionCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="positionCreateModalLabel">Create Position(s)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('positions.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <select id="department" name="department_id" class="custom-select" required>
                <option selected disabled>Select department</option>
                @foreach($department as $dept)
                  <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select id="section" name="section_id" class="custom-select" required>
                <option selected disabled>Select section</option>
                @foreach($section as $sec)
                  <option value="{{ $sec->id }}">{{ $sec->section }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mt-3 repeater" id="positionRepeater">
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3">
                <div class="row g-3 align-items-end">
                  <div class="col-12">
                    <label class="form-label">Position</label>
                    <input type="text" name="items[0][position]" class="form-control" placeholder="e.g. Senior Engineer" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-light btn-sm" id="addPositionRepeaterItem"><i class="fas fa-plus"></i> Add another</button>
              <button type="button" class="btn btn-light btn-sm" id="removePositionRepeaterItem"><i class="fas fa-minus"></i> Remove last</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>


    </div>
  </div>
</div>