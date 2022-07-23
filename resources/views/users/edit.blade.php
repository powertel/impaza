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
                    {{_('Edit User')}}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id ) }}" method="POST">
                @csrf
                @method('PUT')
        
                <div class="form-group row">
                    <label for="name" class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$user->name }}"  value="{{ old('name') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$user->email }}"  value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="department" class="col-sm-3 col-form-label">Department</label>
                    <div class="col-sm-9">
                    <select id="department" class="custom-select  @error('department_id') is-invalid @enderror" name="department_id" value="{{ old('department_id') }}">
                    <option selected="selected" value="{{ $user->department_id}}">{{ $user->department}}</option>
                            @foreach($department as $depart)
                                @if ($depart->id === $user->department_id)
                                    <option value="{{ $depart->id}}">{{ $depart->department }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="section" class="col-sm-3 col-form-label">Section</label>
                    <div class="col-sm-9">
                        <select id="section"  class="custom-select @error('section_id') is-invalid @enderror" name="section_id">
                        <option selected="selected" value="{{ $user->section_id}}">{{ $user->section }}</option>
                            @foreach($section as $sect)
                                @if ($sect->department_id===$user->department_id)
                                    @unless ($sect->id===$user->section_id)
                                    <option value="{{ $sect->id}}">{{ $sect->section }}</option>
                                    @endunless
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="posirion" class="col-sm-3 col-form-label">Position</label>
                    <div class="col-sm-9">
                        <select id="position"  class="custom-select @error('position_id') is-invalid @enderror" name="position_id">
                        <option selected="selected" value="{{ $user->position_id}}">{{ $user->position }}</option>
                            @foreach($position as $position)
                                @if ($position->section_id === $user->section_id)
                                    @unless ($position->id === $user->position_id)
                                        <option value="{{ $position->id}}">{{ $position->position }}</option>
                                    @endunless  
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="role" class="col-sm-3 col-form-label">Role</label>
                    <div class="col-sm-9">
                    {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('password') is-invalid @enderror" name="password" >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="confirm-password" class="col-sm-3 col-form-label">Confirm Password</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password" >
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
    @include('partials.department')
@endsection

