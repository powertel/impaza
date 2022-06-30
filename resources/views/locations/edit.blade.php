@extends('layouts.admin')

@section('title')
Location
@endsection

@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Update Location')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('locations.update', $location->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="city" class="col-sm-2 col-form-label">city</label>
                        <div class="col-sm-10">
                            <select class="custom-select" id="city" name="city_id">
                                <option selected="selected" value="{{ $location->city_id}}">{{ $location->city }}</option>
                                @foreach($cities as $city)
                                    @unless ($city->id ===$location->city_id)
                                        <option value="{{ $city->id}}">{{ $city->city }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label">Location</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="suburb" value="{{ $location->suburb}}">
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
