@can('noc-clear-faults-clear')
<div class="modal fade" id="nocClearModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="nocClearModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nocClearModalLabel-{{ $fault->id }}">Confirm Clear (NOC)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('noc-clear.update',$fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <p class="mb-2">Are you sure you want to mark this fault as <strong>Cleared by NOC</strong>?</p>
          <dl class="row mb-0">
            <dt class="col-sm-4">Customer</dt>
            <dd class="col-sm-8">{{ $fault->customer }}</dd>
            <dt class="col-sm-4">Link</dt>
            <dd class="col-sm-8">{{ $fault->link }}</dd>
          </dl>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Clear</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
