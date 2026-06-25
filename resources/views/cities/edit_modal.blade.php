@can('city-edit')
@foreach($cities as $city)
<div class="modal custom-modal fade" id="cityEditModal{{ $city->id }}" tabindex="-1" aria-labelledby="cityEditModalLabel{{ $city->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="cityEditModalLabel{{ $city->id }}"><i class="fas fa-pen-to-square me-2"></i>Edit City/Town</h5>
          <div class="text-muted small mt-1">Update the city name or region while keeping the network location structure aligned.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> {{ $city->city }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map"></i> {{ $city->region ?? 'Region Pending' }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('cities.update', $city->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changes here affect downstream location and POP mapping labels, so keep city and region names accurate.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-city"></i></span>
              <div>
                <div class="fault-modal-section-title">City Details</div>
                <div class="fault-modal-section-subtitle">Update the city/town label and the region it belongs to.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-3">
                <label class="form-label">City/Town</label>
                <input type="text" name="city" class="form-control" value="{{ $city->city }}" required>
              </div>
              <div class="mb-0">
                <label class="form-label">Region</label>
                <select name="region" class="form-select" required>
                  @if($city->region)
                    <option value="{{ $city->region }}" selected>{{ $city->region }}</option>
                  @else
                    <option value="" disabled selected>Select Region</option>
                  @endif
                  @unless($city->region === 'North')
                    <option value="North">North</option>
                  @endunless
                  @unless($city->region === 'West')
                    <option value="West">West</option>
                  @endunless
                  @unless($city->region === 'East')
                    <option value="East">East</option>
                  @endunless
                  @unless($city->region === 'South')
                    <option value="South">South</option>
                  @endunless
                </select>
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
