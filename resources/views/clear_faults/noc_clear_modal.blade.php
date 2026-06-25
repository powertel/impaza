@can('noc-clear-faults-clear')
<div class="modal custom-modal fade" id="nocClearModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="nocClearModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="nocClearModalLabel-{{ $fault->id }}"><i class="fas fa-check-circle me-2"></i>Confirm Clear</h5>
          <div class="text-muted small mt-1">Close {{ $fault->fault_ref_number ?? 'this fault' }} from the NOC queue after reviewing the latest updates.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-user"></i> {{ $fault->customer ?? 'N/A' }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-link"></i> {{ $fault->link ?? 'N/A' }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('noc-clear.update',$fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Confirm the service has been restored, capture the final NOC note, and keep the conversation trail visible before closing the fault.</div>
          </div>

          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-clipboard-list"></i></span>
              <div>
                <div class="fault-modal-section-title">Clearance Summary</div>
                <div class="fault-modal-section-subtitle">Quick fault context before completing the NOC clearance step.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="fault-modal-grid">
                <div class="fault-modal-kv">
                  <span class="fault-modal-kv-label">Customer</span>
                  <div class="fault-modal-kv-value">{{ $fault->customer ?? 'N/A' }}</div>
                </div>
                <div class="fault-modal-kv">
                  <span class="fault-modal-kv-label">Link</span>
                  <div class="fault-modal-kv-value">{{ $fault->link ?? 'N/A' }}</div>
                </div>
                <div class="fault-modal-kv">
                  <span class="fault-modal-kv-label">Action</span>
                  <div class="fault-modal-kv-value">Move fault to <span class="badge rounded-pill bg-secondary-subtle text-secondary border">Cleared by NOC</span></div>
                </div>
              </div>
            </div>
          </div>

          @if(isset($remarks) && count($remarks))
          <div class="mt-4">
            <div class="d-flex align-items-center mb-2">
              <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
              <h6 class="mb-0 text-secondary">Conversation</h6>
            </div>
            <!-- Scrollable chat-style conversation -->
            <div id="nocClearRemarksScroller-{{ $fault->id }}" class="js-remarks-list legacy-remarks-list" style="max-height: 420px;">
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
                        <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#nocClearPicModal-{{ $remark->id }}">View</button>
                      </div>
                      <!-- Remark Attachment Modal -->
                      <div class="modal custom-modal fade" id="nocClearPicModal-{{ $remark->id }}" data-bs-backdrop="false" data-bs-keyboard="true" tabindex="-1" aria-labelledby="nocClearPicModalLabel-{{ $remark->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                          <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0">
                              <h5 class="modal-title" id="nocClearPicModalLabel-{{ $remark->id }}"><i class="fas fa-paperclip me-2"></i>Attachment</h5>
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

          <div class="mt-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="3" placeholder="Enter remark to clear fault..." required></textarea>
            <input type="hidden" name="activity" value="ON NOC CLEAR">
          </div>

          <p class="mt-3 mb-0 text-danger">Are you sure you want to mark this fault as <strong>Cleared by NOC</strong>?</p>
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-success btn-sm rounded-pill">
            <i class="fas fa-save me-1"></i> Clear
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('nocClearModal-{{ $fault->id }}');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('nocClearRemarksScroller-{{ $fault->id }}');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>
