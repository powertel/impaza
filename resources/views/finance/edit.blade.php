@extends('layouts.admin')

@section('title')
Finance
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    Confirm Link
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('finance.update', $link->id ) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row">
                        <label for="customer" class="col-sm-3 col-form-label">Customer</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="customer" name="customer_id">
                                <option selected="selected" value="{{ $link->customer_id}}">{{ $link->customer }}</option>
                                @foreach($customers as $customer)
                                    @unless ($customer->id ===$link->customer_id)
                                        <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="link" class="col-sm-3 col-form-label">Link</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="link"  value="{{ $link->link}}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contract_number" class="col-sm-3 col-form-label">Contract Number</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="Enter Contract Number" class="form-control @error('contract_number') is-invalid @enderror"  name="contract_number" value="{{ $link->contract_number}}">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('finance.index' ) }}">{{ __('Cancel') }}</a>
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

