@extends('layouts.admin')

@section('title')
Fault
@endsection

@section('content')
    @include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-100">
            <div class="card-header">
                <h3 class="card-title">{{_('Assess Fault')}}</h3>
            </div>
            <div class="card-body">
                <form  action="{{ route('faults.update', $fault->id ) }}" method="POST">
                ``<div class="row g-2">
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
                            <label for="serviceType" class="form-label">Service Type</label>
                            <input type="text" class="form-control" name="serviceType">
                        </div>

                    </div>
        
                    <div class="row g-2">
                        <div class="mb-3 col-md-2">
                            <label for="faultType" class="form-label">Fault Type</label>
                            <input type="text" class="form-control" name="faultType" >
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="faultType" class="form-label">Confirmed RFO</label>
                            <input type="text" class="form-control" name="faultType" >
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="actDepartment" class="form-label">Actioning Department</label>
                            <select id="actDepartment" class="form-select" name="department">
                                <option>Select</option>
                                <option>NOC</option>
                                <option>implementation</option>
                                <option>Operations</option>
                                <option>HR</option>
                                <option>Finace</option>
                                <option>Stores</option>
                                <option>Marketing</option>
                                <option>Procurement</option>
                            </select>
                        </div>                
                    </div>
                    <div class="row g-2">
                        <div class="mb-3 col-md-6">
                            <label for="priorityLevel" class="form-label">Priority Level</label>
                            <select id="priorityLevel" class="form-select" name="priorityLevel">
                                <option>Select</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Critical</option>
                            </select>
                        </div>                
                    </div>
                    
                    <div class="card-footer">
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}">{{ __('Cancel') }}</a>
                        <button type="button" class="btn btn-secondary btn-sm float-right">Revoke</button>              
                        <button type="submit" class="btn btn-success btn-sm float-right" >{{ __('Save') }}</button>
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