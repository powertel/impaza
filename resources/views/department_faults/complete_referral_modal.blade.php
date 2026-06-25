<div class="modal custom-modal fade" id="completeReferralModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="completeReferralModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="completeReferralModalLabel-{{ $fault->id }}">Complete Referral</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('referrals.complete', $fault->referral_id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Completion Remark</label>
            <textarea name="remark" class="form-control" rows="3" placeholder="Enter completion notes" required></textarea>
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
                <div class="d-flex {{ $isOwn ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                  <div class="legacy-remark-bubble {{ $isOwn ? 'legacy-remark-bubble-self' : 'legacy-remark-bubble-other' }}">
                    <div class="legacy-remark-meta">
                      <span class="badge {{ $isOwn ? 'bg-success' : 'bg-secondary' }}">{{ $remark->name ?? 'User' }}</span>
                      <small class="text-muted">{{ Carbon\Carbon::parse($remark->created_at)->diffForHumans() }}</small>
                      @if(!empty($remark->activity))
                        <small class="text-muted">• {{ $remark->activity }}</small>
                      @endif
                    </div>
                    <div class="legacy-remark-body">{{ $remark->remark }}</div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
           <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
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