<div class="modal custom-modal fade" id="referModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="referModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="referModalLabel-{{ $fault->id }}">Refer Fault To Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('my_faults.refer', $fault->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-select" required>
              <option value="" disabled selected>Select section</option>
              @foreach(($sections ?? collect()) as $section)
                <option value="{{ $section->id }}">{{ $section->section }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Work To Be Done</label>
            <textarea name="remark" class="form-control" rows="3" placeholder="Describe the work required" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-check me-1"></i>
            Refer
          </button>
        </div>
      </form>

      @if(isset($remarks) && count($remarks))
      <div class="px-3 pb-2">
        <div class="d-flex align-items-center mb-2">
          <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
          <h6 class="mb-0 text-secondary">Conversation</h6>
        </div>
        <div id="referRemarksScroller-{{ $fault->id }}" class="js-remarks-list legacy-remarks-list" style="max-height: 420px;">
          @foreach($remarks->sortBy('created_at') as $remark)
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
                @if($remark->file_path)
                  <div class="mt-2">
                    <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" class="img-fluid rounded" style="max-height: 160px; object-fit: cover;">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#ReferPicModal-{{ $remark->id }}">View</button>
                  </div>
                  <div class="modal custom-modal fade" id="ReferPicModal-{{ $remark->id }}" data-bs-backdrop="false" data-bs-keyboard="true" tabindex="-1" aria-labelledby="ReferPicModalLabel-{{ $remark->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                      <div class="modal-content rounded-4 border-0 shadow-lg">
                        <div class="modal-header border-0">
                          <h5 class="modal-title" id="ReferPicModalLabel-{{ $remark->id }}"><i class="fas fa-paperclip me-2"></i>Attachment</h5>
                          <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" class="img-fluid rounded">
                        </div>
                        <div class="modal-footer border-0">
                          <a href="{{ asset('storage/'.$remark->file_path) }}" class="btn btn-outline-primary" download><i class="fas fa-download me-1"></i>Download</a>
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
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
  var modalEl = document.getElementById('referModal-{{ $fault->id }}');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('referRemarksScroller-{{ $fault->id }}');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>
