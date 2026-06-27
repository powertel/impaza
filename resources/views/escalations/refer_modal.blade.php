<div class="modal custom-modal fade" id="referModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="referModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="referModalLabel-{{ $fault->id }}"><i class="fas fa-share-square me-2"></i>Refer to Section</h5>
          <div class="text-muted small mt-1">Route {{ $fault->fault_ref_number ?? 'this fault' }} to the correct section with a handoff note.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.refer', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Select the section that should take over this escalation and leave a note so the receiving team has context.</div>
          </div>
          <div class="fault-modal-section">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-diagram-project"></i></span>
              <div>
                <div class="fault-modal-section-title">Referral Details</div>
                <div class="fault-modal-section-subtitle">Choose the target section and explain the handoff.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="mb-3">
                <label class="form-label">Section</label>
                <select name="section_id" class="form-select" required>
                  @foreach(\App\Models\Section::orderBy('section')->get(['id','section']) as $s)
                    <option value="{{ $s->id }}">{{ $s->section }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-0">
                <label class="form-label">Remark</label>
                <textarea name="remark" class="form-control" rows="3" required></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="fas fa-save me-1"></i> Refer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

