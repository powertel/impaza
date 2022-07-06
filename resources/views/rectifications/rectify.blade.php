@extends('layouts.admin')

@section('title')
Rectify
@endsection

@section('content')
    @include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card  w-100">
            <div class="card-header">
                <h3 class="card-title">{{_('Fault Rectification')}}</h3>
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
                    
    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm " >{{ __('Save') }}</button>
                        <button type="button" class="btn btn-secondary btn-sm" >Revoke</button>   
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}" >{{ __('Cancel') }}</a>           
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