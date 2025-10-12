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
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i> Save</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endcan