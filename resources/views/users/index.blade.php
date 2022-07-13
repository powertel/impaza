@extends('layouts.admin')
@section('title')
Users
@endsection
@section('content')
@include('partials.css')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Users')}}</h3>
        <div class="card-tools">
            @can('user-create')
              <a  class="btn btn-primary btn-sm" href="{{ route('users.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create User')}} </a>  
            @endcan
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                        <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                    </td>
                    <td>
                        <form action="{{ route('users.destroy',$user->id) }}" method="POST">
                            <a class="btn btn-info btn-sm" style="padding:0px 2px; color:#fff;" href="{{ route('users.show',$user->id) }}">View</a>
                            @can('user-edit')
                            <a class="btn btn-primary btn-sm" style="padding:0px 2px; color:#fff;" href="{{ route('users.edit',$user->id) }}">Edit</a>   
                            @endcan

                            @csrf
                            @method('DELETE')
                            @can('user-delete')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:0px 2px; color:#fff;">Delete</button>
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody> 
        </table>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
