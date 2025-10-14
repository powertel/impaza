@can('location-edit')
@foreach($locations as $location)
<div class="modal fade" id="locationEditModal{{ $location->id }}" tabindex="-1" aria-labelledby="locationEditModalLabel{{ $location->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="locationEditModalLabel{{ $location->id }}">Edit Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('locations.update', $location->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">City/Town</label>
            <select name="city_id" class="form-select" required>
              <option value="" disabled>Select City/Town</option>
              @foreach($cities as $c)
                <option value="{{ $c->id }}" {{ (isset($location->city_id) && $location->city_id == $c->id) ? 'selected' : '' }}>{{ $c->city }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="suburb" class="form-control" value="{{ $location->suburb }}" required>
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
@endforeach
@endcan