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
            @php($perPage = request('per_page', 20))
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 160px;">
                        <span class="input-group-text"><i class="fas fa-list-ol me-1"></i> Show</span>
                        <select id="usersPageSize" class="form-select form-select-sm">
                            <option value="10" {{ (string)$perPage === '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ (string)$perPage === '20' ? 'selected' : '' }}>20</option>
                            <option value="50" {{ (string)$perPage === '50' ? 'selected' : '' }}>50</option>
                            <option value="100" {{ (string)$perPage === '100' ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <form method="GET" action="{{ route('users.index') }}" class="d-flex align-items-center" style="max-width: 320px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search users...">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        <a href="{{ route('users.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                    </div>
                </form>
            </div>
            <table class="table table-hover align-middle">
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
                    @forelse ($users as $user)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <label class="badge rounded-pill badge-info">{{ $v }}</label>
                            @endforeach
                        @endif
                        </td>
                        <td>{{ $user->department }}</td>
                        <td>{{ $user->section}}</td>
                    <!--  <td>{{ $user->status_name}}</td> -->

                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\UserStatus::STATUS_COLOR[ $user->status_name ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{ $user->status_name }}
                            </span>
                        </td>

                        <td>
                            <form action="{{ route('users.destroy',$user->id) }}" method="POST">
                                <button type="button" class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showUserModal-{{ $user->id }}">
                                <i class="fas fa-eye me-1"></i> View
                                </button>
                                @can('user-edit')
                                <button type="button" class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}">
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
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-info-circle me-1"></i> No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-2">
                <div class="text-muted small">
                    Showing {{ $users->count() ? ($users->firstItem().'–'.$users->lastItem()) : 0 }} of {{ $users->total() }} results
                </div>
                <div>
                    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>

@include('users.create_modal', ['roles' => $roles, 'department' => $department, 'section' => $section, 'position' => $position, 'user_statuses' => $user_statuses, 'regions' => $regions, 'currentUserRegion' => $currentUserRegion])

@foreach ($users as $user)
  @include('users.show_modal', ['user' => $user])
  @include('users.edit_modal', ['user' => $user, 'department' => $department, 'section' => $section, 'position' => $position, 'roles' => $roles, 'user_statuses' => $user_statuses, 'regions' => $regions])
  @include('users.change_password_modal', ['user' => $user])
@endforeach

</section>
@endsection

@section('scripts')
@include('partials.users')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('usersPageSize');
    if (sel) {
        sel.addEventListener('change', function(ev) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', ev.target.value);
            params.delete('page');
            window.location.assign(window.location.pathname + '?' + params.toString());
        });
    }
});
</script>
@endsection

