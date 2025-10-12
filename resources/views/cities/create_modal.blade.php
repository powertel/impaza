@can('city-create')
<div class="modal fade" id="cityCreateModal" tabindex="-1" aria-labelledby="cityCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cityCreateModalLabel">Create City/Town(s)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('cities.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-3">Add one or more cities/towns. Use “Add another” to insert additional rows.</p>
          <div class="repeater" id="cityRepeater">
            <div class="repeater-items">
              <div class="repeater-item border rounded p-3 mb-3">
                <div class="row g-3 align-items-end">
                  <div class="col-12">
                    <label class="form-label">City/Town</label>
                    <input type="text" name="items[0][city]" class="form-control" placeholder="e.g. Lusaka" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-light btn-sm" id="addCityRepeaterItem"><i class="fas fa-plus"></i> Add another</button>
              <button type="button" class="btn btn-light btn-sm" id="removeCityRepeaterItem"><i class="fas fa-minus"></i> Remove last</button>
            </div>
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
@endcan