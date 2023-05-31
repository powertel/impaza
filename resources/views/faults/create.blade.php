@extends('layouts.admin')

@section('title')
Fault
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-100">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Log Fault')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('faults.store') }}" method="POST">
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
                            <input type="text" class="form-control @error('phoneNumber') is-invalid @enderror"  placeholder="Phone Number" name="phoneNumber" value="{{ old('phoneNumber') }}" >

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
                            <label for="suspectedRfo_id" class="form-label">Suspected Reason For Outage</label>
                            <select  class="custom-select @error('suspectedRfo_id') is-invalid @enderror" name="suspectedRfo_id">
                            <option selected disabled>Choose Suspected RFO</option>
                                 @foreach($suspectedRFO as $suspected_rfo)
                                    @if (old('suspectedRFO_id')==$suspected_rfo->id)
                                        <option value="{{ $suspected_rfo->id}}" selected>{{ $suspected_rfo->SuspectedRFO }}</option>
                                    @else
                                        <option value="{{ $suspected_rfo->id}}">{{ $suspected_rfo->SuspectedRFO }}</option>
                                    @endif
                                 @endforeach
                         </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="adress" class="form-label">Address</label>
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
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Enter any additional comments" rows="2"  >{{ old('remark') }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection

@section('scripts')
    @include('partials.faults')
@endsection
