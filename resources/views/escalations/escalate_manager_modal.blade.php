<div class="modal custom-modal fade" id="escalateMgrModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="escalateMgrModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="escalateMgrModalLabel-{{ $fault->id }}">Escalate to Manager</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.escalate-manager', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-level-up-alt me-1"></i> Escalate
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


