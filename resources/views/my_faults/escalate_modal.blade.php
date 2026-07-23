@php
  $latestRemarkForEscalation = collect($remarks ?? [])->sortByDesc('created_at')->first();
@endphp
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
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Switch</label>
              <input type="text" name="switch_name" class="form-control" value="{{ old('switch_name', $latestRemarkForEscalation->switch_name ?? '') }}" placeholder="Enter switch name or identifier">
            </div>
            <div class="col-md-6">
              <label class="form-label">Port</label>
              <input type="text" name="port" class="form-control" value="{{ old('port', $latestRemarkForEscalation->port ?? '') }}" placeholder="Enter port number or label">
            </div>
            <div class="col-md-12">
              <label class="form-label">Reason / Context</label>
              <textarea name="remark" class="form-control" rows="3" placeholder="Provide context for escalation" required></textarea>
            </div>
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
        <div id="escalateRemarksScroller-{{ $fault->id }}" class="js-remarks-list legacy-remarks-list" style="max-height: 420px;">
          @foreach(($remarks ?? collect())->sortBy('created_at') as $remark)
            @php
              $currentName = optional(auth()->user())->name;
              $isOwn = $currentName && (strtolower(trim($remark->name)) === strtolower(trim($currentName)));
            @endphp
            <div class="legacy-remark-row {{ $isOwn ? 'legacy-remark-row-self' : 'legacy-remark-row-other' }}">
              <div class="legacy-remark-bubble {{ $isOwn ? 'legacy-remark-bubble-self' : 'legacy-remark-bubble-other' }}">
                <div class="legacy-remark-meta">
                  <span class="badge {{ $isOwn ? 'bg-success' : 'bg-secondary' }}">{{ $remark->name ?? 'User' }}</span>
                  <small class="text-muted">{{ Carbon\Carbon::parse($remark->created_at)->diffForHumans() }}</small>
                  @if(!empty($remark->activity))
                    <small class="text-muted">• {{ $remark->activity }}</small>
                  @endif
                </div>
                <div class="legacy-remark-body">{{ $remark->remark }}</div>
                @if(!empty($remark->switch_name) || !empty($remark->port))
                  <div class="mt-2 d-flex flex-wrap gap-2">
                    @if(!empty($remark->switch_name))
                      <span class="badge rounded-pill bg-light text-dark border">Switch: {{ $remark->switch_name }}</span>
                    @endif
                    @if(!empty($remark->port))
                      <span class="badge rounded-pill bg-light text-dark border">Port: {{ $remark->port }}</span>
                    @endif
                  </div>
                @endif
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

