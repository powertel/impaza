@can('city-edit')
@foreach($cities as $city)
<div class="modal fade" id="cityEditModal{{ $city->id }}" tabindex="-1" aria-labelledby="cityEditModalLabel{{ $city->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cityEditModalLabel{{ $city->id }}">Edit City/Town</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('cities.update', $city->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">City/Town</label>
            <input type="text" name="city" class="form-control" value="{{ $city->city }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Region</label>
            <select name="region" class="form-select" required>
              @if($city->region)
                <option value="{{ $city->region }}" selected>{{ $city->region }}</option>
              @else
                <option value="" disabled selected>Select Region</option>
              @endif
              @unless($city->region === 'North')
                <option value="North">North</option>
              @endunless
              @unless($city->region === 'West')
                <option value="West">West</option>
              @endunless
              @unless($city->region === 'East')
                <option value="East">East</option>
              @endunless
              @unless($city->region === 'South')
                <option value="South">South</option>
              @endunless
            </select>
          </div>
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
@endforeach
@endcan
