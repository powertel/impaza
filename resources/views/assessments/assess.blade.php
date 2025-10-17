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
                <h3 class="card-title">Fault Assesment</h3>
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
                        <label for="suspectedRfo" class="form-label">Suspected Reason For Outage</label>
                            <select class="custom-select" id="suspectedRFO" name="suspectedRfo_id">
                                <option selected="selected" value="{{ $fault->suspectedRfo_id}}">{{ $fault->RFO }}</option>
                                @foreach($suspectedRFO as $suspected_rfo)
                                    @unless ($suspected_rfo->id ===$fault->suspectedRfo_id)
                                        <option value="{{ $suspected_rfo->id}}">{{ $suspected_rfo->RFO }}</option>
                                    @endunless
                                @endforeach
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
                        <div class="mb-3 col-md-3">
                            <label for="faultType" class="form-label">Fault Type</label>
                            <select type="text"  class="custom-select @error('faultType') is-invalid @enderror"  name="faultType">
                                <option selected disabled>Select Fault Type</option>
                                <option  value="Carrier/Mux"  @if (old('faultType') == "Carrier/Mux") {{ 'selected' }} @endif>Carrier/Mux</option>
                                <option  value="logical"  @if (old('faultType') == "logical") {{ 'selected' }} @endif>logical</option>
                                <option  value="Cable"  @if (old('faultType') == "Cable") {{ 'selected' }} @endif>Cable</option>
                                <option  value="Power"  @if (old('faultType') == "Power") {{ 'selected' }} @endif>Power</option>
                                <option  value="Active Equipments"  @if (old('faultType') == "Active Equipments") {{ 'selected' }} @endif>Active Equipments</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-3">
                        <label for="suspectedRFO" class="form-label">Confirmed Reason For Outage</label>
                            <select  class="custom-select @error('Rfo_id') is-invalid @enderror" name="confirmedRfo_id" >
                                <option selected disabled >Select RFO</option>
                                @foreach($confirmedRFO  as $confirmed_rfo)

                                    @if (old('confirmedRfo_id')==$confirmed_rfo->id)
                                        <option value="{{ $confirmed_rfo->id}}" selected>{{ $confirmed_rfo->RFO }}</option>
                                    @else
                                        <option value="{{ $confirmed_rfo->id}}">{{ $confirmed_rfo->RFO }}</option>
                                    @endif

                                @endforeach
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
                            <select id="priorityLevel"  class="custom-select @error('priorityLevel') is-invalid @enderror" name="priorityLevel">
                                <option selected disabled>Select</option>
                                <option  value="Low"  @if (old('priorityLevel') == "Low") {{ 'selected' }} @endif>Low</option>
                                <option  value="Medium"  @if (old('priorityLevel') == "Medium") {{ 'selected' }} @endif>Medium</option>
                                <option  value="High"  @if (old('priorityLevel') == "High") {{ 'selected' }} @endif>High</option>
                                <option  value="Critical"  @if (old('priorityLevel') == "Critical") {{ 'selected' }} @endif>Critical</option>
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
                <h3 class="card-title">Remarks</h3>
            </div>
            <div class="card-body" style="height: 0px; overflow-y: auto">
            @foreach($remarks as $remark)
                <div class="callout callout-info">
                    <h5 class="font-weight-bold">{{ $remark->name}}</h5>
                    <h4 class="text-muted text-sm">
                        <strong>
                        Added Remark  {{Carbon\Carbon::parse($remark->created_at)->diffForHumans()}}
                       </strong>
                    </h4>
                    <h5 class="font-weight-bold">{{ $remark->activity}}</h5>
                    <p>{{$remark->remark}} </p>
                    <h4 class="text-muted text-sm">
                        <strong>
                        Attachment
                       </strong>
                    </h4>
                    <img src="{{asset('storage/'.$remark->file_path)}}"alt="Not here!" title="Attachment" style="height:100px; width:auto">
                </div>
                @endforeach
            </div> 
<!-- Modal -->
<div class="modal fade bd-example-modal-xl"  id="PicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"> REMARK ATTACHMENT</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <img src="" id="show_it" alt="Not here!" style="height:500px; max-width:100%" title="Attachment">
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
            <div class="card-footer">
                <form action="/faults/{{$fault->id}}/remarks" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="Enter Your Remarks and Attach Your File Below If Any" rows="1"></textarea>
                        <input type="hidden" name="activity" value="ON ASSESSMENT"> 
                        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" id="fileToUpload">
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
