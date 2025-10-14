@can('link-list')
<div class="modal fade" id="linkViewModal{{ $link->id }}" tabindex="-1" aria-labelledby="linkViewModalLabel{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="linkViewModalLabel{{ $link->id }}">Link Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col">
            <strong>Customer</strong>
            <p class="text-muted mb-0">{{ $link->customer }}</p>
          </div>
          <div class="col">
            <strong>City/Town</strong>
            <p class="text-muted mb-0">{{ $link->city }}</p>
          </div>
        </div>
        <div class="row g-2 mt-2">
          <div class="col">
            <strong>Location</strong>
            <p class="text-muted mb-0">{{ $link->suburb }}</p>
          </div>
          <div class="col">
            <strong>Pop</strong>
            <p class="text-muted mb-0">{{ $link->pop }}</p>
          </div>
        </div>
        <div class="mt-3">
          <strong>Link</strong>
          <p class="text-muted mb-0">{{ $link->link }}</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endcan