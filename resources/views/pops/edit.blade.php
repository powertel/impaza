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
                        <label for="city" class="col-sm-2 col-form-label">City/Town</label>
                        <div class="col-sm-10">
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
                        <label for="location" class="col-sm-2 col-form-label">Location</label>
                        <div class="col-sm-10">
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
                        <label for="pop" class="col-sm-2 col-form-label">Pop</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="pop" value="{{ $pop->pop}}">
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
