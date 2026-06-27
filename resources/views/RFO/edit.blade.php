<!-- Modal -->
<div class="modal custom-modal fade" id="editRfoModal{{ $rfo->id }}" tabindex="-1" aria-labelledby="editRfoModalLabel{{ $rfo->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('rfos.update', $rfo->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <div class="fault-modal-header-copy">
                        <h5 class="modal-title" id="editRfoModalLabel{{ $rfo->id }}"><i class="fas fa-pen me-2"></i>Edit Reason For Outage</h5>
                        <div class="text-muted small mt-1">Update this outage reason wherever it appears in the fault workflow.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fault-modal-note mb-3">
                        <i class="fas fa-circle-info"></i>
                        <div>Keep the wording concise so the label stays readable in forms, filters, and reports.</div>
                    </div>
                    <div class="fault-modal-section">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-list-check"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Reason Details</div>
                                <div class="fault-modal-section-subtitle">Edit the outage reason text below.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <label for="RFO{{ $rfo->id }}" class="form-label">Reason For Outage</label>
                            <input type="text" class="form-control @error('RFO') is-invalid @enderror" id="RFO{{ $rfo->id }}" name="RFO" value="{{ old('RFO', $rfo->RFO) }}" required>
                            @error('RFO')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer fault-modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-save"></i>
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
