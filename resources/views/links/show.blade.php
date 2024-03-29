@extends('layouts.admin')

@section('title')
Link
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
                        <p class="text-muted">{{ $link->customer }}</p>
                    </div>
                    <div class="col">
                        <strong>CITY/TOWN</strong>
                        <p class="text-muted">{{ $link->city }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>LOCATION</strong>
                        <p class="text-muted">{{ $link->suburb }}</p>
                    </div>
                    <div class="col">
                        <strong>POP</strong>
                        <p class="text-muted">{{ $link->pop}}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>LINK</strong>
                        <p class="text-muted">{{ $link->link }}</p>
                    </div>
                    <div class="col">
                        <strong>LINK TYPE</strong>
                        <p class="text-muted">{{ $link->linkType }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger btn-sm"href="{{ url()->previous() }}">{{ __('Close') }}</a>
                </div>
            </div>
        </div>
    </div>
 
</section>
@endsection