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
                    {{_('Update City')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('cities.update', $city->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="city" class="col-sm-2 col-form-label">City/Town</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="city" value="{{$city->city }}">
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
