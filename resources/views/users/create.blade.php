@extends('layouts.admin')

@section('title')
User
@endsection
@include('partials.css')
@section('content')

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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="department" class="col-sm-3 col-form-label">Department</label>
                    <div class="col-sm-9">
                    <select id="department" class="custom-select  @error('department_id') is-invalid @enderror" name="department_id" value="{{ old('department_id') }}">
                            <option selected disabled >Select Department</option>
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
                            <option selected disabled>Select Section</option>
                            @foreach($section as $section)
                                @if (old('section_id')==$section->id)
                                    <option value="{{ $section->id}}" selected>{{ $section->section }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="posirion" class="col-sm-3 col-form-label">Position</label>
                    <div class="col-sm-9">
                        <select id="position"  class="custom-select @error('position_id') is-invalid @enderror" name="position_id">
                            <option selected disabled>Select Position</option>
                            @foreach($position as $position)
                                @if (old('position_id')==$position->id)
                                    <option value="{{ $position->id}}" selected>{{ $position->position }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="confirm-password" class="col-sm-3 col-form-label">Role</label>
                    <div class="col-sm-9">
                        {!! Form::select('roles[]', $roles,[], array('class' => 'custom-select')) !!}
                    </div>
                </div>

                
                <div class="form-group row">
                    <label for="status" class="col-sm-3 col-form-label">Status</label>
                    <div class="col-sm-9">
                        <select  class="custom-select @error('user_status') is-invalid @enderror" name="user_status" >
                            <option selected disabled >Select Status</option>
                            @foreach($user_statuses as $status)
                    
                                @if (old('user_status')==$status->id)
                                    <option value="{{ $status->id}}" selected>{{ $status->status_name }}</option>
                                @else
                                    <option value="{{ $status->id}}">{{ $status->status_name }}</option>
                                @endif

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="confirm-password" class="col-sm-3 col-form-label">Confirm Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password" placeholder="Confirm Ppassword">
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
