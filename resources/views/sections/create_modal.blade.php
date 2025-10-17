@can('department-create')
<div class="modal fade" id="sectionCreateModal" tabindex="-1" aria-labelledby="sectionCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sectionCreateModalLabel">Create Section(s)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('sections.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-3">Select a department, then add one or more sections. Use “Add another” to insert additional rows.</p>

          <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Department</label>
              <select name="department_id" class="form-select" required>
                <option value="" disabled selected>Select Department</option>
                @foreach($departments as $dept)
                  <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="repeater" id="sectionRepeater">
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3">
                <div class="row g-3 align-items-end">
                  <div class="col-12">
                    <label class="form-label">Section</label>
                    <input type="text" name="items[0][section]" class="form-control" placeholder="e.g. Network Ops" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-light btn-sm" id="addSectionRepeaterItem"><i class="fas fa-plus"></i> Add another</button>
              <button type="button" class="btn btn-light btn-sm" id="removeSectionRepeaterItem"><i class="fas fa-minus"></i> Remove last</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
