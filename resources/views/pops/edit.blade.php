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
                    {{_('Update Pop')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('pops.update', $pop->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="city" class="col-sm-3 col-form-label">City/Town</label>
                        <div class="col-sm-9 ">
                            <select class="custom-select" id="city" name="city_id">
                                <option selected="selected" value="{{ $pop->city_id}}">{{ $pop->city }}</option>
                                @foreach($cities as $city)
                                    @unless ($city->id ===$pop->city_id)
                                        <option value="{{ $city->id}}">{{ $city->city }}</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-3 col-form-label">Location</label>
                        <div class="col-sm-9 ">
                        <select   class="custom-select" id="suburb" name="suburb_id">
                             <option selected="selected" value="{{ $pop->suburb_id}}">{{ $pop->suburb }}</option>
                                @foreach($suburbs as $suburb)
                                    @if ($suburb->city_id === $pop->city_id)
                                        @unless($suburb->id ===$pop->suburb_id)
                                            <option value="{{ $suburb->id}}">{{ $suburb->suburb }}</option>
                                        @endunless                                    
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pop" class="col-sm-3 col-form-label">Pop</label>
                        <div class="col-sm-9 ">
                            <input type="text" class="form-control" name="pop" value="{{ $pop->pop}}">
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
