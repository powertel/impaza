@extends('layouts.admin')
@section('title')
Roles
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Roles</h3>
        <div class="card-tools">
        <a  class="btn btn-primary btn-sm" href="{{ route('roles.create') }}"><i class="fas fa-plus-circle"></i>{{_('Add New Role')}} </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role )
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach ($role->permissions as $permission )
                                <button class="btn btn-warning btn-sm" role="button"><i class="fas fa-shield-alt"></i> {{ $permission->name }}</button>
                            @endforeach
                        </td>
                        <td>
                            <a class="btn btn-info btn-sm" href="{{ route('roles.show',$role->id) }}">View</a>
                            <a class="btn btn-danger btn-sm" href="{{ route('roles.edit',$role->id) }}">Edit</a>
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td><i class="fas fa-folder-open"></i> No Record found</td><td></td><td></td><td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
