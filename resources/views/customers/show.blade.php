@extends('layouts.admin')

@section('title')
Customer
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
                        <p class="text-muted">CBZ</p>
                    </div>

                    <div class="col">
                        <strong>Service Type</strong>
                        <p class="text-muted">Helloe</p>
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