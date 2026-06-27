<div class="modal custom-modal fade" id="returnFromManagerModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="returnFromManagerLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="returnFromManagerLabel-{{ $fault->id }}"><i class="fas fa-level-down-alt me-2"></i>Return From Manager</h5>
          <div class="text-muted small mt-1">Send {{ $fault->fault_ref_number ?? 'this fault' }} back from manager review to chief technician handling.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.return-from-manager', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Capture the manager feedback or instruction that should guide the next chief technician action.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-reply"></i></span>
              <div>
                <div class="fault-modal-section-title">Return Note</div>
                <div class="fault-modal-section-subtitle">Record the decision coming back from manager review.</div>
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
            <i class="fas fa-level-down-alt me-1"></i> Return to Chief Tech
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
