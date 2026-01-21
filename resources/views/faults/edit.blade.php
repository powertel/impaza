<!-- Edit Fault Modal -->
<div class="modal custom-modal fade" id="editFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="editFaultModalLabel-{{ $fault->id }}">
                    <i class="fas fa-edit me-2"></i>Edit Fault
                </h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="UF-edit-{{ $fault->id }}" action="{{ route('faults.update', $fault->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-info text-dark">Customer & Link</div>
                        <div class="card-body">
                            <div class="row g-2">
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
                            </div>
                            <div class="row g-2 mt-2">
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
                            </div>
                            <div class="row g-2 mt-2">
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

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-success text-dark">Contact & Address</div>
                        <div class="card-body">
                            <div class="row g-2">
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
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-12">
                                    <label for="address-{{ $fault->id }}" class="form-label">Address</label>
                                    <input type="text" class="form-control" value="{{ $fault->address ?? '' }}" name="address" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-secondary text-dark">Suspected RFO</div>
                        <div class="card-body">
                            <div class="row g-2">
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

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light text-dark">Conversation History</div>
                        <div class="card-body">
                            <div id="remarksScroller-edit-{{ $fault->id }}" style="max-height: 300px; overflow-y: auto; padding-right: 6px;">
                                @if(isset($remarks) && count($remarks) > 0)
                                    @foreach($remarks->sortBy('created_at') as $remark)
                                        @php
                                            $currentName = optional(auth()->user())->name;
                                            $isOwn = $currentName && (strtolower(trim($remark->name)) === strtolower(trim($currentName)));
                                        @endphp
                                        <div class="d-flex {{ $isOwn ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                            <div class="rounded-3 shadow-sm px-3 py-2" style="max-width: 85%; background-color: {{ $isOwn ? '#e8f5e9' : '#eef5ff' }};">
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
                                                         <a href="{{ asset('storage/'.$remark->file_path) }}" target="_blank" class="d-inline-block text-decoration-none" title="View attachment">
                                                            <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" class="img-fluid rounded" style="max-height: 100px; object-fit: cover;">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted py-3">No remarks found.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-secondary text-dark">Remarks</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12 mb-2">
                                    <label for="remark-edit-{{ $fault->id }}" class="form-label">Remarks (Issue, port and Switch)</label>
                                    <textarea name="remark" required class="form-control @error('remark') is-invalid @enderror" placeholder="Enter any additional comments" rows="3">{{ $fault->remark ?? old('remark') }}</textarea>
                                    <input type="hidden" name="activity" value="ON EDIT">
                                </div>
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input me-1" type="checkbox" id="resolvedOnCall-edit-{{ $fault->id }}" name="resolved_on_call" value="1" {{ old('resolved_on_call') ? 'checked' : '' }}>
                                        <label class="form-check-label fs-5 fw-semibold ms-1" for="resolvedOnCall-edit-{{ $fault->id }}">Resolved on call</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="submit" form="UF-edit-{{ $fault->id }}" class="btn btn-primary btn-sm">
                  <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
