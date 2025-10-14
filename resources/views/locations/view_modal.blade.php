@can('location-list')
@foreach($locations as $location)
<div class="modal fade js-location-view-modal" id="locationViewModal{{ $location->id }}" data-suburb-id="{{ $location->id }}" tabindex="-1" aria-labelledby="locationViewModalLabel{{ $location->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="locationViewModalLabel{{ $location->id }}">Location Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="row g-2">
            <div class="col-md-6">
              <strong>City/Town</strong>
              <div class="text-muted">{{ $location->city }}</div>
            </div>
            <div class="col-md-6">
              <strong>Location</strong>
              <div class="text-muted">{{ $location->suburb }}</div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">POPs for this Location</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th style="width:60px;">#</th>
                    <th>Pop</th>
                  </tr>
                </thead>
                <tbody id="viewPopsBody{{ $location->id }}">
                  <tr>
                    <td colspan="2" class="text-muted">Loading POPs...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan