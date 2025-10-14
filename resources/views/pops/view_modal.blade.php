@foreach($pops as $pop)
<div class="modal fade" id="popViewModal{{ $pop->id }}" tabindex="-1" aria-labelledby="popViewModalLabel{{ $pop->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="popViewModalLabel{{ $pop->id }}">POP Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-2">
            <div class="col">
                <strong>City/Town</strong>
                <p class="text-muted mb-0">{{ $pop->city }}</p>
            </div>
            <div class="col">
                <strong>Location</strong>
                <p class="text-muted mb-0">{{ $pop->suburb }}</p>
            </div>
        </div>
        <div class="row g-2">
            <div class="col">
                <strong>POP</strong>
                <p class="text-muted mb-0">{{ $pop->pop }}</p>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach