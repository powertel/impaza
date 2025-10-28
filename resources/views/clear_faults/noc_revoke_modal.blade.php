@can('noc-clear-faults-clear')
<div class="modal fade" id="nocRevokeModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="nocRevokeModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nocRevokeModalLabel-{{ $fault->id }}">Confirm Revoke (NOC)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('noc-clear.revoke',$fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <p class="mb-2">Are you sure you want to <strong>revoke</strong> this fault back to Technician for rework?</p>
          <dl class="row mb-0">
            <dt class="col-sm-4">Customer</dt>
            <dd class="col-sm-8">{{ $fault->customer }}</dd>
            <dt class="col-sm-4">Link</dt>
            <dd class="col-sm-8">{{ $fault->link }}</dd>
          </dl>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-undo me-1"></i> Revoke
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
