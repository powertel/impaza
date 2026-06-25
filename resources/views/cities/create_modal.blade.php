@can('city-create')
<div class="modal custom-modal fade" id="cityCreateModal" tabindex="-1" aria-labelledby="cityCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="cityCreateModalLabel"><i class="fas fa-city me-2"></i>Create Cities</h5>
          <div class="text-muted small mt-1">Add one or more cities and map each one to the correct region using the refreshed network workspace modal.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-globe-africa"></i> Region Mapping</span>
            <span class="fault-modal-meta-item"><i class="fas fa-layer-group"></i> Bulk Create</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('cities.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Add multiple city records in one go and keep each one aligned to the correct region for network planning.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-city"></i></span>
              <div>
                <div class="fault-modal-section-title">City Entries</div>
                <div class="fault-modal-section-subtitle">Provide the city name and assign the matching region for each row.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="repeater" id="cityRepeater">
                <div class="repeater-items">
                  <div class="repeater-item border rounded p-3 mb-3">
                    <div class="row g-3 align-items-end">
                      <div class="col-md-6">
                        <label class="form-label">City/Town</label>
                        <input type="text" name="items[0][city]" class="form-control" placeholder="e.g. Harare" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <select name="items[0][region]" class="form-select" required>
                          <option value="" disabled selected>Select Region</option>
                          <option value="North">North</option>
                          <option value="West">West</option>
                          <option value="East">East</option>
                          <option value="South">South</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="addCityRepeaterItem"><i class="fas fa-plus me-1"></i> Add another</button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="removeCityRepeaterItem"><i class="fas fa-minus me-1"></i> Remove last</button>
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
