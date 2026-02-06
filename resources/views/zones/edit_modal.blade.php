@foreach ($zones as $zone)
@can('technician-configuration')
<div class="modal fade" id="zoneEditModal{{ $zone->id }}" tabindex="-1" aria-labelledby="zoneEditModalLabel{{ $zone->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="zoneEditModalLabel{{ $zone->id }}">Edit Zone: {{ $zone->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('zones.update', $zone->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Zone Name</label>
                <input type="text" name="name" class="form-control" value="{{ $zone->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Region</label>
                <select name="region" class="form-select" id="editZoneRegionSelect{{ $zone->id }}" onchange="filterEditPops({{ $zone->id }})">
                    <option value="">Select Region</option>
                    @foreach($regions as $r)
                        <option value="{{ $r }}" {{ $zone->region == $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Select a region to filter the POPs list below.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">POPs</label>
                <input type="text" class="form-control mb-2" id="editZoneSearch{{ $zone->id }}" placeholder="Search POPs..." onkeyup="filterEditPops({{ $zone->id }})">
                <div class="border rounded p-2" style="height: 300px; overflow-y: auto;" id="editPopsList{{ $zone->id }}">
                    <div class="row">
                    @foreach($pops as $pop)
                        <div class="col-6 pop-item" data-region="{{ $pop->region }}" style="{{ ($zone->region && $pop->region != $zone->region) ? 'display: none;' : '' }}">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="pops[]" value="{{ $pop->id }}" 
                                    id="edit_pop_{{ $zone->id }}_{{ $pop->id }}"
                                    {{ $pop->zone_id == $zone->id ? 'checked' : '' }}>
                                <label class="form-check-label {{ ($pop->zone_id && $pop->zone_id != $zone->id) ? 'text-danger' : '' }}" for="edit_pop_{{ $zone->id }}_{{ $pop->id }}">
                                    {{ $pop->pop }}
                                    @if($pop->zone_id && $pop->zone_id != $zone->id) 
                                        <span class="small">({{ $pop->zone_name }})</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
                <small class="text-muted">Select POPs to include in this zone. Unchecking removes them.</small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-success btn-sm">
            <i class="fas fa-save me-1"></i> Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
@endforeach

<script>
function filterEditPops(zoneId) {
    var region = document.getElementById('editZoneRegionSelect' + zoneId).value;
    var searchText = document.getElementById('editZoneSearch' + zoneId).value.toLowerCase();
    var items = document.querySelectorAll('#editPopsList' + zoneId + ' .pop-item');
    
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
