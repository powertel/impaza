@can('department-create')
<div class="modal custom-modal fade" id="sectionCreateModal" tabindex="-1" aria-labelledby="sectionCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="sectionCreateModalLabel"><i class="fas fa-sitemap me-2"></i>Create Sections</h5>
          <div class="text-muted small mt-1">Select the parent department once, then add one or more sections using the refreshed modal workspace.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-building"></i> Department Linked</span>
            <span class="fault-modal-meta-item"><i class="fas fa-layer-group"></i> Bulk Create</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('sections.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Add multiple sections in one pass while keeping them mapped to the correct department structure.</div>
          </div>

          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-building"></i></span>
              <div>
                <div class="fault-modal-section-title">Parent Department</div>
                <div class="fault-modal-section-subtitle">Choose the department that should own the new sections.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="row g-3">
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
            </div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-sitemap"></i></span>
              <div>
                <div class="fault-modal-section-title">Section Entries</div>
                <div class="fault-modal-section-subtitle">List each section you want to create for the selected department.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
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
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addSectionRepeaterItem"><i class="fas fa-plus me-1"></i> Add another</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="removeSectionRepeaterItem"><i class="fas fa-minus me-1"></i> Remove last</button>
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
