@extends('layouts.admin')

@section('title')
Assess
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-100">
            <div class="card-header">
                <h3 class="card-title">{{_('Fault Assesment')}}</h3>
            </div>
            <div class="card-body">
                <form  action="{{ route('assessments.update', $fault->id ) }}" method="POST">
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

                        <div class="mb-3 col-md-2">
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
                    
                        <div class="mb-3 col-md-4">
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
                    </div>
        
                    <div class="row g-2">
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

                        <div class="mb-3 col">
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
                        <div class="mb-3 col">
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
                        <label for="suspectedRfo" class="form-label">Suspected RFO</label>
                            <select  class="custom-select " value="{{$fault->suspectedRfo}}" name="suspectedRfo">
                            <option selected="selected">{{ $fault->suspectedRfo }}</option>
                                <option>No fx Light</option>
                                <option>No PON Light</option>
                                <option>BTS Down</option>
                                <option>Node Down</option>
                                <option>Unknown</option>
                            </select>
                        </div>
                
                        <div class="mb-3 col-md-6">
                        <label for="service" class="form-label">Service Type</label>
                            <select type="text"  class="custom-select " value="{{$fault->serviceType}}" name="serviceType">
                                <option selected="selected">{{ $fault->serviceType }}</option>
                                <option>VOIP</option>
                                <option>VPN</option>
                                <option>INTERNET</option>
                                <option>CARRIER SERVICE</option>
                                <option>POWERTRACK</option>
                                <option>CDMA VOICE</option>
                                <option>CDMA VOICE</option>
                                <option>E-VENDING</option>
                            </select>
                        </div>

                    </div>
        
                    <div class="row g-2">
                        <div class="mb-3 col-md-2">
                            <label for="faultType" class="form-label">Fault Type</label>
                            <select type="text"  class="custom-select "  name="faultType" value="{{ old('faultType') }}">
                                <option selected="selected">Select Fault Type</option>
                                <option>Carrier/Mux</option>
                                <option>logical</option>
                                <option>Cable</option>
                                <option>Power</option>
                                <option>Active Equipments</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="rfo" class="form-label">Confirmed RFO</label>
                            <select type="text"  class="custom-select "  name="confirmedRfo" value="{{ old('confirmedRfo') }}">
                                <option selected="selected">Select RFO</option>
                                <option>Faulty Mux</option>
                                <option>Faulty Board</option>
                                <option>Power Fault</option>
                                <option>UTP fault</option>
                                <option>Patch lead fault</option>
                                <option>UG cable fault</option>
                                <option>Burn Cables</option>
                                <option>FAS</option>
                                <option>Power Outage</option>
                                <option>Backbone fault</option>
                                <option>Faulty Switch</option>
                                <option>Fault Router</option>
                                <option>Fault Chassis</option>
                                <option>Converter Faulty</option>
                                <option>Faulty SW/Port</option>
                                <option>CPE Faulty</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="actDepartment" class="form-label">Actioning Department</label>
                            <select id="section" class="custom-select @error('section_id') is-invalid @enderror" name="section_id">
                                <option selected disabled  >Select Department</option>
                                @foreach($sections as $section)
                                    @if (old('section_id')==$section->id)
                                        <option value="{{ $section->id}}" selected>{{ $section->section }}</option>
                                    @else
                                        <option value="{{ $section->id}}">{{ $section->section }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                
                    </div>
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="priorityLevel" class="form-label">Priority Level</label>
                            <select id="priorityLevel" class="form-select" name="priorityLevel" value="{{ old('priorityLevel') }}">
                                <option>Select</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Critical</option>
                            </select>
                        </div>                
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Save') }}</button>
                        <button type="button" class="btn btn-secondary btn-sm" >Revoke</button>   
                        <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}" >{{ __('Cancel') }}</a>           
                       
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