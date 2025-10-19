<div class="modal fade" id="financeEditModal-{{ $link->id }}" tabindex="-1" aria-labelledby="financeEditModalLabel-{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="financeEditModalLabel-{{ $link->id }}">Confirm Link</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('finance.update', $link->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-2">
            <small class="text-muted">Customer</small>
            <div>{{ $link->customer }}</div>
          </div>
          <div class="mb-2">
            <small class="text-muted">Link</small>
            <div>{{ $link->link }}</div>
          </div>
          <div class="mb-3">
            <label for="contract_number_{{ $link->id }}" class="form-label">Contract Number</label>
            <input id="contract_number_{{ $link->id }}" name="contract_number" type="text" class="form-control @error('contract_number') is-invalid @enderror" value="{{ $link->contract_number }}" placeholder="Enter Contract Number" required>
          </div>
          <p class="text-muted mb-0">Saving will approve and set status to Connected.</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()"><i class="fas fa-save me-1"></i> Save</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>