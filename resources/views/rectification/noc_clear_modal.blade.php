@can('noc-clear-faults-clear')
<div class="modal fade" id="nocClearModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="nocClearModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nocClearModalLabel-{{ $fault->id }}">Confirm Clear (NOC)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('noc-clear.update',$fault->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Customer</label>
              <input type="text" class="form-control" value="{{ $fault->customer }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Link</label>
              <input type="text" class="form-control" value="{{ $fault->link }}" disabled>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirmed Reason For Outage</label>
            <select class="form-select select2 @error('confirmedRfo_id') is-invalid @enderror" name="confirmedRfo_id" id="nocConfirmedRfoSelect-{{ $fault->id }}" required>
              <option selected disabled>Select RFO</option>
              @foreach(($confirmedRFO ?? []) as $confirmed_rfo)
                <option value="{{ $confirmed_rfo->id }}" {{ (isset($fault->confirmedRfo_id) && (int) $fault->confirmedRfo_id === (int) $confirmed_rfo->id) ? 'selected' : '' }}>{{ $confirmed_rfo->RFO }}</option>
              @endforeach
            </select>
          </div>

          @if(isset($remarks) && count($remarks))
          <div class="mt-4">
            <div class="d-flex align-items-center mb-2">
              <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
              <h6 class="mb-0 text-secondary">Conversation</h6>
            </div>
            <!-- Scrollable chat-style conversation -->
            <div id="nocClearRemarksScroller-{{ $fault->id }}" class="js-remarks-list" style="max-height: 420px; overflow-y: auto; padding-right: 6px;">
              @foreach($remarks->sortBy('created_at') as $remark)
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
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-outline-success btn-sm">
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
