<div class="modal custom-modal fade" id="popCreateModal" tabindex="-1" aria-labelledby="popCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="popCreateModalLabel"><i class="fas fa-bullseye me-2"></i>Create POPs</h5>
          <div class="text-muted small mt-1">Select the city and location, then add one or more POP entries using the updated modern network modal.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> City Linked</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map-marker-alt"></i> Location Linked</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js-pops-create-form" action="{{ route('pops.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Create multiple POPs for the selected location in one pass to speed up network configuration work.</div>
          </div>

          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-diagram-project"></i></span>
              <div>
                <div class="fault-modal-section-title">Location Mapping</div>
                <div class="fault-modal-section-subtitle">Choose the city and location that should own the POP entries below.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">City/Town</label>
                  <select id="popCreateCity" class="form-select" name="city_id" required>
                    <option value="" disabled selected>Select City/Town</option>
                    @foreach($cities as $city)
                      <option value="{{ $city->id }}">{{ $city->city }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Location</label>
                  <select id="popCreateSuburb" class="form-select" name="suburb_id" required>
                    <option value="" disabled selected>Select Suburb</option>
                    @foreach($suburbs as $suburb)
                      <option value="{{ $suburb->id }}" data-city="{{ $suburb->city_id }}">{{ $suburb->suburb }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-bullseye"></i></span>
              <div>
                <div class="fault-modal-section-title">POP Entries</div>
                <div class="fault-modal-section-subtitle">List the POP names that belong to the selected location.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="card">
                <div class="card-header py-2">
                  <strong>POPs for this Location</strong>
                </div>
                <div class="card-body p-0">
                  <div class="js-repeater-pops">
                    <div class="list-group list-group-flush js-repeater-list">
                      <div class="list-group-item d-flex align-items-center gap-2 js-repeater-item">
                        <div class="flex-grow-1">
                          <input type="text" class="form-control" name="items[0][pop]" placeholder="Pop name" required>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger js-repeater-remove" title="Remove">&times;</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                  <button type="button" class="btn btn-outline-primary btn-sm js-repeater-add">Add Pop</button>
                  <small class="text-muted">Each row creates one POP at the selected location</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
