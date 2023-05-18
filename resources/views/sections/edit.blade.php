@extends('layouts.admin')
@include('partials.css')
@section('content')

<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Update Section')}}
                </h3>
            </div>

            <div class="card-body">
                <form  action="{{ route('sections.update',$section->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group row">
                        <label for="section" class="col-sm-3 col-form-label">Section</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control" name="section" value="{{ $section->section }}">
                            @error ('section')

                                <div class="alert-danger">
                                     {{$message }}
                                </div>


                            @enderror
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="{{ route('sections.index') }}">{{ __('Cancel') }}</a>



                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
