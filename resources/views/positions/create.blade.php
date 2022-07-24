@extends('layouts.admin')

@section('title')
Position
@endsection
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create Position')}}
                </h3>
            </div>

            <div class="card-body">
                <form action="{{ route('positions.store') }}" method="POST">
                {{ csrf_field() }}
        
                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Department</label>
                        <div class="col-sm-9">
                        <select id="department" class="custom-select  @error('department_id') is-invalid @enderror" name="department_id" value="{{ old('department_id') }}">
                                <option selected disabled >Select department</option>
                                @foreach($department as $department)
                                    @if (old('department_id')==$department->id)
                                        <option value="{{ $department->id}}" selected>{{ $department->department }}</option>
                                    @else
                                        <option value="{{ $department->id}}">{{ $department->department }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="section" class="col-sm-3 col-form-label">Section</label>
                        <div class="col-sm-9">
                            <select id="section"  class="custom-select @error('section_id') is-invalid @enderror" name="section_id">
                                <option selected disabled>Select section</option>
                                @foreach($section as $section)
                                    @if (old('section_id')==$section->id)
                                        <option value="{{ $section->id}}" selected>{{ $section->section }}</option>
                                    @endif
                                @endforeach
                            </select>
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
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('departments.index') }}">{{ __('Cancel') }}</a>

                    </div>
                </form> 
            </div> 
        </div>
    </div>
</section>
@endsection

@section('scripts')
    @include('partials.department')
@endsection
