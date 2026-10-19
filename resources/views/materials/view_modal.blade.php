<div class="modal custom-modal fade" id="viewMaterialModal-{{ $material->id }}" tabindex="-1" aria-labelledby="viewMaterialModalLabel-{{ $material->id }}" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="viewMaterialModalLabel-{{ $material->id }}"><i class="fas fa-box me-2 text-secondary"></i>{{ $material->name }}</h5>
          @if($material->sku)
            <div class="small text-muted mt-1"><span class="font-monospace">{{ $material->sku }}</span></div>
          @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 small">
          <div class="col-6">
            <div class="text-muted">Category</div>
            <strong>{{ $material->category ?: '—' }}</strong>
          </div>
          <div class="col-6">
            <div class="text-muted">Unit</div>
            <strong>{{ $material->unit }}</strong>
          </div>
          <div class="col-6">
            <div class="text-muted">On Hand</div>
            <strong class="{{ $material->quantity_on_hand <= 0 ? 'text-danger' : ($material->isLowStock() ? 'text-warning' : 'text-success') }}">
              {{ number_format($material->quantity_on_hand, 2) }} {{ $material->unit }}
            </strong>
          </div>
          <div class="col-6">
            <div class="text-muted">Reorder Lvl</div>
            <strong>{{ number_format($material->reorder_level, 2) }} {{ $material->unit }}</strong>
          </div>
          <div class="col-6">
            <div class="text-muted">Status</div>
            @if(!$material->is_active)
              <span class="badge bg-secondary-subtle text-secondary border rounded-pill">Inactive</span>
            @elseif($material->quantity_on_hand <= 0)
              <span class="badge bg-danger-subtle text-danger-emphasis border rounded-pill">Out of Stock</span>
            @elseif($material->isLowStock())
              <span class="badge bg-warning-subtle text-warning-emphasis border rounded-pill">Low Stock</span>
            @else
              <span class="badge bg-success-subtle text-success-emphasis border rounded-pill">In Stock</span>
            @endif
          </div>
          <div class="col-6">
            <div class="text-muted">Created By</div>
            <strong>{{ optional($material->createdBy)->name ?: '—' }}</strong>
          </div>
          @if($material->description)
            <div class="col-12 border-top pt-2 mt-2">
              <div class="text-muted">Description</div>
              <div>{{ $material->description }}</div>
            </div>
          @endif
        </div>
      </div>
      <div class="modal-footer fault-modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
