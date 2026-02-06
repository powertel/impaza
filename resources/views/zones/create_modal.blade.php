@can('technician-configuration')
<div class="modal fade" id="zoneCreateModal" tabindex="-1" aria-labelledby="zoneCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="zoneCreateModalLabel">Create Zone</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('zones.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Zone Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Zone A" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Region</label>
                <select name="region" class="form-select" id="createZoneRegionSelect" onchange="filterCreatePops()">
                    <option value="">Select Region</option>
                    @foreach($regions as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Select a region to filter the POPs list below.</small>
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
