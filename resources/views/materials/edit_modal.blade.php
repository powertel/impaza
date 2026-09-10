<div class="modal custom-modal fade" id="editMaterialModal-{{ $material->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editMaterialModalLabel-{{ $material->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('materials.update', $material->id) }}" method="POST" id="editMaterialForm-{{ $material->id }}" novalidate>
        @csrf @method('PUT')
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0" id="editMaterialModalLabel-{{ $material->id }}"><i class="fas fa-pencil me-2 text-primary"></i>Edit Material</h5>
            <div class="text-muted small mt-1">Update catalogue details for {{ $material->name }}.</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label small fw-semibold">Name *</label>
              <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name', $material->name) }}" required autocomplete="off">
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold">SKU</label>
              <input type="text" class="form-control form-control-sm" name="sku" value="{{ old('sku', $material->sku) }}" autocomplete="off">
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold">Category</label>
              <input type="text" class="form-control form-control-sm" name="category" list="categoryListEdit-{{ $material->id }}" value="{{ old('category', $material->category) }}" autocomplete="off">
              <datalist id="categoryListEdit-{{ $material->id }}">
                <option value="Cable"></option>
                <option value="Connectors"></option>
                <option value="Splicing"></option>
                <option value="Patch Panels"></option>
                <option value="Tools"></option>
                <option value="Cabinets & Racks"></option>
                <option value="Consumables"></option>
                @foreach(($categories ?? collect()) as $c)
                  <option value="{{ $c }}"></option>
                @endforeach
              </datalist>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Unit *</label>
              <input type="text" class="form-control form-control-sm" name="unit" value="{{ old('unit', $material->unit) }}" required autocomplete="off">
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Qty on Hand *</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="quantity_on_hand" value="{{ old('quantity_on_hand', $material->quantity_on_hand) }}" required>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-semibold">Reorder Lvl</label>
              <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="reorder_level" value="{{ old('reorder_level', $material->reorder_level) }}">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Description</label>
              <textarea class="form-control form-control-sm" name="description" rows="2">{{ old('description', $material->description) }}</textarea>
            </div>
            <div class="col-12">
              <input type="hidden" name="is_active" value="0">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="matEditActive-{{ $material->id }}" value="1" {{ old('is_active', $material->is_active ? 1 : 0) ? 'checked' : '' }}>
                <label class="form-check-label small" for="matEditActive-{{ $material->id }}">Active in catalogue</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm rounded-pill mat-edit-submit" data-form-id="editMaterialForm-{{ $material->id }}">
            <i class="fas fa-save me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
