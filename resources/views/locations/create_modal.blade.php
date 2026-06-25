@can('location-create')
<div class="modal custom-modal fade" id="locationCreateModal" tabindex="-1" aria-labelledby="locationCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="locationCreateModalLabel"><i class="fas fa-map-marker-alt me-2"></i>Create Locations</h5>
          <div class="text-muted small mt-1">Select the city once, then add one or more locations using the refreshed network modal layout.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> City Linked</span>
            <span class="fault-modal-meta-item"><i class="fas fa-layer-group"></i> Bulk Create</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('locations.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Group related locations under the same city to keep POP and link mapping clean and consistent.</div>
          </div>

          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-city"></i></span>
              <div>
                <div class="fault-modal-section-title">Parent City</div>
                <div class="fault-modal-section-subtitle">Choose the city that should own the locations below.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">City/Town</label>
                  <select name="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                    <option value="" disabled selected>Select City/Town</option>
                    @foreach($cities as $c)
                      <option value="{{ $c->id }}">{{ $c->city }}</option>
                    @endforeach
                  </select>
                  @error('city_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-map-marker-alt"></i></span>
              <div>
                <div class="fault-modal-section-title">Location Entries</div>
                <div class="fault-modal-section-subtitle">Add each location that belongs to the selected city.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="repeater" id="locationRepeater">
                <div class="repeater-items">
                  <div class="repeater-item border rounded p-3 mb-3">
                    <div class="row g-3 align-items-end">
                      <div class="col-md-12">
                        <label class="form-label">Location</label>
                        <input type="text" name="items[0][suburb]" class="form-control @error('items.0.suburb') is-invalid @enderror" placeholder="Location" required>
                        @error('items.0.suburb')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addLocationRepeaterItem"><i class="fas fa-plus me-1"></i> Add another</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="removeLocationRepeaterItem"><i class="fas fa-minus me-1"></i> Remove last</button>
                </div>
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
@endcan
