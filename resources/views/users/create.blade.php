@extends('layouts.admin')

@section('title')
User
@endsection

@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create User')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                {{ csrf_field() }}
        
                <div class="form-group row">
                    <label for="name" class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="confirm-password" class="col-sm-3 col-form-label">Confrim Password</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password" placeholder="Confirm Ppassword">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="confirm-password" class="col-sm-3 col-form-label">Confrim Password</label>
                    <div class="col-sm-9">
                        {!! Form::select('roles[]', $roles,[], array('class' => 'custom-select')) !!}
                    </div>
                </div>
        
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                    <a type="button" class="btn btn-danger btn-sm" href="{{ route('users.index') }}">{{ __('Cancel') }}</a>
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
