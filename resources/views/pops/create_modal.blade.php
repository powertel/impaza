<div class="modal fade" id="popCreateModal" tabindex="-1" aria-labelledby="popCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="popCreateModalLabel">Create POPs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="js-pops-create-form" action="{{ route('pops.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3 mb-2">
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
              <button type="button" class="btn btn-outline-secondary btn-sm js-repeater-add">Add Pop</button>
              <small class="text-muted">Each row creates one POP at the selected location</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
