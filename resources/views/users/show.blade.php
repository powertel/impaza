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
                  {{_('View Details')}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col">
                        <strong>Name</strong>
                        <p class="text-muted">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="row g-2">
                <div class="col">
                        <strong>Email</strong>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <strong>Roles:</strong>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <label class="badge badge-success">{{ $v }}</label>
                            @endforeach
                        @endif
                    </div>
                </div>
                <hr>
                <div class="card-footer">
                    <a type="button" class="btn btn-danger btn-sm" href="{{ route('users.index') }}">{{ __('Close') }}</a>
                </div>
            </div> 
        </div>
    </div>
 
</section>
@endsection

@section('scripts')

@endsection