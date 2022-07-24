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
                    {{_('Update customer')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="customer" class="col-sm-3  col-form-label">Customer</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="customer"  value="{{ $customer->customer}}">
                            @error ('customer')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('customers.index') }}">{{ __('Cancel') }}</a>
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
