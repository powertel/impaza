<!-- Show Fault Modal -->
<div class="modal custom-modal fade" id="showFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showFaultModalLabel-{{ $fault->id }}">
                    <i class="fas fa-eye me-2"></i>View Fault
                </h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-muted">Customer Name</h6>
                                <p class="mb-2">{{ $fault->customer }}</p>
                                <h6 class="text-muted">City/Town</h6>
                                <p class="mb-2">{{ $fault->city }}</p>
                                <h6 class="text-muted">Location</h6>
                                <p class="mb-2">{{ $fault->suburb }}</p>
                                <h6 class="text-muted">Link</h6>
                                <p class="mb-2">{{ $fault->link }}</p>
                                <h6 class="text-muted">POP</h6>
                                <p class="mb-2">{{ $fault->pop }}</p>
                                <h6 class="text-muted">Service Type</h6>
                                <p class="mb-2">{{ $fault->serviceType }}</p>
                                <h6 class="text-muted">Address</h6>
                                <p class="mb-2">{{ $fault->address }}</p>
                                <h6 class="text-muted">Account Manager</h6>
                                <p class="mb-2">{{ $fault->accountManager }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-muted">Contact Name</h6>
                                <p class="mb-2">{{ $fault->contactName }}</p>
                                <h6 class="text-muted">Phone Number</h6>
                                <p class="mb-2">{{ $fault->phoneNumber }}</p>
                                <h6 class="text-muted">Email Address</h6>
                                <p class="mb-2">{{ $fault->contactEmail }}</p>
                                <h6 class="text-muted">Suspected RFO</h6>
                                <p class="mb-2">{{ $fault->RFO }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($remarks) && count($remarks))
                <div class="mt-4">
                    <h5 class="mb-3"><i class="fas fa-comment-dots me-2"></i>Remarks</h5>
                    <div class="row g-3">
                        @foreach($remarks as $remark)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <div>
                                                <h6 class="mb-1">{{ $remark->name }}</h6>
                                                <small class="text-muted">Added {{ Carbon\Carbon::parse($remark->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        <p class="mt-2">{{ $remark->remark }}</p>
                                        @if($remark->file_path)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" title="Attachment" style="max-width:100%; height:auto; border-radius:8px;">
                                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#PicModal-{{ $remark->id }}">View Attachment</button>
                                            </div>

                                            <!-- Remark Attachment Modal -->
                                            <div class="modal custom-modal fade" id="PicModal-{{ $remark->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="PicModalLabel-{{ $remark->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="PicModalLabel-{{ $remark->id }}">
                                                                <i class="fas fa-paperclip me-2"></i>Remark Attachment
                                                            </h5>
                                                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="{{ asset('storage/'.$remark->file_path) }}" alt="Attachment" title="Attachment" style="max-width:100%; height:auto; border-radius:8px;">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>