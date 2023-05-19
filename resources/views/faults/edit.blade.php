@extends('layouts.admin')

@section('title')
Fault
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-100">
            <div class="card-header">
                <h3 class="card-title">{{_('Edit Fault')}}</h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('faults.update', $fault->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="customerName" class="form-label">Customer Name </label>
                            <select class="custom-select" id="customer" name="customer_id">
                                <option selected="selected" value="{{ $fault->customer_id}}">{{ $fault->customer }}</option>
                                @foreach($customers as $customer)
                                    @unless ($customer->id ===$fault->customer_id)
                                        <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="city" class="form-label">City/Town</label>
                            <select  class="custom-select" id="city" name="city_id">
                                <option selected="selected" value="{{ $fault->city_id}}">{{ $fault->city }}</option>
                                @foreach($cities as $city)
                                    @unless($city->id ===$fault->city_id)
                                        <option value="{{ $city->id}}">{{ $city->city }}</option>
                                    @endunless
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="contactName" class="form-label">Contact Name</label>
                            <input type="text" class="form-control" value="{{$fault->contactName}}" name="contactName">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="city" class="form-label">Location</label>
                            <select   class="custom-select" id="suburb" name="suburb_id">
                             <option selected="selected" value="{{ $fault->suburb_id}}">{{ $fault->suburb }}</option>
                                @foreach($suburbs as $suburb)
                                    @if ($suburb->city_id === $fault->city_id)
                                        @unless($suburb->id ===$fault->suburb_id)
                                            <option value="{{ $suburb->id}}">{{ $suburb->suburb }}</option>
                                        @endunless
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="suburb" class="form-label">Link</label>
                            <select class="custom-select" id="link" name="link_id">
                            <option selected="selected" value="{{ $fault->link_id}}">{{ $fault->link}}</option>
                                @foreach($links as $link)
                                    @if ($link->customer_id === $fault->customer_id)
                                        @unless ($link->id ===$fault->link_id)
                                            <option value="{{ $link->id}}">{{ $link->link }}</option>
                                        @endunless
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="pop" class="form-label">POP</label>
                            <select  class="custom-select" id="pop" name="pop_id" >
                                <option selected="selected" value="{{ $fault->pop_id}}">{{ $fault->pop }}</option>
                                @foreach($pops as $pop)
                                    @if($pop->suburb_id === $fault->suburb_id)
                                        @unless($pop->id ===$fault->pop_id)
                                            <option value="{{ $pop->id}}">{{ $pop->pop }}</option>
                                        @endunless
                                    @endif
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="{{$fault->phoneNumber}}" name="phoneNumber">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="service" class="form-label">Service Type</label>
                            <select type="text"  class="custom-select " value="{{$fault->serviceType}}" name="serviceType">
                                <option selected="selected">{{ $fault->serviceType }}</option>
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
                            <input type="email" class="form-control" value="{{$fault->contactEmail}}" name="contactEmail">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="suspectedRfo" class="form-label">Suspected Reason For Outage</label>
                            <select  class="custom-select " value="{{$fault->suspectedRfo}}" name="suspectedRfo">
                                <option selected="selected">{{ $fault->suspectedRfo }}</option>
                                <option value="No fx Light"  @if (old('suspectedRfo') == "No fx Light") {{ 'selected' }} @endif>No fx Light</option>
                                <option value="No PON Light"  @if (old('suspectedRfo') == "No PON Light") {{ 'selected' }} @endif>No PON Light</option>
                                <option value="BTS Down"  @if (old('suspectedRfo') == "BTS Down") {{ 'selected' }} @endif>BTS Down</option>
                                <option value="Node Down"  @if (old('suspectedRfo') == "Node Down") {{ 'selected' }} @endif>Node Down</option>
                                <option value="Unknown"  @if (old('suspectedRfo') == "Unknown") {{ 'selected' }} @endif>Unknown</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="adress" class="form-label">Address</label>
                            <input type="text" class="form-control" value="{{$fault->address}}" name="address">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="accountManager" class="form-label">Account Manager</label>
                            <select class="custom-select" id="accountManager" name="accountManager_id">
                                <option selected="selected" value="{{ $fault->accountManager_id}}">{{ $fault->accountManager }}</option>
                                @foreach($accountManagers as $acc_manager)
                                    @unless ($acc_manager->id ===$fault->accountManager_id)
                                        <option value="{{ $acc_manager->id}}">{{ $acc_manager->accountManager }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="javascript:history.back()">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Remarks')}}</h3>
            </div>
            <div class="card-body" style="height: 0px; overflow-y: auto">
                @foreach($remarks as $remark)
                @if ($remark->fault_id === $fault->id)
                <div class="callout callout-info">
                    @if($remark->user)
                    <h5 class="font-weight-bold">{{ $remark->user->name}}</h5>
                    @endif

                    <h4 class="text-muted text-sm">
                        <strong>
                        Added Remark  {{$remark->created_at->diffForHumans()}}
                       </strong>
                    </h4>

                    <p>{{$remark->remark}} </p>
                </div>
                @endif
                @endforeach
            </div>

            <div class="card-footer">
                <form action="/faults/{{$fault->id}}/remarks" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="remark" class="form-control" placeholder="Enter Your Remarks" rows="1"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-sm float-right">{{ __('Add Remark') }}</button>
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
