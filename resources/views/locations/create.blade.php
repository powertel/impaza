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
                    {{_('Create Location')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('locations.store') }}" method="POST">
                {{ csrf_field() }}
        
                <div class="form-group row">
                        <label for="city" class="col-sm-3 col-form-label">City/Town</label>
                        <div class="col-sm-9">
                        <select id="city" class="custom-select @error('city_id') is-invalid @enderror" name="city_id">
                            <option selected disabled  >Select City/Town</option>
                            @foreach($city as $city)
                                @if (old('city_id')==$city->id)
                                    <option value="{{ $city->id}}" selected>{{ $city->city }}</option>
                                @else
                                    <option value="{{ $city->id}}">{{ $city->city }}</option>
                                @endif
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="suburb" class="col-sm-3 col-form-label">Location</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('suburb') is-invalid @enderror" name="suburb" placeholder="Location" value="{{ old('suburb') }}">
                        </div>
                    </div>
           
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm float-right">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('locations.index' ) }}">{{ __('Cancel') }}</a>
                    </div>
                </form> 
            </div> 
        </div>
    </div>
 
</section>
@endsection
