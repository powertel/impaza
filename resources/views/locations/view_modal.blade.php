@can('location-list')
@foreach($locations as $location)
<div class="modal custom-modal fade js-location-view-modal" id="locationViewModal{{ $location->id }}" data-suburb-id="{{ $location->id }}" tabindex="-1" aria-labelledby="locationViewModalLabel{{ $location->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="locationViewModalLabel{{ $location->id }}"><i class="fas fa-eye me-2"></i>Location Details</h5>
          <div class="text-muted small mt-1">Review the location details and the POPs currently mapped to this network area.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> {{ $location->city }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map-marker-alt"></i> {{ $location->suburb }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
          <i class="fas fa-circle-info"></i>
          <div>This summary helps confirm that POPs are mapped to the correct city and location before making edits.</div>
        </div>

        <div class="fault-modal-section mb-3">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-map-marker-alt"></i></span>
            <div>
              <div class="fault-modal-section-title">Location Overview</div>
              <div class="fault-modal-section-subtitle">Core geographic details for this network location.</div>
            </div>
          </div>
          <div class="fault-modal-section-body">
            <div class="fault-modal-grid">
              <div class="fault-modal-kv">
                <span class="fault-modal-kv-label">City/Town</span>
                <div class="fault-modal-kv-value">{{ $location->city }}</div>
              </div>
              <div class="fault-modal-kv">
                <span class="fault-modal-kv-label">Location</span>
                <div class="fault-modal-kv-value">{{ $location->suburb }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="fault-modal-section">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-bullseye"></i></span>
            <div>
              <div class="fault-modal-section-title">POPs for this Location</div>
              <div class="fault-modal-section-subtitle">Active POP records connected to this location.</div>
            </div>
          </div>
          <div class="fault-modal-section-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0">
                <thead>
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
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan
