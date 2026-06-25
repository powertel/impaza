@can('department-create')
<div class="modal custom-modal fade" id="departmentCreateModal" tabindex="-1" aria-labelledby="departmentCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="departmentCreateModalLabel">
            <i class="fas fa-building me-2"></i>Create Departments
          </h5>
          <div class="text-muted small mt-1">Add one or more departments using the same modern bulk-entry flow used across the updated workspace.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-layer-group"></i> Bulk Create</span>
            <span class="fault-modal-meta-item"><i class="fas fa-sitemap"></i> Org Structure</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('departments.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Use <strong>Add another</strong> to capture multiple departments in one submission while keeping a clean audit trail.</div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-building"></i></span>
              <div>
                <div class="fault-modal-section-title">Department Details</div>
                <div class="fault-modal-section-subtitle">Capture the names of each department you want to add to the organization structure.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="repeater" id="departmentRepeater">
                <div class="repeater-items">
                  <div class="repeater-item border rounded p-3 mb-3">
                    <div class="row g-3 align-items-end">
                      <div class="col-12">
                        <label class="form-label">Department</label>
                        <input type="text" name="items[0][department]" class="form-control" placeholder="e.g. Operations" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addRepeaterItem"><i class="fas fa-plus me-1"></i> Add another</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="removeRepeaterItem"><i class="fas fa-minus me-1"></i> Remove last</button>
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
@endcan
