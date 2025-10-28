@foreach($roles as $role)
<div class="modal fade" id="roleShowModal{{ $role->id }}" tabindex="-1" aria-labelledby="roleShowModalLabel{{ $role->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="roleShowModalLabel{{ $role->id }}"><i class="fas fa-id-badge me-2"></i> Role Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="border rounded p-3 bg-light">
              <div class="mb-2"><strong class="text-muted">Name</strong></div>
              <div class="fw-semibold">{{ $role->name }}</div>
            </div>
          </div>
          <div class="col-12">
            @php $permIds = $rolePermissionsMap[$role->id] ?? []; $assignedCount = count($permIds); @endphp
            <div class="d-flex align-items-center mb-2">
              <strong class="text-muted">Permissions</strong>
              <span class="badge bg-secondary ms-2">{{ $assignedCount }} assigned</span>
            </div>
            <div class="border rounded p-2">
              @foreach($permission as $p)
                @if(in_array($p->id, $permIds))
                  <span class="badge bg-warning text-dark rounded-pill me-1 mb-1"><i class="fas fa-shield-alt"></i> {{ $p->name }}</span>
                @endif
              @endforeach
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
