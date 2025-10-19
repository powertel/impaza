<div class="modal fade" id="financeViewModal-{{ $link->id }}" tabindex="-1" aria-labelledby="financeViewModalLabel-{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="financeViewModalLabel-{{ $link->id }}">View Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <strong>Customer</strong>
            <p class="text-muted mb-0">{{ $link->customer }}</p>
          </div>
          <div class="col-md-6">
            <strong>City/Town</strong>
            <p class="text-muted mb-0">{{ $link->city }}</p>
          </div>
        </div>
        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <strong>Location</strong>
            <p class="text-muted mb-0">{{ $link->suburb }}</p>
          </div>
          <div class="col-md-6">
            <strong>POP</strong>
            <p class="text-muted mb-0">{{ $link->pop }}</p>
          </div>
        </div>
        <div class="row g-3 mt-2">
          <div class="col-md-12">
            <strong>Link</strong>
            <p class="text-muted mb-0">{{ $link->link }}</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>