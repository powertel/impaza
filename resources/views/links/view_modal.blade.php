@can('link-list')
<div class="modal fade" id="linkViewModal{{ $link->id }}" tabindex="-1" aria-labelledby="linkViewModalLabel{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="linkViewModalLabel{{ $link->id }}">Link Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 align-items-end">
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
            <div class="form-control bg-light">{{ $link->linkType ?? $link->linkType ?? '' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Link</label>
            <div class="form-control bg-light">{{ $link->link }}</div>
          </div>
          <div class="w-100"></div>
          <div class="col-md-4">
            <div class="mb-3">
              <small class="text-muted">JCC Number</small>
              <div class="fs-6">{{ $link->jcc_number ?? '—' }}</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <small class="text-muted">Service Type</small>
              <div class="fs-6">{{ $link->service_type ?? '—' }}</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <small class="text-muted">Capacity</small>
              <div class="fs-6">{{ $link->capacity ?? '—' }}</div>
            </div>
          </div>
+
+          <div class="col-md-4">
+            <div class="mb-3">
+              <small class="text-muted">Contract Number</small>
+              <div class="fs-6">{{ $link->contract_number ?? '—' }}</div>
+            </div>
+          </div>
+          <div class="col-md-4">
+            <div class="mb-3">
+              <small class="text-muted">SAP Codes</small>
+              <div class="fs-6">{{ $link->sapcodes ?? '—' }}</div>
+            </div>
+          </div>
+          <div class="col-md-4">
+            <div class="mb-3">
+              <small class="text-muted">Quantity</small>
+              <div class="fs-6">{{ $link->quantity ?? '—' }}</div>
+            </div>
+          </div>
+          <div class="col-md-12">
+            <div class="mb-3">
+              <small class="text-muted">Comment</small>
+              <div class="fs-6">{{ $link->comment ?? '—' }}</div>
+            </div>
+          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endcan
