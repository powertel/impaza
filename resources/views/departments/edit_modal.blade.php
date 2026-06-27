@can('department-edit')
@foreach($departments as $department)
<div class="modal custom-modal fade" id="departmentEditModal{{ $department->id }}" tabindex="-1" aria-labelledby="departmentEditModalLabel{{ $department->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="departmentEditModalLabel{{ $department->id }}">
            <i class="fas fa-pen-to-square me-2"></i>Edit Department
          </h5>
          <div class="text-muted small mt-1">Update the selected department name and keep the organization structure aligned with the new workspace design.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-hashtag"></i> ID {{ $department->id }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-building"></i> {{ $department->department }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('departments.update', $department->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Renaming a department updates its label across related sections and positions while preserving existing records.</div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-building"></i></span>
              <div>
                <div class="fault-modal-section-title">Department Name</div>
                <div class="fault-modal-section-subtitle">Use a clear and consistent business name for reporting and assignment workflows.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-0">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" value="{{ $department->department }}" required>
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
@endforeach
@endcan
