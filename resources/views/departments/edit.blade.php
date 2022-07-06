@extends('layouts.admin')
@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Update Department')}}
                </h3>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your selection.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card-body">
                <form  action="{{ route('departments.update',$department->id) }}" method="POST">
                    @csrf
                    @method('PUT')
        
                    <div class="form-group row">
                        <label for="department" class="col-sm-3 col-form-label">Department</label>
                        <div class="col-sm-9">
                            <input type="text"  class="form-control" name="department" value="{{ $department->department }}">
                        </div>
                    </div>
           
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm float-right">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm" href="javascript:history.back()">{{ __('Cancel') }}</a>
                    </div>
                </form> 
            </div> 
        </div>
    </div>
</section>

@endsection
