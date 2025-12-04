@can('chief-tech-clear-faults-clear')
<div class="modal fade" id="referModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="referModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="referModalLabel-{{ $fault->id }}">Refer Escalation to Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('chief-tech-escalations.refer', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-select" required>
              @foreach(\App\Models\Section::orderBy('section')->get(['id','section']) as $s)
                <option value="{{ $s->id }}">{{ $s->section }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-save me-1"></i> Refer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

