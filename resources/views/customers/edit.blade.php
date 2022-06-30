@extends('layouts.admin')

@section('title')
Customer
@endsection

@section('content')
@include('partials.css')
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
                        <label for="customer" class="col-sm-2 col-form-label">Customer</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="customer"  value="{{ $customer->customer}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="city" class="col-sm-2 col-form-label">City/Town</label>
                        <div class="col-sm-10">
                            <select class="custom-select" id="city" name="city_id">
                                <option selected="selected" value="{{ $customer->city_id}}">{{ $customer->city }}</option>
                                @foreach($cities as $city)
                                    @unless ($city->id ===$customer->city_id)
                                        <option value="{{ $city->id}}">{{ $city->city }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label">Location</label>
                        <div class="col-sm-10">
                        <select   class="custom-select" id="suburb" name="suburb_id">
                             <option selected="selected" value="{{ $customer->suburb_id}}">{{ $customer->suburb }}</option>
                                @foreach($suburbs as $suburb)
                                    @if ($suburb->city_id === $customer->city_id)
                                        @unless($suburb->id ===$customer->suburb_id)
                                            <option value="{{ $suburb->id}}">{{ $suburb->suburb }}</option>
                                        @endunless                                    
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pop" class="col-sm-2 col-form-label">Pop</label>
                        <div class="col-sm-10">
                            <select  class="custom-select" id="pop" name="pop_id" >
                                <option selected="selected" value="{{ $customer->pop_id}}">{{ $customer->pop }}</option>
                                @foreach($pops as $pop)
                                    @if($pop->suburb_id === $customer->suburb_id)
                                        @unless($pop->id ===$customer->pop_id)
                                            <option value="{{ $pop->id}}">{{ $pop->pop }}</option>
                                        @endunless                                        
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="link" class="col-sm-2 col-form-label">Link</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="link"  value="{{ $customer->link}}">
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
