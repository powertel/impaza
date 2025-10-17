<!-- Edit Fault Modal -->
<div class="modal custom-modal fade" id="editFaultModal-{{ $fault->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editFaultModalLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFaultModalLabel-{{ $fault->id }}">
                    <i class="fas fa-edit me-2"></i>Edit Fault
                </h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="UF-edit-{{ $fault->id }}" action="{{ route('faults.update', $fault->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="customer-{{ $fault->id }}" class="form-label">Customer Name</label>
                            <select class="form-select customer-select" id="customer-{{ $fault->id }}" name="customer_id">
                                @isset($fault->customer_id)
                                    <option selected="selected" value="{{ $fault->customer_id }}">{{ $fault->customer ?? 'Current Customer' }}</option>
                                @endisset
                                @foreach($customers as $customer)
                                    @if (!isset($fault->customer_id) || $customer->id !== $fault->customer_id)
                                        <option value="{{ $customer->id }}">{{ $customer->customer }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="city-{{ $fault->id }}" class="form-label">City/Town</label>
                            <select class="form-select city-select" id="city-{{ $fault->id }}" name="city_id" disabled>
                                @isset($fault->city_id)
                                    <option selected="selected" value="{{ $fault->city_id }}">{{ $fault->city ?? 'Current City' }}</option>
                                @else
                                    <option selected disabled>Derived from Link</option>
                                @endisset
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-2">
                            <label for="suburb-{{ $fault->id }}" class="form-label">Location</label>
                            <select class="form-select suburb-select" id="suburb-{{ $fault->id }}" name="suburb_id" data-selected="{{ $fault->suburb_id ?? '' }}" disabled>
                                @isset($fault->suburb_id)
                                    <option selected="selected" value="{{ $fault->suburb_id }}">{{ $fault->suburb ?? 'Current Suburb' }}</option>
                                @else
                                    <option selected disabled>Derived from Link</option>
                                @endisset
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
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
                        <div class="mb-3 col-md-2">
                            <label for="pop-{{ $fault->id }}" class="form-label">POP</label>
                            <select class="form-select pop-select" id="pop-{{ $fault->id }}" name="pop_id" data-selected="{{ $fault->pop_id ?? '' }}" disabled>
                                @isset($fault->pop_id)
                                    <option selected="selected" value="{{ $fault->pop_id }}">{{ $fault->pop ?? 'Current Pop' }}</option>
                                @else
                                    <option selected disabled>Derived from Link</option>
                                @endisset
                            </select>
    
                        </div>

                        <div class="mb-3 col-md-6">
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

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="phone-{{ $fault->id }}" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="{{ $fault->phoneNumber ?? '' }}" name="phoneNumber">
                        </div>

                        
                        <div class="mb-3 col-md-6">
                            <label for="contactName-{{ $fault->id }}" class="form-label">Contact Name</label>
                            <input type="text" class="form-control" value="{{ $fault->contactName ?? '' }}" name="contactName">
                        </div>

                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
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
                        <div class="mb-3 col-md-6">
                            <label for="address-{{ $fault->id }}" class="form-label">Address</label>
                            <input type="text" class="form-control" value="{{ $fault->address ?? '' }}" name="address">
                        </div>
                    </div>

                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="UF-edit-{{ $fault->id }}" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

