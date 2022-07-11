@extends('layouts.admin')

@section('title')
Account Manager
@endsection

@section('content')
@include('partials.css')
<section class="content">
    <div class="col d-flex justify-content-center">
        <div class="card w-50">
            <div class="card-header">
                <h3 class="card-title">
                    {{_('Create Account Manager')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('account_managers.store') }}" method="POST">
                {{ csrf_field() }}
                    <div class="form-group row">
                        <label for="account_manger" class="col-sm-4 col-form-label">Account Manager</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('accountManager') is-invalid @enderror" name="accountManager" placeholder="Account Manager" value="{{ old('accountManager') }}">
                        </div>
                    </div>
           
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm"href="{{ route('account_managers.index' ) }}">{{ __('Cancel') }}</a>
                    </div>
                </form> 
            </div> 
        </div>
    </div>
 
</section>
@endsection
