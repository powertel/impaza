@can('assign-fault')
<div class="modal fade" id="assignModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="assignModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignModalLabel-{{ $fault->id }}">Assign Fault</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('assign.update', $fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-2">
            <small class="text-muted">Customer</small>
            <div>{{ $fault->customer }}</div>
          </div>
          <div class="mb-2">
            <small class="text-muted">Link</small>
            <div>{{ $fault->link }}</div>
          </div>
          <div class="mb-3">
            <small class="text-muted">Currently Assigned To</small>
            <div>{{ $fault->name ?? '—' }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign To</label>
            <select name="assignedTo" class="form-select" required>
              <option value="">Select technician</option>
              @foreach($technicians as $tech)
                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Assign</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan