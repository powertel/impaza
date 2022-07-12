@extends('layouts.admin')

@section('title')
 Permissions
@endsection

@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
        <h3 class="card-title">Permission Table</h3>
        <div class="card-tools">
            <a href="{{ route('permission.create') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i></i>Create new permission</a>
        </div>
        </div>

        <div class="card-body">
         <table class="table table-hover text-nowrap">
        <thead>
             <tr>
        <th>ID</th>
        <th>Name</th>

        <th>Date</th><th>Action</th>

        </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $permission )
        <tr>
        <td>{{ $permission->id }}</td>
        <td>{{ $permission->name }}</td>
        <td>{{$permission->created_at  }}</td>
        <td>
            <a href="{{ route('permission.edit',$permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
        </td>

        </tr>
            @empty
            <tr><td>Results not Found</td></tr>
            @endforelse



        </tbody>
        </table>
        </div>

        </div>
</section>
@endsection
