@can('re-assign-fault')
<div class="modal custom-modal fade" id="reassignModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reassignModalLabel-{{ $fault->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title mb-0" id="reassignModalLabel-{{ $fault->id }}"><i class="fas fa-arrows-rotate me-2"></i>Re-Assign Fault</h5>
          <small class="text-muted">Ref. {{ $fault->fault_ref_number ?? 'N/A' }} • {{ $fault->customer ?? 'N/A' }}</small>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-link"></i> {{ $fault->link ?? 'N/A' }}</span>
            <span class="fault-modal-meta-item"><i class="fas fa-user-tag"></i> {{ $fault->name ?? 'Unassigned' }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-0">
        <div class="fault-modal-note mb-4">
          <i class="fas fa-circle-info"></i>
          <div>Reassign the fault with a clear handover note so the next technician understands the current state and next action.</div>
        </div>
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="row g-4">
              <div class="col-12">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                  <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0 text-secondary"><i class="fas fa-info-circle me-2 text-primary"></i>Fault Details</h6>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Customer Name</small>
                        <div class="fw-semibold">{{ $fault->customer ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">City/Town</small>
                        <div class="fw-semibold">{{ $fault->city ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Location</small>
                        <div class="fw-semibold">{{ $fault->suburb ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Link</small>
                        <div class="fw-semibold">{{ $fault->link ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">POP</small>
                        <div class="fw-semibold">{{ $fault->pop ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Service Type</small>
                        <div class="fw-semibold"><span class="badge bg-secondary">{{ $fault->serviceType ?? 'N/A' }}</span></div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Account Manager</small>
                        <div class="fw-semibold">{{ $fault->accountManager ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Age</small>
                        <div class="fw-semibold">
                          <span class="badge bg-light text-dark border fault-age" data-age-start="{{ $ageStart ?? '' }}" data-age-end="{{ $ageEnd ?? '' }}">{{ $ageText ?? '' }}</span>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="col-12">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                  <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0 text-secondary"><i class="fas fa-user-circle me-2 text-primary"></i>Contact & RFO</h6>
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Contact Name</small>
                        <div class="fw-semibold">{{ $fault->contactName ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Phone Number</small>
                        <div class="fw-semibold">{{ $fault->phoneNumber ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Email Address</small>
                        <div class="fw-semibold">{{ $fault->contactEmail ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Suspected RFO</small>
                        <div class="fw-semibold">{{ $fault->RFO ?? 'N/A' }}</div>
                      </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                      <div>
                        <small class="text-muted">Confirmed RFO</small>
                        <div class="fw-semibold">{{ $fault->confirmedRFO ?? 'N/A' }}</div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 text-secondary"><i class="fas fa-user-tag me-2 text-primary"></i>Re-Assignment</h6>
              </div>
              <div class="card-body">
                <form id="reassign-form-{{ $fault->id }}" action="{{ route('assign.update', $fault->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="row g-3">
                    <div class="col-12">
                      <small class="text-muted d-block">Currently Assigned To</small>
                      <div class="fw-semibold">{{ $fault->name ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                      <label class="form-label required" for="assignedTo-reassign-{{ $fault->id }}">Assign To</label>
                      <select id="assignedTo-reassign-{{ $fault->id }}" name="assignedTo" class="form-select @error('assignedTo') is-invalid @enderror" required>
                        <option value="">Select technician</option>
                        @foreach($technicians as $tech)
                          <option value="{{ $tech->id }}" @if((int)($fault->assignedTo ?? 0) === (int)$tech->id) selected @endif>{{ $tech->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label required" for="remark-reassign-{{ $fault->id }}">Remarks</label>
                      <textarea id="remark-reassign-{{ $fault->id }}" name="remark" class="form-control @error('remark') is-invalid @enderror" rows="7" placeholder="Reason for re-assignment and handover notes." required>{{ old('remark', '') }}</textarea>
                      <div class="form-text text-muted">This is saved to the conversation.</div>
                    </div>
                  </div>
                </form>
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
          <div id="remarksScroller-{{ $fault->id }}" class="border rounded-3 bg-white p-3">
            <div class="chat-messages fault-modal-stream">
              @foreach($remarks->sortBy('created_at') as $r)
                @php
                  $currentName = optional(auth()->user())->name;
                  $isOwn = $currentName && (strtolower(trim($r->name)) === strtolower(trim($currentName)));
                  $attachmentPath = (string) ($r->file_path ?? '');
                  $attachmentUrl = ($attachmentPath !== '' && \Illuminate\Support\Facades\Storage::disk('public')->exists($attachmentPath))
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($attachmentPath)
                    : null;
                @endphp
                <div class="chat-msg {{ $isOwn ? 'chat-msg-self' : 'chat-msg-other' }}">
                  <div class="chat-msg-meta d-flex align-items-center gap-2">
                    <span class="badge {{ $isOwn ? 'bg-primary' : 'bg-secondary' }}">{{ $r->name ?? 'User' }}</span>
                    <span>{{ Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</span>
                    @if(!empty($r->activity))
                      <span>• {{ $r->activity }}</span>
                    @endif
                    @if($attachmentUrl)
                      <span class="ms-auto">
                        <a href="{{ $attachmentUrl }}" class="btn btn-link btn-sm p-0 text-decoration-none" download>
                          <i class="fas fa-download me-1"></i>Download
                        </a>
                      </span>
                    @endif
                  </div>
                  <div class="chat-msg-body">{{ $r->remark }}</div>
                  @if($r->file_path)
                    <div class="fault-modal-attachment">
                      @if($attachmentUrl)
                        <a href="{{ $attachmentUrl }}" target="_blank" class="fault-modal-attachment-thumb">
                          <img src="{{ $attachmentUrl }}" alt="Attachment" class="img-fluid rounded">
                        </a>
                      @else
                        <div class="fault-modal-attachment-missing"><i class="fas fa-paperclip"></i><span>Attachment unavailable</span></div>
                      @endif
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
        @endif
      </div>

      <div class="modal-footer border-0 fault-modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button type="submit" form="reassign-form-{{ $fault->id }}" class="btn btn-primary btn-sm rounded-pill">
          <i class="fas fa-save me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
@endcan
