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
                    {{_('Create Customer')}}
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('customers.store') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-group row">
                        <label for="customer" class="col-sm-3 col-form-label">Customer</label>
                        <div class="col-sm-9 ">
                            <input type="text" class="form-control @error('customer') is-invalid @enderror" name="customer" placeholder="Customer Name" value="{{ old('customer') }}">
                            @error ('customer')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror
                        </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return inlineSave()">{{ __('Save') }}</button>
                    <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Cancel') }}</a>
                </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection
@section('scripts')
    @include('partials.scripts')
@endsection
