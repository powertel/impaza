@extends('layouts.admin')
@section('title')
Users
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Users</h3>
        <div class="card-tools">
            @can('user-create')
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal"><i class="fas fa-plus-circle"></i>Create User</button>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="usersPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="usersSearch" class="form-control" placeholder="Search users">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#usersPageSize" data-pager="#usersPager" data-search="#usersSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Department</th>
                        <th>Section</th>
                        <th>Status</th>
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
                        <td>{{ $user->department }}</td>
                        <td>{{ $user->section}}</td>
                    <!--  <td>{{ $user->status_name}}</td> -->

                        <td style="background-color: {{ App\Models\UserStatus::STATUS_COLOR[ $user->status_name ] ?? 'none' }};">
                        <strong>{{$user->status_name}}</strong>
                        </td>

                        <td>
                            <form action="{{ route('users.destroy',$user->id) }}" method="POST">
                                <button type="button" class="btn btn-outline-success btn-sm" style="padding:0px 2px;" data-bs-toggle="modal" data-bs-target="#showUserModal-{{ $user->id }}">
                                <i class="fas fa-eye me-1"></i> View
                                </button>
                                @can('user-edit')
                                <button type="button" class="btn btn-outline-primary btn-sm" style="padding:0px 2px;" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
                                <i class="fas fa-edit me-1"></i>  Edit
                                </button>
                                @endcan

                               <!--  @csrf
                                @method('DELETE')
                                @can('user-delete')
                                <button type="button" class="btn btn-outline-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="padding:0px 2px; color:#fff;">
                                    <i class="fas fa-trash"></i>Delete
                                </button> 
                                @endcan -->
                            </form>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="usersPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>

@include('users.create_modal', ['roles' => $roles, 'department' => $department, 'section' => $section, 'position' => $position, 'user_statuses' => $user_statuses])

@foreach ($users as $user)
  @include('users.show_modal', ['user' => $user])
  @include('users.edit_modal', ['user' => $user, 'department' => $department, 'section' => $section, 'position' => $position, 'roles' => $roles, 'user_statuses' => $user_statuses])
@endforeach

</section>
@endsection

@section('scripts')
@include('partials.users')
@endsection

