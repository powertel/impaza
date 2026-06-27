<!-- Create Fault Modal -->
<div class="modal custom-modal fade" id="createFaultModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createFaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="fault-modal-header-copy">
                    <h5 class="modal-title" id="createFaultModalLabel">
                        <i class="fas fa-tools me-2"></i>Log Fault
                    </h5>
                    <div class="text-muted small mt-1">Capture the affected service, contact details, and first technical update.</div>
                    <div class="fault-modal-meta">
                        <span class="fault-modal-meta-item"><i class="fas fa-circle-plus"></i> New Intake</span>
                        <span class="fault-modal-meta-item"><i class="fas fa-image"></i> Attachment Optional</span>
                        <span class="fault-modal-meta-item"><i class="fas fa-bolt"></i> Quick Capture</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="UF" action="{{ route('faults.store') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="fault-modal-note mb-3">
                        <i class="fas fa-circle-info"></i>
                        <div>Use this form to log the first customer report, link the affected service, and capture the initial technical notes in one place.</div>
                    </div>

                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-network-wired"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Customer & Link</div>
                                <div class="fault-modal-section-subtitle">Select the customer account and the affected service link.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer" class="form-label">Customer Name</label>
                                    <select id="customer" class="form-select select2 customer-select @error('customer_id') is-invalid @enderror" name="customer_id" data-selected="{{ old('customer_id') }}">
                                        <option value=""></option>
                                        @foreach($customer as $customerItem)
                                            <option value="{{ $customerItem->id }}" {{ old('customer_id') == $customerItem->id ? 'selected' : '' }}>
                                                {{ $customerItem->customer }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="link" class="form-label">Link</label>
                                    <select id="link" class="form-select select2 link-select @error('link_id') is-invalid @enderror" name="link_id" data-selected="{{ old('link_id') }}">
                                        <option value=""></option>
                                        @foreach($link as $linkItem)
                                            @if (old('link_id') == $linkItem->id)
                                                <option value="{{ $linkItem->id }}" selected>{{ $linkItem->link }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('link_id')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section mb-3">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-address-book"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Contact & Address</div>
                                <div class="fault-modal-section-subtitle">Add the caller details and suspected reason for outage.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="contactName" class="form-label">Contact Name</label>
                                    <input id="contactName" type="text" class="form-control @error('contactName') is-invalid @enderror" name="contactName" value="{{ old('contactName') }}" placeholder="Contact Name">
                                    @error('contactName')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="phoneNumber" class="form-label">Phone Number</label>
                                    <input id="phoneNumber" type="tel" class="form-control @error('phoneNumber') is-invalid @enderror" name="phoneNumber" value="{{ old('phoneNumber') }}" required pattern="^2637\d{8}$" minlength="12" maxlength="12" inputmode="numeric" title="Phone number must be 12 digits starting with 2637" placeholder="e.g. 263776123456">
                                    @error('phoneNumber')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @else
                                        <div class="invalid-feedback">Phone number must be 12 digits starting with 2637</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="contactEmail" class="form-label">Contact Email (optional)</label>
                                    <input id="contactEmail" type="email" class="form-control @error('contactEmail') is-invalid @enderror" name="contactEmail" value="{{ old('contactEmail') }}" placeholder="e.g. name@example.com">
                                    @error('contactEmail')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="suspectedRFO" class="form-label">Suspected Reason For Outage</label>
                                    <select id="suspectedRFO" class="form-select select2 @error('suspectedRfo_id') is-invalid @enderror" name="suspectedRfo_id">
                                        <option value=""></option>
                                        @foreach($suspectedRFO as $suspectedRfoItem)
                                            <option value="{{ $suspectedRfoItem->id }}" {{ old('suspectedRfo_id') == $suspectedRfoItem->id ? 'selected' : '' }}>
                                                {{ $suspectedRfoItem->RFO }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('suspectedRfo_id')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Fault Address</label>
                                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" placeholder="Address">
                                    @error('address')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fault-modal-section">
                        <div class="fault-modal-section-header">
                            <span class="fault-modal-section-icon"><i class="fas fa-clipboard-check"></i></span>
                            <div>
                                <div class="fault-modal-section-title">Remarks & Resolution</div>
                                <div class="fault-modal-section-subtitle">Describe the issue clearly and attach any image evidence if available.</div>
                            </div>
                        </div>
                        <div class="fault-modal-section-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="remark" class="form-label">Remarks (Issue, port and switch)</label>
                                    <textarea id="remark" name="remark" required class="form-control @error('remark') is-invalid @enderror" rows="4" placeholder="Enter the current issue, troubleshooting notes, port details, and switch details">{{ old('remark') }}</textarea>
                                    @error('remark')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                    <input type="hidden" name="activity" value="ON LOGGING">
                                </div>
                                <div class="col-md-12">
                                    <label for="attachment" class="form-label">Image attachment (optional)</label>
                                    <div class="impaza-dropzone" data-impaza-dropzone>
                                        <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept="image/*">
                                        <div class="dz-inner">
                                            <div class="dz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                            <div class="dz-text">
                                                <div class="a">Drag and drop an image here</div>
                                                <div class="b">Or click to browse. JPG/PNG supported. Max size follows server settings.</div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('attachment')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                    <div class="form-text">You can also paste an image directly into the remarks field.</div>
                                    <div class="mt-2" id="attachmentPreviewContainer" style="display:none;">
                                        <img id="attachmentPreview" src="" class="img-thumbnail" style="max-height:200px;">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="fault-modal-toggle">
                                        <div class="form-check mt-2">
                                        <input class="form-check-input me-1" type="checkbox" id="resolvedOnCall" name="resolved_on_call" value="1" {{ old('resolved_on_call') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="resolvedOnCall">Resolved on call</label>
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
                <button type="submit" form="UF" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-save me-1"></i> Log Fault
                </button>
            </div>
        </div>
    </div>
</div>

