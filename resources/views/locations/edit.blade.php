@extends('layouts.admin')

@section('title')
Location
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Update Location')}}
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('locations.update', $location->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="city" class="col-sm-3 col-form-label">city</label>
                        <div class="col-sm-9">
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
                        <label for="location" class="col-sm-3 col-form-label">Location</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="suburb" value="{{ $location->suburb}}">
                            @error ('suburb')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()" >{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('locations.index' ) }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection
