<div class="modal custom-modal fade" id="escalateModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="escalateModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="escalateModalLabel-{{ $fault->id }}">Escalate Fault to Chief Technician</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('my_faults.escalate', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Reason / Context</label>
            <textarea name="remark" class="form-control" rows="3" placeholder="Provide context for escalation" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="fas fa-level-up-alt me-1"></i>Escalate
          </button>
        </div>
      </form>

      @if(isset($remarks) && count($remarks))
      <div class="px-3 pb-2">
        <div class="d-flex align-items-center mb-2">
          <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
          <h6 class="mb-0 text-secondary">Conversation</h6>
        </div>
        <div id="escalateRemarksScroller-{{ $fault->id }}" class="js-remarks-list" style="max-height: 420px; overflow-y: auto; padding-right: 6px;">
          @foreach(($remarks ?? collect())->sortBy('created_at') as $remark)
            @php
              $currentName = optional(auth()->user())->name;
              $isOwn = $currentName && (strtolower(trim($remark->name)) === strtolower(trim($currentName)));
            @endphp
            <div class="d-flex {{ $isOwn ? 'justify-content-end' : 'justify-content-start' }} mb-3">
              <div class="rounded-3 shadow-sm px-3 py-2" style="max-width: 75%; background-color: {{ $isOwn ? '#e8f5e9' : '#eef5ff' }};">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <span class="badge {{ $isOwn ? 'bg-success' : 'bg-secondary' }}">{{ $remark->name ?? 'User' }}</span>
                  <small class="text-muted">{{ Carbon\Carbon::parse($remark->created_at)->diffForHumans() }}</small>
                  @if(!empty($remark->activity))
                    <small class="text-muted">• {{ $remark->activity }}</small>
                  @endif
                </div>
                <div class="fw-normal">{{ $remark->remark }}</div>
                @if(!empty($remark->file_path))
                  <div class="mt-2">
                    <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" class="img-fluid rounded" style="max-height: 160px; object-fit: cover;">
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('escalateModal-{{ $fault->id }}');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('escalateRemarksScroller-{{ $fault->id }}');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>

