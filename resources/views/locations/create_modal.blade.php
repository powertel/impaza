@can('location-create')
<div class="modal fade" id="locationCreateModal" tabindex="-1" aria-labelledby="locationCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="locationCreateModalLabel">Create Location(s)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('locations.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-3">Select a city once, then add one or more locations for that city.</p>

          <div class="row g-3 mb-3">
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
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-light btn-sm" id="addLocationRepeaterItem"><i class="fas fa-plus"></i> Add another</button>
              <button type="button" class="btn btn-light btn-sm" id="removeLocationRepeaterItem"><i class="fas fa-minus"></i> Remove last</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan