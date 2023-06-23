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
                  {{_('View Details')}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>CUSTOMER</strong>
                        <p class="text-muted">{{ $fault->customer }}</p>
                    </div>

                    <div class="col">
                        <strong>CITY/TOWN</strong>
                        <p class="text-muted">{{ $fault->city }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-md-6">
                        <strong>CONTACT NAME</strong>
                        <p class="text-muted">{{ $fault->contactName }}</p>
                    </div>

                    <div class="col-md-2">
                        <strong>LOCATION</strong>
                        <p class="text-muted">{{ $fault->suburb }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>LINK</strong>
                        <p class="text-muted">{{ $fault->link }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>POP</strong>
                        <p class="text-muted">{{ $fault->pop }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>PHONE NUMBER</strong>
                        <p class="text-muted">{{ $fault->phoneNumber }}</p>
                    </div>

                    <div class="col">
                        <strong>SERVICE TYPE</strong>
                        <p class="text-muted">{{ $fault->serviceType }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>EMAIL ADDRESS</strong>
                        <p class="text-muted">{{ $fault->contactEmail }}</p>
                    </div>

                    <div class="col">
                        <strong>SUSPECTED REASON FOR OUTAGE</strong>
                        <p class="text-muted">{{ $fault->suspectedRfo }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>PHYSICAL ADDRESS</strong>
                        <p class="text-muted">{{ $fault->address }}</p>
                    </div>

                    <div class="col">
                        <strong>ACCOUNT MANAGER</strong>
                        <p class="text-muted">{{ $fault->accountManager }}</p>
                    </div>
                </div>
                <hr>

                <div class="row g-2">
                    <div class="col">
                        <strong>REASON FOR OUTAGE</strong>
                        <p class="text-muted">{{ $fault->confirmedRfo }}</p>
                    </div>

                    <div class="col">
                        <strong>FAULT TYPE</strong>
                        <p class="text-muted">{{ $fault->faultType }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>PRIORITY LEVEL</strong>
                        <p class="text-muted">{{ $fault->priorityLevel }}</p>
                    </div>
                </div>
                <hr>

                <div class="card-footer">
                    @can('fault-assessment')
                        <a type="button" class="btn btn-primary btn-sm" href="{{ route('assessments.edit',$fault->id) }}">Assess</a>
                    @endcan
                    @can('assign-fault')
                    <a type="button" class="btn btn-Success btn-sm" href="{{ route('assign.edit',$fault->id) }}">Assign</a>
                    @endcan
                    <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Close') }}</a>
                </div>
            </div> 
        </div>
        <div class="card  w-50">
            <div class="card-header">
                <h3 class="card-title">{{_('Remarks')}}</h3>
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
                </div>
                @endforeach
            </div> 
        </div>
    </div>
</section>
@endsection

@section('scripts')

@endsection