@can('request-permit')
<div class="modal fade" id="requestPermitEditModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="requestPermitEditModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="requestPermitEditModalLabel-{{ $fault->id }}">Request Permit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Keeping as UI-only form to match existing edit view behavior -->
      <form>
        <div class="modal-body">
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Requested By</label>
              <input type="text" class="form-control" value="{{ $fault->customer }}" disabled>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Fault Number</label>
              <input type="text" class="form-control" placeholder="e.g. F-2025-001">
            </div>
            <div class="mb-3 col">
              <label class="form-label">PTW Number</label>
              <input type="text" class="form-control" placeholder="e.g. PTW-12345">
            </div>
            <div class="mb-3 col">
              <label class="form-label">CR Number</label>
              <input type="text" class="form-control" placeholder="e.g. CR-9876">
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Date of Issue</label>
              <input type="text" class="form-control" placeholder="YYYY-MM-DD">
            </div>
            <div class="mb-3 col">
              <label class="form-label">Start Time</label>
              <input type="text" class="form-control" placeholder="HH:MM">
            </div>
            <div class="mb-3 col">
              <label class="form-label">End Time</label>
              <input type="text" class="form-control" placeholder="HH:MM">
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Priority</label>
              <select class="form-select">
                <option>Select</option>
                <option>Low</option>
                <option>Medium</option>
                <option>Normal</option>
                <option>High</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label class="form-label">Description</label>
              <textarea class="form-control" rows="3" placeholder="Work description"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success btn-sm"><i class="fas fa-save me-1"></i>Request</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan