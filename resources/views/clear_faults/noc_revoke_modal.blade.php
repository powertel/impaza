@can('noc-clear-faults-clear')
<div class="modal custom-modal fade" id="nocRevokeModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="nocRevokeModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="nocRevokeModalLabel-{{ $fault->id }}"><i class="fas fa-rotate-left me-2"></i>Confirm Revoke</h5>
          <div class="text-muted small mt-1">Return {{ $fault->fault_ref_number ?? 'this fault' }} to rectification when the NOC clearance should not proceed.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-user"></i> {{ $fault->customer ?? 'N/A' }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-link"></i> {{ $fault->link ?? 'N/A' }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('noc-clear.revoke',$fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Use this when the fault still requires technical rework and should be sent back before NOC clearance is finalized.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-undo"></i></span>
              <div>
                <div class="fault-modal-section-title">Revoke Summary</div>
                <div class="fault-modal-section-subtitle">Review the customer and service context before returning the fault.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="fault-modal-grid">
                <div class="fault-modal-kv">
                  <span class="fault-modal-kv-label">Customer</span>
                  <div class="fault-modal-kv-value">{{ $fault->customer ?? 'N/A' }}</div>
                </div>
                <div class="fault-modal-kv">
                  <span class="fault-modal-kv-label">Link</span>
                  <div class="fault-modal-kv-value">{{ $fault->link ?? 'N/A' }}</div>
                </div>
                <div class="fault-modal-kv">
                  <span class="fault-modal-kv-label">Action</span>
                  <div class="fault-modal-kv-value">Return to technician rework</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
            <i class="fas fa-undo me-1"></i> Revoke
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
