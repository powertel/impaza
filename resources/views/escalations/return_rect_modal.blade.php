<div class="modal custom-modal fade" id="returnRectModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="returnRectModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="returnRectModalLabel-{{ $fault->id }}"><i class="fas fa-undo me-2"></i>Return to Rectification</h5>
          <div class="text-muted small mt-1">Send {{ $fault->fault_ref_number ?? 'this fault' }} back for more rectification work with a clear reason.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.return', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Use this when the escalation still needs field work, additional checks, or a corrected technical update before it can progress.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-wrench"></i></span>
              <div>
                <div class="fault-modal-section-title">Return Reason</div>
                <div class="fault-modal-section-subtitle">Explain what must be corrected during rectification.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <label class="form-label">Remark</label>
              <textarea name="remark" class="form-control" rows="3" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill">
            <i class="fas fa-undo me-1"></i> Return
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

