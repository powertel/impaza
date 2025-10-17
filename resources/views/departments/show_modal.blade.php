@can('department-list')
@foreach($departments as $department)
<div class="modal fade" id="departmentShowModal{{ $department->id }}" tabindex="-1" aria-labelledby="departmentShowModalLabel{{ $department->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="departmentShowModalLabel{{ $department->id }}">Department Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <h6 class="mb-1">Department</h6>
          <p class="mb-0"><strong>{{ $department->department }}</strong></p>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <h6>Sections</h6>
            @if($department->sections && $department->sections->count())
              <ul class="list-group">
                @foreach($department->sections as $sec)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $sec->section }}
                    <span class="badge bg-secondary">{{ $sec->positions->count() }} positions</span>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-muted">No sections</p>
            @endif
          </div>
          <div class="col-md-6">
            <h6>Positions</h6>
            @php $positions = $department->positions; @endphp
            @if($positions && $positions->count())
              <div>
                @foreach($positions as $pos)
                  <span class="badge bg-light text-dark border me-1 mb-1">{{ $pos->position }}</span>
                @endforeach
              </div>
            @else
              <p class="text-muted">No positions</p>
            @endif
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan
