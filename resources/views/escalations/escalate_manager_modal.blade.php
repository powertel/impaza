<div class="modal custom-modal fade" id="escalateMgrModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="escalateMgrModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="escalateMgrModalLabel-{{ $fault->id }}"><i class="fas fa-level-up-alt me-2"></i>Escalate to Manager</h5>
          <div class="text-muted small mt-1">Push {{ $fault->fault_ref_number ?? 'this fault' }} to manager review with escalation context.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.escalate-manager', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Summarize the technical impact, risk, or blocker that requires manager-level visibility before you escalate.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-arrow-trend-up"></i></span>
              <div>
                <div class="fault-modal-section-title">Escalation Note</div>
                <div class="fault-modal-section-subtitle">Provide the manager-facing summary for this escalation.</div>
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
          <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
            <i class="fas fa-level-up-alt me-1"></i> Escalate
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

