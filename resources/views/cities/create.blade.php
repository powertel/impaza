@extends('layouts.admin')

@section('title')
City
@endsection

@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create City')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('cities.store') }}" method="POST">
                {{ csrf_field() }}
        
                    <div class="form-group row">
                        <label for="city" class="col-sm-3 col-form-label">City/Town</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control" name="city" placeholder="City Name" value="{{ old('city') }}">
                            @error ('city')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                            @enderror
                        </div>
                    </div>
           
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('cities.index' ) }}">{{ __('Cancel') }}</a>
                    </div>
                </form> 
            </div> 
        </div>
    </div>
 
</section>
@endsection
