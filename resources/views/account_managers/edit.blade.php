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
                    {{_('Update Account Manager')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('account_managers.update', $acc_manager->id ) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="account_manager" class="col-sm-4 col-form-label">Account Manager</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="accountManager" value="{{$acc_manager->accountManager }}">
                            @error ('accountManager')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>                                
                            @enderror
                        </div>
                    </div>
           
                    <div class="card-footer">
                        <a type="button" class="btn btn-danger" href="javascript:history.back()">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-success btn-sm float-right">{{ __('Save') }}</button>
                    </div>
                </form> 
            </div> 
        </div>
    </div>
 
</section>
@endsection
