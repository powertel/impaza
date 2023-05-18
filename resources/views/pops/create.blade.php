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
                    {{_('Create Pop')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pops.store') }}" method="POST">
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
                    <label for="location" class="col-sm-3 col-form-label">Location</label>
                    <div class="col-sm-9">
                    <select id="suburb"  class="custom-select @error('suburb_id') is-invalid @enderror" name="suburb_id">
                        <option selected disabled>Select Suburb</option>
                        @foreach($location as $location)
                            @if (old('suburb_id')==$location->id)
                                <option value="{{ $location->id}}" selected>{{ $location->suburb }}</option>
                            @endif
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pop" class="col-sm-3 col-form-label">Pop</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('pop') is-invalid @enderror" name="pop" placeholder="Pop Name" value="{{ old('pop') }}">
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()"> Save</button>
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
