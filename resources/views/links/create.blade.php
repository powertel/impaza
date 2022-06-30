@extends('layouts.admin')

@section('title')
Link
@endsection

@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create Link')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('links.store') }}" method="POST">
                {{ csrf_field() }}
                    
                    <div class="form-group row">
                        <label for="customer" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10">
                            <select id="customer" class="custom-select " name="customer_id">
                                <option selected disabled >Select Customer Name</option>
                                @foreach($customer as $customer)
                                    <option value="{{ $customer->id}}">{{ $customer->customer }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label for="city" class="col-sm-2 col-form-label">City/Town</label>
                    <div class="col-sm-10">
                        <select id="city" class="custom-select " name="city_id">
                            <option selected disabled >Select city name</option>
                            @foreach($city as $city)
                                <option value="{{ $city->id}}">{{ $city->city}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="location" class="col-sm-2 col-form-label">Location</label>
                    <div class="col-sm-10">
                    <select id="suburb"  class="custom-select" name="suburb_id">
                        <option selected disabled>Select Suburb</option>
                     </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pop" class="col-sm-2 col-form-label">Pop</label>
                    <div class="col-sm-10">
                        <select id="pop"  class="custom-select " name="pop_id" >
                            <option selected disabled>Select Pop</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pop" class="col-sm-2 col-form-label">Link</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="link" placeholder="Link Name">
                    </div>
                </div>

           
                    <div class="card-footer">
                        <a type="button" class="btn btn-danger" href="javascript:history.back()">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-success float-right">{{ __('Save') }}</button>
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
