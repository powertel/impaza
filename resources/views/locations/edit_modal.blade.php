@can('location-edit')
@foreach($locations as $location)
<div class="modal custom-modal fade" id="locationEditModal{{ $location->id }}" tabindex="-1" aria-labelledby="locationEditModalLabel{{ $location->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="locationEditModalLabel{{ $location->id }}"><i class="fas fa-pen-to-square me-2"></i>Edit Location</h5>
          <div class="text-muted small mt-1">Update the city mapping or location name while keeping the network structure synchronized.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> {{ $location->city }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map-marker-alt"></i> {{ $location->suburb }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('locations.update', $location->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changes here affect how POPs and links reference this location, so keep the city and location labels consistent.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-map-marker-alt"></i></span>
              <div>
                <div class="fault-modal-section-title">Location Details</div>
                <div class="fault-modal-section-subtitle">Update the city mapping and the human-readable location label.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-3">
                <label class="form-label">City/Town</label>
                <select name="city_id" class="form-select" required>
                  <option value="" disabled>Select City/Town</option>
                  @foreach($cities as $c)
                    <option value="{{ $c->id }}" {{ (isset($location->city_id) && $location->city_id == $c->id) ? 'selected' : '' }}>{{ $c->city }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-0">
                <label class="form-label">Location</label>
                <input type="text" name="suburb" class="form-control" value="{{ $location->suburb }}" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endcan
