@foreach($roles as $role)
<div class="modal custom-modal fade" id="roleShowModal{{ $role->id }}" tabindex="-1" aria-labelledby="roleShowModalLabel{{ $role->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="roleShowModalLabel{{ $role->id }}"><i class="fas fa-id-badge me-2"></i>Role Details</h5>
          <div class="modal-subtitle">Review the role name and all permissions currently assigned to this access group.</div>
        </div>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="workspace-panel mb-3">
          <div class="workspace-panel-header">
            <div>
              <h6 class="workspace-panel-title mb-0">Role Overview</h6>
              <div class="workspace-panel-lead">Core identity and permission coverage for this role.</div>
            </div>
          </div>
          <div class="workspace-panel-body">
            @php $permIds = $rolePermissionsMap[$role->id] ?? []; $assignedCount = count($permIds); @endphp
            <div class="workspace-stat-list">
              <div class="workspace-stat-row">
                <div>
                  <div class="workspace-stat-label">Role Name</div>
                  <div class="workspace-cell-main">{{ $role->name }}</div>
                </div>
                <div class="workspace-stat-value">{{ $assignedCount }} assigned</div>
              </div>
            </div>
          </div>
        </div>

        <div class="workspace-panel">
          <div class="workspace-panel-header">
            <div>
              <h6 class="workspace-panel-title mb-0">Permissions</h6>
              <div class="workspace-panel-lead">Assigned capabilities available to users with this role.</div>
            </div>
          </div>
          <div class="workspace-panel-body">
            <div class="workspace-chip-stack">
              @foreach($permission as $p)
                @if(in_array($p->id, $permIds))
                  <span class="badge rounded-pill" style="background: rgba(245, 158, 11, .14); color: #B45309;"><i class="fas fa-shield-alt me-1"></i>{{ $p->name }}</span>
                @endif
              @endforeach
              @if($assignedCount === 0)
                <span class="workspace-cell-sub">No permissions assigned.</span>
              @endif
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
