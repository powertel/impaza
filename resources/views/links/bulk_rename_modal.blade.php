@can('link-edit')
<div class="modal fade" id="bulkRenameModal" tabindex="-1" aria-labelledby="bulkRenameModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkRenameModalLabel">Bulk Rename Links</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="GET" action="{{ route('links.rename-bulk') }}">
        <div class="modal-body">
          <p class="text-muted mb-3">
            This will rename selected links using the format:
            <span class="fw-semibold">Customer - City - Location - Pop - Service Type</span>.
          </p>
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
          <div class="alert alert-warning mb-0">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Existing names will be overwritten. If duplicates occur, a numeric suffix is appended.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-text-width me-1"></i> Rename
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan