@extends('layouts.admin')

@section('title')
Link
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
                        <p class="text-muted">{{ $link->city }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>City/Town</strong>
                        <p class="text-muted">{{ $link->city }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Location</strong>
                        <p class="text-muted">{{ $link->suburb }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>Pop</strong>
                        <p class="text-muted">{{ $link->pop }}</p>
                    </div>
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col">
                        <strong>link</strong>
                        <p class="text-muted">{{ $link->link }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger" href="{{ route('links.index') }}">{{ __('Close') }}</a>
                </div>
            </div>
        </div>
    </div>
 
</section>
@endsection