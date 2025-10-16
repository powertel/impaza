<!-- Show Fault Modal (Modernized) -->
<div class="modal custom-modal fade" id="showFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2"><i class="fas fa-eye"></i></span>
                    <h5 class="modal-title mb-0" id="showFaultModalLabel-{{ $fault->id }}">View Fault</h5>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-0">
                <div class="row g-4">
                    <!-- Fault Details -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 rounded-3">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0 text-secondary"><i class="fas fa-info-circle me-2 text-primary"></i>Fault Details</h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Customer Name</small>
                                        <div class="fw-semibold">{{ $fault->customer }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">City/Town</small>
                                        <div class="fw-semibold">{{ $fault->city }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Location</small>
                                        <div class="fw-semibold">{{ $fault->suburb }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Link</small>
                                        <div class="fw-semibold">{{ $fault->link }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">POP</small>
                                        <div class="fw-semibold">{{ $fault->pop }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Service Type</small>
                                        <div class="fw-semibold"><span class="badge bg-secondary">{{ $fault->serviceType }}</span></div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Address</small>
                                        <div class="fw-semibold">{{ $fault->address }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Account Manager</small>
                                        <div class="fw-semibold">{{ $fault->accountManager }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Contact & RFO -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 rounded-3">
                            <div class="card-header bg-transparent border-0">
                                <h6 class="mb-0 text-secondary"><i class="fas fa-user-circle me-2 text-primary"></i>Contact & RFO</h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Contact Name</small>
                                        <div class="fw-semibold">{{ $fault->contactName }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Phone Number</small>
                                        <div class="fw-semibold">{{ $fault->phoneNumber }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Email Address</small>
                                        <div class="fw-semibold">{{ $fault->contactEmail }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Suspected RFO</small>
                                        <div class="fw-semibold">{{ $fault->RFO }}</div>
                                    </div>
                                </li>
                            </ul>
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
                    <div id="remarksScroller-{{ $fault->id }}" style="max-height: 420px; overflow-y: auto; padding-right: 6px;">
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
    </div>

    <div class="modal-footer border-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
    </div>
    </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl = document.getElementById('showFaultModal-{{ $fault->id }}');
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      var scroller = document.getElementById('remarksScroller-{{ $fault->id }}');
      if (scroller) { scroller.scrollTop = scroller.scrollHeight; }
    });
  }
});
</script>