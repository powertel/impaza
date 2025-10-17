@extends('layouts.admin')

@section('title')
Pop
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                  View Details
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>CITY/TOWN</strong>
                        <p class="text-muted">{{ $pop->city }}</p>
                    </div>
                    <div class="col">
                        <strong>LOCATION</strong>
                        <p class="text-muted">{{ $pop->suburb }}</p>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col">
                        <strong>POP</strong>
                        <p class="text-muted">{{ $pop->pop }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger btn-sm" href="{{ route('pops.index') }}">{{ __('Close') }}</a>
                </div>
            </div>
        </div>
    </div>
 
</section>
@endsection
