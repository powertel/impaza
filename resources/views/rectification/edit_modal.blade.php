@can('rectify-fault')
<div class="modal fade" id="rectifyEditModal-{{ $fault->id }}" tabindex="-1" aria-labelledby="rectifyEditModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
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

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent border-0 d-flex align-items-center">
            <h6 class="mb-0 text-secondary"><i class="fas fa-comments me-2 text-primary"></i>Remarks</h6>
          </div>
          <div id="rectifyRemarks-{{ $fault->id }}" class="card-body chat-messages js-remarks-list" style="max-height: 300px; overflow-y: auto;">
            @if(isset($remarks) && count($remarks))
              @foreach($remarks as $remark)
                @php $isSelf = (isset(auth()->user()->name) && $remark->name === auth()->user()->name); @endphp
                <div class="chat-msg {{ $isSelf ? 'chat-msg-self' : 'chat-msg-other' }}">
                  <div class="chat-msg-meta">
                    <strong>{{ $remark->name }}</strong>
                    <span class="text-muted">• {{ Carbon\Carbon::parse($remark->created_at)->diffForHumans() }}</span>
                    @if($remark->activity)
                      <span class="ms-2 badge bg-light text-dark">{{ $remark->activity }}</span>
                    @endif
                  </div>
                  <div class="chat-msg-body">{{ $remark->remark }}</div>
                  @if($remark->file_path)
                    <div class="mt-2">
                      <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" title="Attachment" class="img-fluid rounded" style="height:100px; width:auto" data-bs-toggle="modal" data-bs-target="#PicModal-{{ $remark->id }}">
                    </div>
                    <!-- Remark Attachment Modal -->
                    <div class="modal fade" id="PicModal-{{ $remark->id }}" tabindex="-1" aria-labelledby="PicModalLabel-{{ $remark->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="PicModalLabel-{{ $remark->id }}">REMARK ATTACHMENT</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" class="img-fluid" style="max-height:500px; max-width:100%">
                          </div>
                          <div class="modal-footer"></div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              @endforeach
            @endif
          </div>
        </div>

        <div class="mt-3">
          <form action="/faults/{{ $fault->id }}/remarks" method="POST" enctype="multipart/form-data" class="js-remark-form" data-remarks-target="#rectifyRemarks-{{ $fault->id }}">
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