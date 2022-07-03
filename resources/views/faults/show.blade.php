@extends('layouts.admin')

@section('title')
Fault
@endsection

@section('content')
@include('partials.css')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-100">
            <div class="card-header">
                <h3 class="card-title">
                  {{_('View Details')}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>Customer Name</strong>
                        <p class="text-muted">{{ $fault->customer }}</p>
                    </div>

                    <div class="col">
                        <strong>City/Town</strong>
                        <p class="text-muted">{{ $fault->city }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-md-6">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col-md-2">
                        <strong>Location</strong>
                        <p class="text-muted">{{ $fault->suburb }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Link</strong>
                        <p class="text-muted">{{ $fault->link }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Pop</strong>
                        <p class="text-muted">{{ $fault->pop }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Phone Number</strong>
                        <p class="text-muted">{{ $fault->phoneNumber }}</p>
                    </div>

                    <div class="col">
                        <strong>Service Type</strong>
                        <p class="text-muted">{{ $fault->serviceType }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Email Address</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>

                    <div class="col">
                        <strong>Reason For Outage</strong>
                        <p class="text-muted">{{ $fault->suspectedRfo }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Physica Address</strong>
                        <p class="text-muted">{{ $fault->address }}</p>
                    </div>

                    <div class="col">
                        <strong>Service Attribute</strong>
                        <p class="text-muted">{{ $fault->serviceAttribute }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Account Manager</strong>
                        <p class="text-muted">{{ $fault->accountManager }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger" href="{{ route('faults.index') }}">{{ __('Close') }}</a>
                    <a href="{{ route('faults.edit',$fault->id) }}" class="btn btn-danger">Assess</a>
                </div>
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
        </div>
    </div>
 
</section>
@endsection

@section('scripts')

@endsection