@can('materials')
<div class="modal fade" id="requestMaterialCreateModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="requestMaterialCreateModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="requestMaterialCreateModalLabel-{{ $fault->id }}">Request Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('stores.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Sap Ref. No.</label>
            <input type="text" class="form-control" name="sap-ref-no" placeholder="Enter SAP reference">
          </div>
          <div class="mb-3">
            <label class="form-label">Material</label>
            <input type="text" class="form-control" name="request-material" placeholder="Enter material">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Request</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan