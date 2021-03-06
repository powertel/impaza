@extends('layouts.admin')

@section('title')
Department
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create Department')}}
                </h3>
            </div>

            <div class="card-body">
                <form action="{{ route('departments.store') }}" method="POST">
                {{ csrf_field() }}
        
                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Department</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control @error('department') is-invalid @enderror" name="department" placeholder="Department Name" value="{{ old('department') }}">
                            @error ('department')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                            @enderror

                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="section" class="col-sm-3 col-form-label">Section</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control @error('section') is-invalid @enderror" name="section" placeholder="Section" value="{{ old('section') }}">
                            @error ('section')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                            @enderror

                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="position" class="col-sm-3 col-form-label">Position</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control @error('position') is-invalid @enderror" name="position" placeholder="Position" value="{{ old('position') }}">
                            @error ('position')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                            @enderror
                        </div>
                    </div>
           
                    <div class="card-footer">

                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ url()->previous() }}">{{ __('Cancel') }}</a>

                    </div>
                </form> 
            </div> 
        </div>
    </div>
</section>
@endsection
