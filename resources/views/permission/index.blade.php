@extends('layouts.admin')

@section('title')
 Permissions
@endsection
@include('partials.css')
@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Permission Table</h3>
            <div class="card-tools">
               <!--  <a href="{{ route('permission.create') }}" class="btn btn-primary"><i class="fa fa-plus-circle"></i></i>Create new permission</a> -->
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                    <div class="input-group input-group-sm" style="width: 170px;">
                        <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                        <select id="permissionPageSize" class="form-select form-select-sm" style="width:auto;">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <input type="text" id="permissionSearch" class="form-control" placeholder="Search permission">
                    </div>
                </div>
                <table class="table table-hover text-nowrap js-paginated-table" data-page-size="20" data-page-size-control="#permissionPageSize" data-pager="#permissionPager" data-search="#permissionSearch">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission )
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{$permission->created_at  }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="permissionPager" class="mt-2"></div>
            </div>
        </div>
    </div>
</section>
@endsection
