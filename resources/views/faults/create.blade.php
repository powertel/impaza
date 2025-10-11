<!-- Add Programme Modal -->
<div class="modal custom-modal fade" id="createFaultModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createFaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFaultModalLabel">
                    <i class="bx bx-building me-2"></i>Add New Fault
                </h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div class="card-body">
                <form id="UF" action="{{ route('faults.store') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="customer" class="form-label">Customer Name </label>
                            <select id="customer" class="custom-select @error('customer_id') is-invalid @enderror"  name="customer_id" >
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
                            <label for="linkName" class="form-label">City/Town</label>
                            <select id="city" class="custom-select @error('city_id') is-invalid @enderror" name="city_id" >
                                <option selected disabled  >Select City/Town</option>
                                @foreach($city as $city)
                                    @if (old('city_id')==$city->id)
                                        <option value="{{ $city->id}}" selected>{{ $city->city }}</option>
                                    @else
                                        <option value="{{ $city->id}}">{{ $city->city }}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="contactName" class="form-label">Contact Name</label>
                            <input type="text" class="form-control @error('contactName') is-invalid @enderror"  placeholder="Contact Name" name="contactName" value="{{ old('contactName') }}">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="city" class="form-label">Location</label>
                            <select id="suburb"  class="custom-select @error('suburb_id') is-invalid @enderror" name="suburb_id" >
                                <option selected disabled>Select Suburb</option>
                                @foreach($location as $location)
                                    @if (old('suburb_id')==$location->id)
                                        <option value="{{ $location->id}}" selected>{{ $location->suburb }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="suburb" class="form-label">Link</label>
                            <select id="link" class="custom-select @error('link_id') is-invalid @enderror" name="link_id" >
                                <option selected disabled>Select Link</option>
                                @foreach($link as $link)
                                    @if (old('link_id')==$link->id)
                                        <option value="{{ $link->id}}" selected>{{ $link->link }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="pop" class="form-label">POP</label>
                            <select id="pop"  class="custom-select @error('pop_id') is-invalid @enderror" name="pop_id" >
                                <option selected disabled>Select Pop</option>
                                @foreach($pop as $pop)
                                    @if (old('pop_id')==$pop->id)
                                        <option value="{{ $pop->id}}" selected>{{ $pop->pop }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="number" class="form-control @error('phoneNumber') is-invalid @enderror"  placeholder="Phone Number" name="phoneNumber" value="{{ old('phoneNumber') }}" >

                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="serviceType" class="form-label">Service Type</label>
                            <select  class="custom-select @error('serviceType') is-invalid @enderror" name="serviceType">
                                <option  selected disabled>Choose</option>
                                <option value="VPN"  @if (old('serviceType') == "VPN") {{ 'selected' }} @endif>VPN</option>
                                <option value="INTERNET" @if (old('serviceType') == "INTERNET") {{ 'selected' }} @endif>INTERNET</option>
                                <option value="VOIP" @if (old('serviceType') == "VOIP") {{ 'selected' }} @endif>VOIP</option>
                                <option value="CARRIER SERVICE" @if (old('serviceType') == "CARRIER SERVICE") {{ 'selected' }} @endif>CARRIER SERVICE</option>
                                <option value="POWERTRACK" @if (old('serviceType') == "POWERTRACK") {{ 'selected' }} @endif>POWERTRACK</option>
                                <option value="CDMA VOICE" @if (old('serviceType') == "CDMA VOICE") {{ 'selected' }} @endif>CDMA VOICE</option>
                                <option value="E-VENDING" @if (old('serviceType') == "E-VENDING") {{ 'selected' }} @endif>E-VENDING</option>
                            </select>
                        </div>

                    </div>
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('contactEmail') is-invalid @enderror" placeholder="email" name="contactEmail" value="{{ old('contactEmail') }}" >
                        </div>
                        <div class="mb-3 col-md-6">
                        <label for="suspectedRFO" class="form-label">Suspected Reason For Outage</label>
                            <select  class="custom-select @error('suspectedRfo_id') is-invalid @enderror" name="suspectedRfo_id" >
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

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"  placeholder="Address" name="address" value="{{ old('address') }}" >
                        </div>
                        <div class="mb-3 col-md-6">
                        <label for="accountManager" class="form-label">Account Manager</label>
                            <select  class="custom-select @error('accountManager_id') is-invalid @enderror" name="accountManager_id" >
                                <option selected disabled >Select Account Manager</option>
                                @foreach($accountManager as $acc_manager)

                                    @if (old('accountManager_id')==$acc_manager->id)
                                        <option value="{{ $acc_manager->id}}" selected>{{ $acc_manager->accountManager }}</option>
                                    @else
                                        <option value="{{ $acc_manager->id}}">{{ $acc_manager->accountManager }}</option>
                                    @endif

                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="remark" class="form-label">Remarks</label><br>
                           <!-- <input for="fileToUpload" class="nav-icon fal fa-upload">Attach File With Remark</label> -->
                            <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Enter any additional comments and Attach Your File Below If Any" rows="2"  >{{ old('remark') }}</textarea>
                           <input type="hidden" name="activity" value="ON LOGGING">
                           <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" id="fileToUpload">
                        </div>
                    </div>
</form>
            </div>
        </div>
    </div>
</div>

<!-- Removed stray Blade section directives from partial to prevent InvalidArgumentException -->
