@can('link-list')
<div class="modal custom-modal fade" id="linkViewModal{{ $link->id }}" tabindex="-1" aria-labelledby="linkViewModalLabel{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="linkViewModalLabel{{ $link->id }}"><i class="fas fa-eye me-2"></i>Link Details</h5>
          <div class="text-muted small mt-1">Review full service, location, and contract details for this link in the refreshed business modal layout.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-link"></i> {{ $link->link }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-users"></i> {{ $link->customer }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
          <i class="fas fa-circle-info"></i>
          <div>This overview helps verify service and location mapping details before editing or changing the link lifecycle.</div>
        </div>
        <div class="fault-modal-section">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-link"></i></span>
            <div>
              <div class="fault-modal-section-title">Link Overview</div>
              <div class="fault-modal-section-subtitle">Customer, service, and mapping details for this link record.</div>
            </div>
          </div>
          <div class="fault-modal-section-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Customer</label>
            <div class="form-control bg-light">{{ $link->customer }}</div>
          </div>
          <div class="col-md-3">
            <label class="form-label">City/Town</label>
            <div class="form-control bg-light">{{ $link->city }}</div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Location</label>
            <div class="form-control bg-light">{{ $link->suburb }}</div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Pop</label>
            <div class="form-control bg-light">{{ $link->pop }}</div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Link Type</label>
            <div class="form-control bg-light">{{ $link->linkType ?? '' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Link</label>
            <div class="form-control bg-light">{{ $link->link }}</div>
          </div>
          <div class="w-100"></div>
          <div class="col-md-4">
            <label class="form-label">JCC Number</label>
            <div class="form-control bg-light">{{ $link->jcc_number ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Service Type</label>
            <div class="form-control bg-light">{{ $link->service_type ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Capacity</label>
            <div class="form-control bg-light">{{ $link->capacity ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Contract Number</label>
            <div class="form-control bg-light">{{ $link->contract_number ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">SAP Codes</label>
            <div class="form-control bg-light">{{ $link->sapcodes ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Quantity</label>
            <div class="form-control bg-light">{{ $link->quantity ?? '—' }}</div>
          </div>
          <div class="col-md-12">
            <label class="form-label">Comment</label>
            <div class="form-control bg-light">{{ $link->comment ?? '—' }}</div>
              <div class="fs-6">{{ $link->comment ?? '—' }}</div>
            </div>
          </div>
        </div>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
@endcan
