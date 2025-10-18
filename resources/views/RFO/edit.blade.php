<!-- Modal -->
<div class="modal fade" id="editRfoModal{{ $rfo->id }}" tabindex="-1" aria-labelledby="editRfoModalLabel{{ $rfo->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rfos.update', $rfo->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editRfoModalLabel{{ $rfo->id }}">Edit Reason For Outage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="RFO{{ $rfo->id }}" class="form-label">Reason For Outage</label>
                        <input type="text" class="form-control" id="RFO{{ $rfo->id }}" name="RFO" value="{{ $rfo->RFO }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>