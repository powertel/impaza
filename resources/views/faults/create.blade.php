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
                <div class="impaza-stepper" data-impaza-stepper="UF">
                  <button type="button" class="impaza-step" data-impaza-step="1">
                    <span class="dot">1</span>
                    <span class="meta"><span class="k">Step 1</span><span class="t">Customer & Link</span></span>
                  </button>
                  <button type="button" class="impaza-step" data-impaza-step="2">
                    <span class="dot">2</span>
                    <span class="meta"><span class="k">Step 2</span><span class="t">Contact & RFO</span></span>
                  </button>
                  <button type="button" class="impaza-step" data-impaza-step="3">
                    <span class="dot">3</span>
                    <span class="meta"><span class="k">Step 3</span><span class="t">Remarks & Attachments</span></span>
                  </button>
                  <button type="button" class="impaza-step" data-impaza-step="4">
                    <span class="dot">4</span>
                    <span class="meta"><span class="k">Step 4</span><span class="t">Review & Submit</span></span>
                  </button>
                </div>

                <form id="UF" data-impaza-stepper-form action="{{ route('faults.store') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="impaza-form-section" data-impaza-step-pane="1">
                        <div class="impaza-form-section-header">
                          <div class="impaza-form-section-title">
                            <div class="impaza-form-section-icon"><i class="fas fa-building"></i></div>
                            <div class="impaza-form-section-text">
                              <div class="title">Customer & Link</div>
                              <div class="desc">Select the customer and affected service link.</div>
                            </div>
                          </div>
                        </div>
                        <div class="impaza-form-section-body">
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

                    <div class="impaza-form-section" data-impaza-step-pane="2">
                        <div class="impaza-form-section-header">
                          <div class="impaza-form-section-title">
                            <div class="impaza-form-section-icon"><i class="fas fa-user"></i></div>
                            <div class="impaza-form-section-text">
                              <div class="title">Contact & RFO</div>
                              <div class="desc">Capture contact details and the suspected reason for outage.</div>
                            </div>
                          </div>
                        </div>
                        <div class="impaza-form-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="impaza-field">
                                      <input id="contactName" type="text" class="form-control @error('contactName') is-invalid @enderror" placeholder=" " name="contactName" value="{{ old('contactName') }}">
                                      @error('contactName')
                                        <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                      @enderror
                                      <label for="contactName" class="form-label impaza-float-label">Contact Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="impaza-field">
                                      <input id="phoneNumber" type="tel" class="form-control @error('phoneNumber') is-invalid @enderror" placeholder=" " name="phoneNumber" value="{{ old('phoneNumber') }}" required pattern="^2637\d{8}$" minlength="12" maxlength="12" inputmode="numeric" title="Phone number must be 12 digits starting with 2637">
                                      @error('phoneNumber')
                                          <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                      @else
                                          <div class="invalid-feedback">Phone number must be 12 digits starting with 2637</div>
                                      @enderror
                                      <label for="phoneNumber" class="form-label impaza-float-label">Phone Number</label>
                                    </div>
                                    <div class="form-text">e.g. 263776123456</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="impaza-field">
                                      <input id="contactEmail" type="email" class="form-control @error('contactEmail') is-invalid @enderror" placeholder=" " name="contactEmail" value="{{ old('contactEmail') }}">
                                      @error('contactEmail')
                                          <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                      @enderror
                                      <label for="contactEmail" class="form-label impaza-float-label">Contact Email (optional)</label>
                                    </div>
                                    <div class="form-text">e.g. name@example.com</div>
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
                                    <div class="impaza-field">
                                      <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" placeholder=" " name="address" value="{{ old('address') }}">
                                      @error('address')
                                          <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                      @enderror
                                      <label for="address" class="form-label impaza-float-label">Fault Address</label>
                                    </div>
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
                    <div class="impaza-form-section" data-impaza-step-pane="3">
                        <div class="impaza-form-section-header">
                          <div class="impaza-form-section-title">
                            <div class="impaza-form-section-icon"><i class="fas fa-clipboard-list"></i></div>
                            <div class="impaza-form-section-text">
                              <div class="title">Remarks & Attachments</div>
                              <div class="desc">Add engineering remarks and optional evidence.</div>
                            </div>
                          </div>
                        </div>
                        <div class="impaza-form-section-body">
                            <div class="row g-3">
                                <div class="col-md-12 mb-2">
                                    <div class="impaza-field">
                                      <textarea id="remark" name="remark" required class="form-control @error('remark') is-invalid @enderror" placeholder=" " rows="3">{{ old('remark') }}</textarea>
                                      @error('remark')
                                          <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                      @enderror
                                      <label for="remark" class="form-label impaza-float-label">Remarks (Issue, port and Switch)</label>
                                    </div>
                                    <input type="hidden" name="activity" value="ON LOGGING">
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label for="attachment" class="form-label">Image attachment (optional)</label>
                                    <div class="impaza-dropzone" data-impaza-dropzone>
                                      <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" accept="image/*">
                                      <div class="dz-inner">
                                        <div class="dz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                        <div class="dz-text">
                                          <div class="a">Drag & drop an image here</div>
                                          <div class="b">Or click to browse • JPG/PNG • Max as per server settings</div>
                                        </div>
                                      </div>
                                    </div>
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
                                        <label class="form-check-label fw-semibold" for="resolvedOnCall">Resolved on call</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="impaza-form-section" data-impaza-step-pane="4">
                        <div class="impaza-form-section-header">
                          <div class="impaza-form-section-title">
                            <div class="impaza-form-section-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="impaza-form-section-text">
                              <div class="title">Review & Submit</div>
                              <div class="desc">Confirm details before logging the fault.</div>
                            </div>
                          </div>
                        </div>
                        <div class="impaza-form-section-body">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <div class="text-muted small mb-1">Customer</div>
                              <div class="fw-semibold" id="impazaReviewCustomer">—</div>
                            </div>
                            <div class="col-md-6">
                              <div class="text-muted small mb-1">Link</div>
                              <div class="fw-semibold" id="impazaReviewLink">—</div>
                            </div>
                            <div class="col-md-6">
                              <div class="text-muted small mb-1">Contact</div>
                              <div class="fw-semibold" id="impazaReviewContact">—</div>
                            </div>
                            <div class="col-md-6">
                              <div class="text-muted small mb-1">Suspected RFO</div>
                              <div class="fw-semibold" id="impazaReviewRfo">—</div>
                            </div>
                            <div class="col-12">
                              <div class="text-muted small mb-1">Remarks</div>
                              <div class="fw-semibold" style="white-space: pre-wrap;" id="impazaReviewRemark">—</div>
                            </div>
                          </div>
                          <div class="alert alert-info mt-3 mb-0" style="border-radius: 14px;">
                            Review the summary, then click Log Fault to submit.
                          </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-light border btn-sm" data-impaza-stepper-prev="UF">
                  <i class="fas fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-impaza-stepper-next="UF">
                  Continue <i class="fas fa-arrow-right ms-1"></i>
                </button>
                <button type="submit" form="UF" class="btn btn-primary btn-sm" data-impaza-stepper-submit="UF">
                  <i class="fas fa-save me-1"></i> Log Fault
                </button>
            </div>
        </div>
    </div>
</div>

