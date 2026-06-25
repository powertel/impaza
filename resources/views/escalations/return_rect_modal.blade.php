<div class="modal custom-modal fade" id="returnRectModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="returnRectModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="returnRectModalLabel-{{ $fault->id }}">Return to Rectification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.return', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="mb-2">Provide a remark to return this fault to rectification.</p>
          <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-warning btn-sm">
            <i class="fas fa-undo me-1"></i> Return
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


