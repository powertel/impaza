@extends('layouts.admin')

@section('title')
Account Manager
@endsection

@include('partials.css')
@section('content')

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
                            <input type="text" class="form-control @error('accountManager') is-invalid @enderror" name="accountManager" value="{{$acc_manager->accountManager }}">
                            @error ('accountManager')
                                <div class="alert-danger">
                                     {{$message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return submitResult()">{{ __('Save') }}</button>
                        <a type="button" class="btn btn-danger btn-sm"href="{{ route('account_managers.index' ) }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection
