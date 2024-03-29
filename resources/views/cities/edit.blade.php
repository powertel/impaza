@extends('layouts.admin')

@section('title')
City
@endsection

@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Update City')}}
                </h3>
            </div>
            <div class="card-body">
                <form id="UF" action="{{ route('cities.update', $city->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="city" class="col-sm-2 col-form-label">City/Town</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="city" value="{{$city->city }}">
                            @error ('city')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()">{{ __('Save') }}</button>

                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('cities.index' ) }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection
