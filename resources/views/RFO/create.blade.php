<div class="modal custom-modal fade" id="createRfoModal" tabindex="-1" aria-labelledby="createRfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="createRfoModalLabel"><i class="fas fa-circle-plus me-2"></i>Create Reason For Outage</h5>
          <div class="text-muted small mt-1">Add a new outage reason to the shared library used across fault logging and assessment.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('rfos.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Keep the name short and clear so it works well in forms, filters, reports, and status conversations.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-list-check"></i></span>
              <div>
                <div class="fault-modal-section-title">Reason Details</div>
                <div class="fault-modal-section-subtitle">Capture the new outage reason that should be available system-wide.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <label class="form-label" for="createRfoInput">Reason For Outage</label>
              <input type="text" id="createRfoInput" name="RFO" class="form-control @error('RFO') is-invalid @enderror" placeholder="Reason For Outage" value="{{ old('RFO') }}" required>
              @error('RFO')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times"></i>
            Cancel</button>
          <button type="submit" class="btn btn-outline-success btn-sm rounded-pill">
            <i class="fas fa-save"></i>
            Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
