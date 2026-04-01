<!-- Add Programme Modal -->
<div class="modal custom-modal fade" id="createFaultModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createFaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="createFaultModalLabel">
                    <i class="fas fa-tools me-2"></i>Add New Fault
                </h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="UF" action="{{ route('faults.store') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-info text-dark">Customer & Link</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer" class="form-label">Customer Name</label>
                                    <select id="customer" class="form-select select2 @error('customer_id') is-invalid @enderror" name="customer_id">
                                        <option selected disabled>Select Customer</option>
                                        @foreach($customer as $customer)
                                            @if (old('customer_id')==$customer->id)
                                                <option value="{{ $customer->id}}" selected>{{ $customer->customer }}</option>
                                            @else
                                                <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="link" class="form-label">Link</label>
                                    <select id="link" class="form-select select2 @error('link_id') is-invalid @enderror" name="link_id">
                                        <option selected disabled>Select Link</option>
                                        @foreach($link as $link)
                                            @if (old('link_id')==$link->id)
                                                <option value="{{ $link->id}}" selected>{{ $link->link }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-success text-dark">Contact & RFO</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contactName" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control @error('contactName') is-invalid @enderror" placeholder="Contact Name" name="contactName" value="{{ old('contactName') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phoneNumber') is-invalid @enderror" placeholder="e.g. 263776123456" name="phoneNumber" value="{{ old('phoneNumber') }}" required pattern="^2637\d{8}$" minlength="12" maxlength="12" inputmode="numeric" title="Phone number must be 12 digits starting with 2637">
                                    @error('phoneNumber')
                                        <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                    @else
                                        <div class="invalid-feedback">Phone number must be 12 digits starting with 2637</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="contactEmail" class="form-label">Contact Email (optional)</label>
                                    <input type="email" class="form-control @error('contactEmail') is-invalid @enderror" placeholder="e.g. name@example.com" name="contactEmail" value="{{ old('contactEmail') }}">
                                    @error('contactEmail')
                                        <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="suspectedRFO" class="form-label">Suspected Reason For Outage</label>
                                    <select class="form-select select2 @error('suspectedRfo_id') is-invalid @enderror" name="suspectedRfo_id">
                                        <option selected disabled>Select RFO</option>
                                        @foreach($suspectedRFO  as $suspected_rfo)
                                            @if (old('suspectedRfo_id')==$suspected_rfo->id)
                                                <option value="{{ $suspected_rfo->id}}" selected>{{ $suspected_rfo->RFO }}</option>
                                            @else
                                                <option value="{{ $suspected_rfo->id}}">{{ $suspected_rfo->RFO }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label for="address" class="form-label">Fault Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" placeholder="Address" name="address" value="{{ old('address') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                  <!--   <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-warning text-dark">Location & Address</div>
                        <div class="card-body">
                            <div class="row g-3">

                            </div>
                        </div>
                    </div>
 -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-secondary text-dark">Remarks</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12 mb-2">
                                    <label for="remark" class="form-label">Remarks (Issue, port and Switch)</label>
                                    <textarea name="remark" required class="form-control @error('remark') is-invalid @enderror" placeholder="Enter any additional comments" rows="3">{{ old('remark') }}</textarea>
                                    <input type="hidden" name="activity" value="ON LOGGING">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label for="attachment" class="form-label">Image attachment (optional)</label>
                                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept="image/*">
                                    @error('attachment')
                                        <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                                    @enderror
                                    <div class="form-text">You can also paste an image into the remarks field.</div>
                                    <div class="mt-2" id="attachmentPreviewContainer" style="display:none;">
                                        <img id="attachmentPreview" src="" class="img-thumbnail" style="max-height:200px;">
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input me-1" type="checkbox" id="resolvedOnCall" name="resolved_on_call" value="1" {{ old('resolved_on_call') ? 'checked' : '' }}>
                                        <label class="form-check-label fs-5 fw-semibold" for="resolvedOnCall">Resolved on call</label>
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
                <button type="submit" form="UF" class="btn btn-primary btn-sm">
                  <i class="fas fa-save me-1"></i> Log Fault
                </button>
            </div>
        </div>
    </div>
</div>

