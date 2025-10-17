@can('rectify-fault')
<div class="modal fade" id="rectifyEditModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="rectifyEditModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rectifyEditModalLabel-{{ $fault->id }}">Fault Rectification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
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

        @if(isset($remarks) && count($remarks))
        <div class="mt-4">
          <div class="d-flex align-items-center mb-2">
            <span class="badge bg-info me-2"><i class="fas fa-comments"></i></span>
            <h6 class="mb-0 text-secondary">Conversation</h6>
          </div>
          <!-- Scrollable chat-style conversation -->
          <div id="remarksScroller-{{ $fault->id }}" class="js-remarks-list" style="max-height: 420px; overflow-y: auto; padding-right: 6px;">
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
                      <button type="button" class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#PicModal-{{ $remark->id }}">View</button>
                    </div>
                    <!-- Remark Attachment Modal -->
                    <div class="modal custom-modal fade" id="PicModal-{{ $remark->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="PicModalLabel-{{ $remark->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                          <div class="modal-header border-0">
                            <h5 class="modal-title" id="PicModalLabel-{{ $remark->id }}"><i class="fas fa-paperclip me-2"></i>Attachment</h5>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" class="img-fluid rounded">
                          </div>
                          <div class="modal-footer border-0">
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
          <form action="/faults/{{ $fault->id }}/remarks" method="POST" enctype="multipart/form-data" class="js-remark-form" data-remarks-target="#remarksScroller-{{ $fault->id }}">
            {{ csrf_field() }}
            <div class="row g-2 align-items-end">
              <div class="col-md-8">
                <label class="form-label">Add Remark</label>
                <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" rows="2" placeholder="Enter your message"></textarea>
                <input type="hidden" name="activity" value="ON RECTIFICATION">
                <input type="hidden" name="url" value="{{ url()->current() }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Attachment (optional)</label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/png,image/jpg,image/jpeg">
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-success btn-sm float-end">Send</button>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('rectify.update', $fault->id ) }}" method="POST" class="d-inline">
          @csrf
          @method('PUT')
          <button type="submit" class="btn btn-success btn-sm">Restore</button>
        </form>
      </div>
  </div>
</div>
</div>
@endcan

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('rectifyEditModal-{{ $fault->id }}');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('remarksScroller-{{ $fault->id }}');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>
