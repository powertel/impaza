@extends('layouts.admin')

@section('title')
Materials
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                  {{_('View Details')}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>Fault Ref No.</strong>
                        <p class="text-muted">{{ $stores->fault_ref_number }}</p>
                    </div>
                    <div class="col">
                        <strong>Faulty Type</strong>
                        <p class="text-muted">{{ $stores->faultType }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>SAP Ref No.</strong>
                        <p class="text-muted">{{ $stores->SAP_ref }}</p>
                    </div>
                    <div class="col">
                        <strong>Materials</strong>
                        <p class="text-muted">{{ $stores->materials}}</p>
                    </div>
                </div>
                {{--  --}}
                <div class="card-footer">
                    <a type="button" class="btn btn-danger btn-sm"href="{{ url()->previous() }}">{{ __('Close') }}</a>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
