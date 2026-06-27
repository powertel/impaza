@can('link-edit')
<div class="modal custom-modal fade" id="bulkRenameModal" tabindex="-1" aria-labelledby="bulkRenameModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="bulkRenameModalLabel"><i class="fas fa-text-width me-2"></i>Bulk Rename Links</h5>
          <div class="text-muted small mt-1">Rename multiple links in one step using the standard customer-location-service naming pattern.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="GET" action="{{ route('links.rename-bulk') }}">
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>This will rename links using the format <strong>Customer - City - Location - Pop - Service Type</strong>.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-filter"></i></span>
              <div>
                <div class="fault-modal-section-title">Rename Scope</div>
                <div class="fault-modal-section-subtitle">Limit the rename job to one customer, or leave it blank to process all links.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-3">
                <label class="form-label">Customer (optional)</label>
                <select name="customer_id" class="form-select">
                  <option value="">All customers</option>
                  @foreach($customers as $cust)
                    <option value="{{ $cust->id }}">{{ $cust->customer }}</option>
                  @endforeach
                </select>
                <small class="form-text text-muted">Leave blank to rename all links.</small>
              </div>
              <div class="fault-modal-note mb-0">
                <i class="fas fa-triangle-exclamation"></i>
                <div>Existing names will be overwritten. If duplicates occur, a numeric suffix is appended.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning btn-sm">
            <i class="fas fa-text-width me-1"></i> Rename
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
