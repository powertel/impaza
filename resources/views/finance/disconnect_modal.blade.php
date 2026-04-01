<div class="modal fade" id="financeDisconnectModal-{{ $link->id }}" tabindex="-1" aria-labelledby="financeDisconnectModalLabel-{{ $link->id }}" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="financeDisconnectModalLabel-{{ $link->id }}">Disconnect Link</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('disconnect', $link->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <p class="mb-1">Are you sure you want to disconnect this link?</p>
          <ul class="mb-0 text-muted">
            <li><strong>Customer:</strong> {{ $link->customer }}</li>
            <li><strong>Link:</strong> {{ $link->link }}</li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-sm" onclick="return submitResult()"><i class="fas fa-plug me-1"></i> Disconnect</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>