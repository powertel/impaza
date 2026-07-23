<!-- Edit Fault Modal -->
@php
    $editStatusRaw = trim((string) ($fault->description ?? ''));
    $editStatusLabel = match (strtolower($editStatusRaw)) {
        'fault has been restored', 'resolved' => 'Fault Restored',
        'fault is under rectification', 'under rectification' => 'Under Rectification',
        'waiting for assessment', 'waiting assessment' => 'Waiting Assessment',
        'fault has been assessed', 'assessed' => 'Assessed',
        'fault has been rectified', 'rectified' => 'Rectified',
        'fault has been cleared by ct', 'cleared by ct' => 'Cleared by CT',
        'fault has been refered', 'fault has been referred', 'referred' => 'Referred',
        'fault has been parked', 'parked' => 'Parked',
        'fault has been revoked', 'revoked' => 'Revoked',
        'fault  escalated to chief technician', 'fault escalated to chief technician', 'escalated to chief technician' => 'Escalated',
        'impacted by pop outage' => 'POP Outage',
        default => $editStatusRaw !== '' ? $editStatusRaw : 'Open',
    };
    $editStatusColor = \App\Models\Status::STATUS_COLOR[$editStatusRaw] ?? \App\Models\Status::STATUS_COLOR[$editStatusLabel] ?? '#64748B';
