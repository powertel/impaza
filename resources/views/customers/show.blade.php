@extends('layouts.admin')

@section('title')
Customer
@endsection

@section('content')
@include('partials.css')
@include('partials.css')
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
                        <strong>Customer</strong>
                        <p class="text-muted">{{ $customer->customer }}</p>
                        <p class="text-muted">{{ $customer->city }}</p>
                        <p class="text-muted">{{ $customer->suburb }}</p>
                        <p class="text-muted">{{ $customer->pop}}</p>
                        <p class="text-muted">{{ $customer->link }}</p>
                    </div>
                </div>
                <hr>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger" href="{{ route('customers.index') }}">{{ __('Close') }}</a>
                </div>
            </div> 
        </div>
    </div>
 
</section>
@endsection