<div class="modal custom-modal fade" id="positionCreateModal" tabindex="-1" role="dialog" aria-labelledby="positionCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="positionCreateModalLabel"><i class="fas fa-briefcase me-2"></i>Create Positions</h5>
          <div class="text-muted small mt-1">Choose the department and section first, then add one or more positions using the refreshed workspace modal.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-building"></i> Department Linked</span>
            <span class="fault-modal-meta-item"><i class="fas fa-sitemap"></i> Section Linked</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('positions.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Add multiple role entries in one submission while preserving the correct organizational hierarchy.</div>
          </div>

          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-diagram-project"></i></span>
              <div>
                <div class="fault-modal-section-title">Hierarchy Mapping</div>
                <div class="fault-modal-section-subtitle">Map new positions to the correct department and section.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Department</label>
                  <select id="department" name="department_id" class="form-select" required>
                    <option selected disabled>Select department</option>
                    @foreach($department as $dept)
                      <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Section</label>
                  <select id="section" name="section_id" class="form-select" required>
                    <option selected disabled>Select section</option>
                    @foreach($section as $sec)
                      <option value="{{ $sec->id }}">{{ $sec->section }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-briefcase"></i></span>
              <div>
                <div class="fault-modal-section-title">Position Entries</div>
                <div class="fault-modal-section-subtitle">Add one or more positions that belong to the chosen section.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="repeater" id="positionRepeater">
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
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addPositionRepeaterItem"><i class="fas fa-plus me-1"></i> Add another</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="removePositionRepeaterItem"><i class="fas fa-minus me-1"></i> Remove last</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>


    </div>
  </div>
</div>
