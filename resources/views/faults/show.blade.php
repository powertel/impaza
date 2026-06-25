<!-- Show Fault Modal (Modernized) -->
<div class="modal custom-modal fade" id="showFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="showFaultModalLabel-{{ $fault->id }}">
                        <i class="fas fa-eye me-2"></i>View Fault
                    </h5>
                    <div class="text-muted small mt-1">Full fault profile, service details, and conversation history for {{ $fault->fault_ref_number }}.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="fault-modal-section h-100">
                            <div class="fault-modal-section-header">
                                <span class="fault-modal-section-icon"><i class="fas fa-info-circle"></i></span>
                                <div>
                                    <div class="fault-modal-section-title">Fault Details</div>
                                    <div class="fault-modal-section-subtitle">Service context, location, and ownership information.</div>
                                </div>
                            </div>
                            <div class="fault-modal-section-body">
                                <div class="fault-modal-grid">
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Customer</span>
                                        <div class="fault-modal-kv-value">{{ $fault->customer }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Account Manager</span>
                                        <div class="fault-modal-kv-value">{{ $fault->accountManager ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">City/Town</span>
                                        <div class="fault-modal-kv-value">{{ $fault->city }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Location</span>
                                        <div class="fault-modal-kv-value">{{ $fault->suburb }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Link</span>
                                        <div class="fault-modal-kv-value">{{ $fault->link }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">POP</span>
                                        <div class="fault-modal-kv-value">{{ $fault->pop }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Service Type</span>
                                        <div class="fault-modal-kv-value"><span class="badge rounded-pill bg-secondary-subtle text-secondary border">{{ $fault->serviceType }}</span></div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Age</span>
                                        <div class="fault-modal-kv-value">
                                            <span class="faults-age-pill fault-age" data-age-start="{{ $ageStart ?? '' }}" data-age-end="{{ $ageEnd ?? '' }}">{{ $ageText ?? '' }}</span>
                                        </div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Address</span>
                                        <div class="fault-modal-kv-value">{{ $fault->address ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Assigned To</span>
                                        <div class="fault-modal-kv-value">{{ $fault->assignedTo ?? 'Not yet assigned' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Assessed By</span>
                                        <div class="fault-modal-kv-value">{{ $fault->assessedBy ?? 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Ref. No.</span>
                                        <div class="fault-modal-kv-value">{{ $fault->fault_ref_number }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="fault-modal-section h-100">
                            <div class="fault-modal-section-header">
                                <span class="fault-modal-section-icon"><i class="fas fa-user-circle"></i></span>
                                <div>
                                    <div class="fault-modal-section-title">Contact & RFO</div>
                                    <div class="fault-modal-section-subtitle">Caller details, outage reason, and escalation context.</div>
                                </div>
                            </div>
                            <div class="fault-modal-section-body">
                                <div class="fault-modal-grid">
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Contact Name</span>
                                        <div class="fault-modal-kv-value">{{ $fault->contactName ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Phone Number</span>
                                        <div class="fault-modal-kv-value">{{ $fault->phoneNumber ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Email Address</span>
                                        <div class="fault-modal-kv-value">{{ $fault->contactEmail ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Reported By</span>
                                        <div class="fault-modal-kv-value">{{ $fault->reportedBy ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Suspected RFO</span>
                                        <div class="fault-modal-kv-value">{{ $fault->RFO ?: 'N/A' }}</div>
                                    </div>
                                    <div class="fault-modal-kv">
                                        <span class="fault-modal-kv-label">Confirmed RFO</span>
                                        <div class="fault-modal-kv-value">{{ $fault->confirmedRFO ?: 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($remarks) && count($remarks))
                <div class="mt-4">
                    <div class="fault-modal-section">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-comments"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Conversation</div>
                                <div class="fault-modal-section-subtitle">Chronological updates, ownership actions, and attachments.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div id="remarksScroller-{{ $fault->id }}" class="chat-messages" style="max-height: 420px; overflow-y: auto; padding-right: 6px;">
                                @foreach($remarks->sortBy('created_at') as $remark)
                                    @php
                                        $currentName = optional(auth()->user())->name;
                                        $isOwn = $currentName && (strtolower(trim($remark->name)) === strtolower(trim($currentName)));
                                        $attachmentPath = (string) ($remark->file_path ?? '');
                                        $attachmentExists = $attachmentPath !== '' && \Illuminate\Support\Facades\Storage::disk('public')->exists($attachmentPath);
                                        $attachmentUrl = $attachmentExists ? \Illuminate\Support\Facades\Storage::disk('public')->url($attachmentPath) : null;
                                    @endphp
                                    <div class="chat-msg {{ $isOwn ? 'chat-msg-self ms-auto' : 'chat-msg-other' }}">
                                        <div class="chat-msg-meta">
                                            <strong>{{ $remark->name ?? 'User' }}</strong>
                                            <span class="mx-1">•</span>{{ Carbon\Carbon::parse($remark->created_at)->diffForHumans() }}
                                            @if(!empty($remark->activity))
                                                <span class="mx-1">•</span>{{ $remark->activity }}
                                            @endif
                                        </div>
                                        <div class="chat-msg-body">{{ $remark->remark }}</div>
                                        @if($remark->file_path)
                                            <div class="mt-2">
                                                @if($attachmentUrl)
                                                    <a href="#" class="d-inline-block text-decoration-none" data-bs-toggle="modal" data-bs-target="#PicModal-{{ $remark->id }}" aria-controls="PicModal-{{ $remark->id }}" title="View attachment">
                                                        <img src="{{ $attachmentUrl }}" alt="Attachment" class="img-fluid rounded" style="max-height: 160px; object-fit: cover; cursor: pointer;">
                                                    </a>
                                                    <a href="{{ $attachmentUrl }}" class="btn btn-link btn-sm text-decoration-none" download>
                                                        <i class="fas fa-download me-1"></i>
                                                    </a>
                                                @else
                                                    <div class="small text-muted d-inline-flex align-items-center gap-2 px-3 py-2 rounded border">
                                                        <i class="fas fa-paperclip"></i>
                                                        <span>Attachment unavailable</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($attachmentUrl)
                                                <div class="modal custom-modal fade" id="PicModal-{{ $remark->id }}" data-bs-backdrop="false" data-bs-keyboard="true" tabindex="-1" aria-labelledby="PicModalLabel-{{ $remark->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="PicModalLabel-{{ $remark->id }}"><i class="fas fa-paperclip me-2"></i>Attachment</h5>
                                                                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $attachmentUrl }}" alt="Attachment" class="img-fluid rounded">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a href="{{ $attachmentUrl }}" class="btn btn-outline-primary" download><i class="fas fa-download me-1"></i>Download</a>
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
