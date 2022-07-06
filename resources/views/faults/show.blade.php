@extends('layouts.admin')

@section('title')
Fault
@endsection

@section('content')
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
                        <strong>REASON FOR OUTAGE</strong>
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
                        <strong>SERVICE ATTRIBUTE</strong>
                        <p class="text-muted">{{ $fault->serviceAttribute }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>ACCOUNT MANAGER</strong>
                        <p class="text-muted">{{ $fault->accountManager }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger btn-sm" href="{{ route('faults.index') }}">{{ __('Close') }}</a>
                    <a href="{{ route('faults.edit',$fault->id) }}" class="btn btn-success btn-sm float-right">Assess</a>
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