@can('pop-edit')
@foreach($pops as $pop)
<div class="modal custom-modal fade" id="popEditModal{{ $pop->id }}" tabindex="-1" aria-labelledby="popEditModalLabel{{ $pop->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="popEditModalLabel{{ $pop->id }}"><i class="fas fa-pen-to-square me-2"></i>Edit POP</h5>
          <div class="text-muted small mt-1">Update the city, location, or POP label while keeping all dependent network mappings aligned.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-city"></i> {{ $pop->city }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-map-marker-alt"></i> {{ $pop->suburb }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-bullseye"></i> {{ $pop->pop }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('pops.update', $pop->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Changing the POP details affects connected link references, so keep the hierarchy accurate before saving.</div>
          </div>

          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-bullseye"></i></span>
              <div>
                <div class="fault-modal-section-title">POP Details</div>
                <div class="fault-modal-section-subtitle">Update the city, location, and POP label for this record.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="row g-3 mb-2">
                <div class="col-md-6">
                  <label class="form-label">City/Town</label>
                  <select id="popEditCity{{ $pop->id }}" class="form-select" name="city_id" required>
                    <option value="" disabled {{ empty($pop->city_id) ? 'selected' : '' }}>Select City/Town</option>
                    @foreach($cities as $city)
                      <option value="{{ $city->id }}" {{ isset($pop->city_id) && ($pop->city_id == $city->id) ? 'selected' : '' }}>{{ $city->city }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Location</label>
                  <select id="popEditSuburb{{ $pop->id }}" class="form-select" name="suburb_id" required>
                    <option value="" disabled {{ empty($pop->suburb_id) ? 'selected' : '' }}>Select Suburb</option>
                    @foreach($suburbs as $suburb)
                      <option value="{{ $suburb->id }}" data-city="{{ $suburb->city_id }}" {{ isset($pop->suburb_id) && ($pop->suburb_id == $suburb->id) ? 'selected' : '' }} style="{{ isset($pop->city_id) && ($suburb->city_id == $pop->city_id) ? '' : 'display:none;' }}">{{ $suburb->suburb }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-12">
                  <label class="form-label">POP</label>
                  <input type="text" class="form-control" name="pop" value="{{ $pop->pop }}" required>
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
            <i class="fas fa-save me-1"></i> Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var citySel = document.getElementById('popEditCity{{ $pop->id }}');
    var suburbSel = document.getElementById('popEditSuburb{{ $pop->id }}');
    function filterSuburbs() {
      if (!citySel || !suburbSel) return;
      var cityId = citySel.value;
      var options = suburbSel.querySelectorAll('option');
      var hasVisible = false;
      options.forEach(function(opt){
        var c = opt.getAttribute('data-city');
        if (!c) return;
        var visible = String(c) === String(cityId);
        opt.style.display = visible ? '' : 'none';
        if (visible) hasVisible = true;
      });
      if (!hasVisible) { suburbSel.value = ''; }
    }
    citySel && citySel.addEventListener('change', filterSuburbs);
    var modalEl = document.getElementById('popEditModal{{ $pop->id }}');
    modalEl && modalEl.addEventListener('shown.bs.modal', filterSuburbs);
  });
</script>
@endforeach
@endcan