@endphp
<div class="modal custom-modal fade" id="editFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="fault-modal-header-copy">
                    <h5 class="modal-title" id="editFaultModalLabel-{{ $fault->id }}">
                        <i class="fas fa-edit me-2"></i>Edit Fault
                    </h5>
                    <div class="text-muted small mt-1">Update customer details, remarks, and attachments for {{ $fault->fault_ref_number }}.</div>
                    <div class="fault-modal-meta">
                        <span class="fault-modal-meta-item"><i class="fas fa-hashtag"></i> {{ $fault->fault_ref_number }}</span>
                        <span class="fault-modal-meta-item">
                            <x-status-badge :label="$editStatusLabel" :color="$editStatusColor" :soft="true" />
                        </span>
                        <span class="fault-modal-meta-item"><i class="fas fa-link"></i> {{ $fault->link ?: 'Current Link' }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="UF-edit-{{ $fault->id }}" action="{{ route('faults.update', $fault->id ) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="fault-modal-note mb-3">
                        <i class="fas fa-pen-to-square"></i>
                        <div>Use this workspace to update contact details, review the conversation history, and append the latest technical action without losing the original audit trail.</div>
                    </div>
                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-network-wired"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Customer & Link</div>
                                <div class="fault-modal-section-subtitle">Core service details for the affected fault.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" value="{{ !empty($fault->customer) ? $fault->customer : 'Current Customer' }}" disabled>
                                    <input type="hidden" name="customer_id" value="{{ $fault->customer_id ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="link-{{ $fault->id }}" class="form-label">Link</label>
                                    <select class="form-select link-select" id="link-{{ $fault->id }}" name="link_id" data-selected="{{ $fault->link_id ?? '' }}">
                                        @isset($fault->link_id)
                                            <option selected="selected" value="{{ $fault->link_id }}">{{ $fault->link ?? 'Current Link' }}</option>
                                        @else
                                            <option selected disabled>Select Link</option>
                                        @endisset
                                        @foreach($links as $l)
                                            @if (isset($fault->customer_id) && $l->customer_id == $fault->customer_id)
                                                <option value="{{ $l->id }}" @if(isset($fault->link_id) && $fault->link_id == $l->id) selected @endif>{{ $l->link }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="city-{{ $fault->id }}" class="form-label">City/Town</label>
                                    <select class="form-select city-select" id="city-{{ $fault->id }}" name="city_id" disabled>
                                        @isset($fault->city_id)
                                            <option selected="selected" value="{{ $fault->city_id }}">{{ !empty($fault->city) ? $fault->city : 'Current City' }}</option>
                                        @else
                                            <option selected disabled>Derived from Link</option>
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="suburb-{{ $fault->id }}" class="form-label">Location</label>
                                    <select class="form-select suburb-select" id="suburb-{{ $fault->id }}" name="suburb_id" data-selected="{{ $fault->suburb_id ?? '' }}" disabled>
                                        @isset($fault->suburb_id)
                                            <option selected="selected" value="{{ $fault->suburb_id }}">{{ !empty($fault->suburb) ? $fault->suburb : 'Current Suburb' }}</option>
                                        @else
                                            <option selected disabled>Derived from Link</option>
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="pop-{{ $fault->id }}" class="form-label">POP</label>
                                    <select class="form-select pop-select" id="pop-{{ $fault->id }}" name="pop_id" data-selected="{{ $fault->pop_id ?? '' }}" disabled>
                                        @isset($fault->pop_id)
                                            <option selected="selected" value="{{ $fault->pop_id }}">{{ !empty($fault->pop) ? $fault->pop : 'Current Pop' }}</option>
                                        @else
                                            <option selected disabled>Derived from Link</option>
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="service-{{ $fault->id }}" class="form-label">Service Type</label>
                                    <select class="form-select" name="serviceType" disabled>
                                        @isset($fault->serviceType)
                                            <option selected="selected">{{ $fault->serviceType }}</option>
                                        @else
                                            <option selected disabled>Derived from Link</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-address-book"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Contact & Address</div>
                                <div class="fault-modal-section-subtitle">Update the caller and address details for follow-up.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="phone-{{ $fault->id }}" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" value="{{ $fault->phoneNumber ?? '' }}" name="phoneNumber" placeholder="e.g. 263776123456">
                                </div>
                                <div class="col-md-4">
                                    <label for="contactName-{{ $fault->id }}" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control" value="{{ $fault->contactName ?? '' }}" name="contactName" placeholder="Contact Name">
                                </div>
                                <div class="col-md-4">
                                    <label for="contactEmail-{{ $fault->id }}" class="form-label">Contact Email (optional)</label>
                                    <input type="email" class="form-control" value="{{ $fault->contactEmail ?? '' }}" name="contactEmail" placeholder="e.g. name@example.com">
                                </div>
                                <div class="col-md-12">
                                    <label for="address-{{ $fault->id }}" class="form-label">Address</label>
                                    <input type="text" class="form-control" value="{{ $fault->address ?? '' }}" name="address" placeholder="Address">
                                </div>
                                <div class="col-md-6">
                                    <label for="suspectedRfo-{{ $fault->id }}" class="form-label">Suspected Reason For Outage</label>
                                    <select class="form-select" id="suspectedRFO-{{ $fault->id }}" name="suspectedRfo_id">
                                        @isset($fault->suspectedRfo_id)
                                            <option selected="selected" value="{{ $fault->suspectedRfo_id }}">{{ $fault->RFO ?? 'Current Suspected RFO' }}</option>
                                        @endisset
                                        @foreach($suspectedRFO as $suspected_rfo)
                                            @if (!isset($fault->suspectedRfo_id) || $suspected_rfo->id !== $fault->suspectedRfo_id)
                                                <option value="{{ $suspected_rfo->id }}">{{ $suspected_rfo->RFO }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-comments"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Conversation History</div>
                                <div class="fault-modal-section-subtitle">Previous remarks, ownership trail, and attachments.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div id="remarksScroller-edit-{{ $fault->id }}" class="chat-messages fault-modal-stream">
                                @if(isset($remarks) && count($remarks) > 0)
                                    @foreach($remarks->sortBy('created_at') as $remark)
                                        @php
                                            $currentName = optional(auth()->user())->name;
                                            $isOwn = $currentName && (strtolower(trim($remark->name)) === strtolower(trim($currentName)));
                                            $attachmentPath = (string) ($remark->file_path ?? '');
                                            $attachmentUrl = ($attachmentPath !== '' && \Illuminate\Support\Facades\Storage::disk('public')->exists($attachmentPath))
                                                ? \Illuminate\Support\Facades\Storage::disk('public')->url($attachmentPath)
                                                : null;
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
                                            @if($remark->file_path)
                                                <div class="fault-modal-attachment">
                                                    @if($attachmentUrl)
                                                        <a href="{{ $attachmentUrl }}" target="_blank" class="fault-modal-attachment-thumb" title="View attachment">
                                                            <img src="{{ $attachmentUrl }}" alt="Attachment" class="img-fluid rounded">
                                                        </a>
                                                        <div class="fault-modal-attachment-actions">
                                                            <a href="{{ $attachmentUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-up-right-from-square me-1"></i> Open
                                                            </a>
                                                            <a href="{{ $attachmentUrl }}" class="btn btn-outline-secondary btn-sm" download>
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="fault-modal-attachment-missing">
                                                            <i class="fas fa-paperclip"></i>
                                                            <span>Attachment unavailable</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="fault-modal-empty">No remarks found.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-clipboard-check"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Remarks & Resolution</div>
                                <div class="fault-modal-section-subtitle">Capture the latest technical update and any supporting evidence.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="switch_name-edit-{{ $fault->id }}" class="form-label">Switch</label>
                                    <input id="switch_name-edit-{{ $fault->id }}" type="text" class="form-control @error('switch_name') is-invalid @enderror" name="switch_name" value="{{ old('switch_name') }}" placeholder="Enter switch name or identifier">
                                    @error('switch_name')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="port-edit-{{ $fault->id }}" class="form-label">Port</label>
                                    <input id="port-edit-{{ $fault->id }}" type="text" class="form-control @error('port') is-invalid @enderror" name="port" value="{{ old('port') }}" placeholder="Enter port number or label">
                                    @error('port')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="remark-edit-{{ $fault->id }}" class="form-label">Remarks</label>
                                    <textarea name="remark" required class="form-control @error('remark') is-invalid @enderror edit-remark" data-fault-id="{{ $fault->id }}" placeholder="Enter any additional comments" rows="4">{{ $fault->remark ?? old('remark') }}</textarea>
                                    <input type="hidden" name="activity" value="ON EDIT">
                                </div>
                                <div class="col-md-12">
                                    <label for="attachment-edit-{{ $fault->id }}" class="form-label">Image attachment (optional)</label>
                                    <input type="file" class="form-control @error('attachment') is-invalid @enderror edit-attachment" id="attachment-edit-{{ $fault->id }}" name="attachment" accept="image/*" data-fault-id="{{ $fault->id }}">
                                    @error('attachment')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                    <div class="form-text">You can also paste an image into the remarks field.</div>
                                    <div class="mt-2 edit-attachment-preview-container" data-fault-id="{{ $fault->id }}" style="display:none;">
                                        <img class="img-thumbnail edit-attachment-preview" data-fault-id="{{ $fault->id }}" style="max-height:200px;">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="fault-modal-toggle">
                                        <div class="form-check mt-2">
                                        <input class="form-check-input me-1" type="checkbox" id="resolvedOnCall-edit-{{ $fault->id }}" name="resolved_on_call" value="1" {{ old('resolved_on_call') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-1" for="resolvedOnCall-edit-{{ $fault->id }}">Resolved on call</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer fault-modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="submit" form="UF-edit-{{ $fault->id }}" class="btn btn-primary btn-sm rounded-pill">
                  <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
