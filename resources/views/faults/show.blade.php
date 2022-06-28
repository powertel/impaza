@extends('layouts.admin')

@section('title')
Faults
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
                        <p class="text-muted">{{ $fault->customerName }}</p>
                    </div>

                    <div class="col">
                        <strong>Service Type</strong>
                        <p class="text-muted">{{ $fault->serviceType }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-md-6">
                        <strong>Contact Name</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col-md-2">
                        <strong>Fault Locale</strong>
                        <p class="text-muted">{{ $fault->city }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Suburb</strong>
                        <p class="text-muted">{{ $fault->suburb }}</p>
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
                        <strong>Link</strong>
                        <p class="text-muted">{{ $fault->linkName }}</p>
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

                    <div class="col">
                        <strong>Remarks</strong>
                        <p class="text-muted">{{ $fault->remarks }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger" href="{{ route('faults.index') }}">{{ __('Close') }}</a>
                    <a href="{{ route('faults.edit',$fault->id) }}" class="btn btn-danger">Assess</a>
                </div>
            </div> 
        </div>
    </div>
 
</section>
@endsection

@section('scripts')

@endsection