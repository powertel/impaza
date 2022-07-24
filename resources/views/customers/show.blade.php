@extends('layouts.admin')

@section('title')
Customer
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
                        <strong>CUSTOMER</strong>
                        <p class="text-muted">{{ $customer->customer }}</p>
                    </div>
                    <div class="col">
                        <strong>CITY/TOWN</strong>
                        <p class="text-muted">{{ $customer->city }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>LOCATION</strong>
                        <p class="text-muted">{{ $customer->suburb }}</p>
                    </div>
                    <div class="col">
                        <strong>POP</strong>
                        <p class="text-muted">{{ $customer->pop}}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>LINK</strong>
                        <p class="text-muted">{{ $customer->link }}</p>
                    </div>
                </div>
                <hr>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger btn-sm" href="{{ route('customers.index') }}">{{ __('Close') }}</a>
                </div>
            </div> 
        </div>
    </div>
 
</section>
@endsection