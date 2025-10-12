@extends('layouts.admin')

@section('title')
Departments
@endsection

@include('partials.css')

@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0">{{_('Departments')}}</h3>
        <div class="card-tools ms-auto d-flex align-items-center gap-2">
            @can('department-create')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#departmentCreateModal"><i class="fas fa-plus-circle"></i> {{_('Create Department(s)')}} </button>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="departmentsPageSize" class="form-control">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="departmentsSearch" class="form-control" placeholder="Search departments">
                </div>
            </div>

            <table id="departmentsTable" class="table table-hover align-middle js-paginated-table" data-page-size="20" data-page-size-control="#departmentsPageSize" data-pager="#departmentsPager" data-search="#departmentsSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $department->department }}</td>
                        <td class="text-nowrap">
                            
                                @can('department-edit')
                                <button  class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#departmentEditModal{{ $department->id }}">
                                    <i class="fas fa-edit"></i>Edit
                                </button>
                                @endcan
                                @can('department-delete')
                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger show_confirm" data-name="{{ $department->department }}" title="Delete">
                                        <i class="fas fa-trash"></i>Delete
                                    </button>
                                </form>
                                @endcan
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="departmentsPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>


</section>

@include('departments.create_modal')
@include('departments.edit_modal')

@endsection

@section('scripts')
@endsection
