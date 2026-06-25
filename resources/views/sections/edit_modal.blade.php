@can('department-edit')
@foreach($sections as $section)
<div class="modal custom-modal fade" id="sectionEditModal{{ $section->id }}" tabindex="-1" aria-labelledby="sectionEditModalLabel{{ $section->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="sectionEditModalLabel{{ $section->id }}"><i class="fas fa-pen-to-square me-2"></i>Edit Section</h5>
          <div class="text-muted small mt-1">Update the section name while keeping it aligned to the modern organization workspace.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-building"></i> {{ $section->department ?? 'Department' }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-sitemap"></i> {{ $section->section }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('sections.update', $section->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Renaming the section updates how it appears across organization and assignment views without changing its linked records.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-sitemap"></i></span>
              <div>
                <div class="fault-modal-section-title">Section Name</div>
                <div class="fault-modal-section-subtitle">Use a short and recognizable label for this department section.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-0">
                <label class="form-label">Section</label>
                <input type="text" name="section" class="form-control" value="{{ $section->section }}" required>
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
