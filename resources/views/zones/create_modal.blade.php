@can('technician-configuration')
<div class="modal custom-modal fade" id="zoneCreateModal" tabindex="-1" aria-labelledby="zoneCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="zoneCreateModalLabel"><i class="fas fa-map-marked-alt me-2"></i>Create Zone</h5>
          <div class="modal-subtitle">Define a new zone, assign its region, and map the POPs it should contain.</div>
        </div>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('zones.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="fault-modal-helper mb-3">
                <i class="fas fa-broadcast-tower"></i>
                <span>Select a region first to narrow the POP list, then pick the POPs that belong in the new zone.</span>
            </div>

            <div class="fault-modal-section">
                <div class="fault-modal-section-title">
                    <i class="fas fa-location-arrow"></i>
                    <span>Zone Setup</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Zone Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Zone A" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <select name="region" class="form-select" id="createZoneRegionSelect" onchange="filterCreatePops()">
                            <option value="">Select Region</option>
                            @foreach($regions as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select a region to filter the POPs list below.</small>
                    </div>
                </div>
            </div>

            <div class="fault-modal-section">
                <div class="fault-modal-section-title">
                    <i class="fas fa-network-wired"></i>
                    <span>POP Assignment</span>
                </div>
                <div class="mb-3">
                    <label class="form-label">POPs</label>
                    <input type="text" class="form-control mb-2" id="createZoneSearch" placeholder="Search POPs..." onkeyup="filterCreatePops()">
                    <div class="border rounded p-2" style="height: 300px; overflow-y: auto;" id="createPopsList">
                        <div class="row">
                        @foreach($pops as $pop)
                            <div class="col-6 pop-item" data-region="{{ $pop->region }}">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="pops[]" value="{{ $pop->id }}" id="create_pop_{{ $pop->id }}">
                                    <label class="form-check-label {{ $pop->zone_id ? 'text-danger' : '' }}" for="create_pop_{{ $pop->id }}">
                                        {{ $pop->pop }}
                                        @if($pop->zone_id) 
                                            <span class="small">({{ $pop->zone_name }})</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    <small class="text-muted">Select POPs to add to this zone. POPs already in another zone will be moved.</small>
                </div>
            </div>
            
            <script>
            function filterCreatePops() {
                var region = document.getElementById('createZoneRegionSelect').value;
                var searchText = document.getElementById('createZoneSearch').value.toLowerCase();
                var items = document.querySelectorAll('#createPopsList .pop-item');
                
                items.forEach(function(item) {
                    var itemRegion = item.getAttribute('data-region');
                    var label = item.querySelector('.form-check-label').textContent.toLowerCase();
                    
                    var regionMatch = (region === '' || itemRegion === region);
                    var searchMatch = (label.indexOf(searchText) > -1);

                    if (regionMatch && searchMatch) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            </script>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-success btn-sm">
            <i class="fas fa-save me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
