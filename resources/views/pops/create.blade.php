@extends('layouts.admin')

@section('title')
Pop
@endsection

@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create Pop')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pops.store') }}" method="POST">
                {{ csrf_field() }}
        
                <div class="form-group row">
                    <label for="city" class="col-sm-3 col-form-label">City/Town</label>
                    <div class="col-sm-9">
                        <select id="city" class="custom-select " name="city_id" value="{{ old('city_id') }}">
                            <option selected disabled >Select city name</option>
                            @foreach($city as $city)
                                <option value="{{ $city->id}}">{{ $city->city}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="location" class="col-sm-3 col-form-label">Location</label>
                    <div class="col-sm-9">
                    <select id="suburb"  class="custom-select" name="suburb_id" value="{{ old('suburb_id') }}">
                        <option selected disabled>Select Suburb</option>
                     </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pop" class="col-sm-3 col-form-label">Pop</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="pop" placeholder="Pop Name" value="{{ old('pop') }}">
                        @error ('pop')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                        @enderror
                    </div>
                </div>
        
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                    <a type="button" class="btn btn-danger btn-sm" href="{{ route('pops.index') }}">{{ __('Cancel') }}</a>
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
