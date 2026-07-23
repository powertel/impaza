@php
  $latestRemarkForCompleteReferral = collect($remarks ?? [])->sortByDesc('created_at')->first();
@endphp
<div class="modal custom-modal fade" id="completeReferralModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="completeReferralModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="completeReferralModalLabel-{{ $fault->id }}"><i class="fas fa-check me-2"></i>Complete Referral</h5>
          <div class="text-muted small mt-1">Close the section referral with a completion note and review the conversation before finalizing.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('referrals.complete', $fault->referral_id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="fault-modal-note mb-3">
            <i class="fas fa-circle-info"></i>
            <div>Summarize the work completed by your section so the handoff trail remains clear in the fault history.</div>
          </div>
          <div class="fault-modal-section mb-3">
            <div class="fault-modal-section-header">
              <span class="fault-modal-section-icon"><i class="fas fa-clipboard-check"></i></span>
              <div>
                <div class="fault-modal-section-title">Completion Note</div>
                <div class="fault-modal-section-subtitle">Record what was done before closing the referral.</div>
              </div>
            </div>
            <div class="fault-modal-section-body">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label">Switch</label>
                  <input type="text" name="switch_name" class="form-control" value="{{ old('switch_name', $latestRemarkForCompleteReferral->switch_name ?? '') }}" placeholder="Enter switch name or identifier">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Port</label>
                  <input type="text" name="port" class="form-control" value="{{ old('port', $latestRemarkForCompleteReferral->port ?? '') }}" placeholder="Enter port number or label">
                </div>
                <div class="col-md-12">
                  <label class="form-label">Completion Remark</label>
                  <textarea name="remark" class="form-control" rows="3" placeholder="Enter completion notes" required></textarea>
                </div>
              </div>
            </div>
          </div>
          @if(isset($remarks) && count($remarks))
          <div class="px-1 pb-1">
            <div class="d-flex align-items-center mb-2">
              <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
              <h6 class="mb-0 text-secondary">Conversation</h6>
            </div>
            <div id="completeReferralRemarksScroller-{{ $fault->id }}" class="js-remarks-list legacy-remarks-list" style="max-height: 320px;">
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
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @endif
        </div>
        <div class="modal-footer fault-modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
           <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill">
            <i class="fas fa-check me-1"></i>
            Complete
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('completeReferralModal-{{ $fault->id }}');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('completeReferralRemarksScroller-{{ $fault->id }}');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>
