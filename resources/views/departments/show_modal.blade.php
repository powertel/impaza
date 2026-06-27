@can('department-list')
@foreach($departments as $department)
<div class="modal custom-modal fade" id="departmentShowModal{{ $department->id }}" tabindex="-1" aria-labelledby="departmentShowModalLabel{{ $department->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="departmentShowModalLabel{{ $department->id }}">
            <i class="fas fa-eye me-2"></i>Department Details
          </h5>
          <div class="text-muted small mt-1">Review the department, linked sections, and configured positions in the same modern modal pattern used across the refreshed UI.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-building"></i> {{ $department->department }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-sitemap"></i> {{ $department->sections->count() }} {{ Str::plural('Section', $department->sections->count()) }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-briefcase"></i> {{ $department->positions->count() }} {{ Str::plural('Position', $department->positions->count()) }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
          <i class="fas fa-circle-info"></i>
          <div>This overview helps verify how the department is structured before making edits to related sections or positions.</div>
        </div>

        <div class="fault-modal-section mb-3">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-building"></i></span>
            <div>
              <div class="fault-modal-section-title">Department Overview</div>
              <div class="fault-modal-section-subtitle">High-level structure details for the selected department.</div>
            </div>
          </div>
          <div class="fault-modal-section-body">
            <div class="fault-modal-grid">
              <div class="fault-modal-kv">
                <span class="fault-modal-kv-label">Department</span>
                <div class="fault-modal-kv-value">{{ $department->department }}</div>
              </div>
              <div class="fault-modal-kv">
                <span class="fault-modal-kv-label">Structure Summary</span>
                <div class="fault-modal-kv-value">{{ $department->sections->count() }} {{ Str::plural('Section', $department->sections->count()) }} and {{ $department->positions->count() }} {{ Str::plural('Position', $department->positions->count()) }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <div class="fault-modal-section h-100">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-sitemap"></i></span>
                <div>
                  <div class="fault-modal-section-title">Sections</div>
                  <div class="fault-modal-section-subtitle">Grouped business areas within this department.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                @if($department->sections && $department->sections->count())
                  <ul class="list-group">
                    @foreach($department->sections as $sec)
                      <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                        <span>{{ $sec->section }}</span>
                        <span class="record-chip"><i class="fas fa-briefcase"></i> {{ $sec->positions->count() }} {{ Str::plural('position', $sec->positions->count()) }}</span>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <div class="fault-modal-empty">No sections linked to this department yet.</div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="fault-modal-section h-100">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-briefcase"></i></span>
                <div>
                  <div class="fault-modal-section-title">Positions</div>
                  <div class="fault-modal-section-subtitle">All roles currently mapped to this department.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                @php $positions = $department->positions; @endphp
                @if($positions && $positions->count())
                  <div class="d-flex flex-wrap gap-2">
                    @foreach($positions as $pos)
                      <span class="record-chip"><i class="fas fa-briefcase"></i> {{ $pos->position }}</span>
                    @endforeach
                  </div>
                @else
                  <div class="fault-modal-empty">No positions linked to this department yet.</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan
