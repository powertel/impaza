<!-- Add Programme Modal -->
<div class="modal custom-modal fade" id="createFaultModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createFaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFaultModalLabel">
                    <i class="fas fa-tools me-2"></i>Add New Fault
                </h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="UF" action="{{ route('faults.store') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="row g-3">
                        <div class="mb-3 col-md-6">
                            <label for="customer" class="form-label">Customer Name </label>
                            <select id="customer" class="form-select select2 @error('customer_id') is-invalid @enderror"  name="customer_id" >
                                <option selected disabled >Select Customer</option>
                                @foreach($customer as $customer)
                                    @if (old('customer_id')==$customer->id)
                                        <option value="{{ $customer->id}}" selected>{{ $customer->customer }}</option>
                                    @else
                                        <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="link" class="form-label">Link</label>
                            <select id="link" class="form-select @error('link_id') is-invalid @enderror" name="link_id" >
                                <option selected disabled>Select Link</option>
                                @foreach($link as $link)
                                    @if (old('link_id')==$link->id)
                                        <option value="{{ $link->id}}" selected>{{ $link->link }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="mb-3 col-md-4">
                            <label for="contactName" class="form-label">Contact Name</label>
                            <input type="text" class="form-control @error('contactName') is-invalid @enderror"  placeholder="Contact Name" name="contactName" value="{{ old('contactName') }}">
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="number" class="form-control @error('phoneNumber') is-invalid @enderror"  placeholder="Phone Number" name="phoneNumber" value="{{ old('phoneNumber') }}" >
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="suspectedRFO" class="form-label">Suspected Reason For Outage</label>
                            <select  class="form-select @error('suspectedRfo_id') is-invalid @enderror" name="suspectedRfo_id" >
                                <option selected disabled >Select RFO</option>
                                @foreach($suspectedRFO  as $suspected_rfo)
                                    @if (old('suspectedRfo_id')==$suspected_rfo->id)
                                        <option value="{{ $suspected_rfo->id}}" selected>{{ $suspected_rfo->RFO }}</option>
                                    @else
                                        <option value="{{ $suspected_rfo->id}}">{{ $suspected_rfo->RFO }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                    </div>

                    <div class="row g-3">
                        <div class="mb-3 col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"  placeholder="Address" name="address" value="{{ old('address') }}" >
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="mb-3 col-md-8">
                            <label for="remark" class="form-label">Remarks</label>
                            <textarea name="remark" required class="form-control @error('remark') is-invalid @enderror" placeholder="Enter any additional comments" rows="3">{{ old('remark') }}</textarea>
                            <input type="hidden" name="activity" value="ON LOGGING">
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="fileToUpload" class="form-label">Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" id="fileToUpload">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="UF" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
