@can('chief-tech-clear-faults-clear')
<div class="modal custom-modal fade" id="chiefTechClearModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="chiefTechClearModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="chiefTechClearModalLabel-{{ $fault->id }}"><i class="fas fa-check-circle me-2"></i>Confirm Clear</h5>
          <div class="text-muted small mt-1">Confirm final chief technician clearance for {{ $fault->fault_ref_number ?? 'this fault' }}.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-user"></i> {{ $fault->customer ?? 'N/A' }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-link"></i> {{ $fault->link ?? 'N/A' }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-clear.update',$fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Use this action only after the rectification work has been verified and the case is ready to move into the cleared state.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-clipboard-check"></i></span>
              <div>
                <div class="fault-modal-section-title">Clearance Summary</div>
                <div class="fault-modal-section-subtitle">Quick review of the customer and affected service before confirming.</div>
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
                  <div class="fault-modal-kv-value">Move fault to <span class="badge rounded-pill bg-secondary-subtle text-secondary border">Cleared by Chief Tech</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="fas fa-save me-1"></i> Confirm Clear
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
