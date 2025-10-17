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
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endcan
