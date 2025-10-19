@extends('layouts.admin')

@section('title')
Account Managers
@endsection

@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Account Manager</h3>
        <div class="card-tools">
            @can('account-manager-create')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#accountManagerCreateModal">
                <i class="fas fa-plus-circle"></i>Create Account Manager
            </button>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="accountManagersPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="accountManagersSearch" class="form-control" placeholder="Search Account managers">
                </div>
            </div>
            <table class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#accountManagersPageSize" data-pager="#accountManagersPager" data-search="#accountManagersSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Account Manager</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($account_managers as $acc_manager)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $acc_manager->name ?? '—' }}</td>
                        <td>
                            <form action="{{ route('account_managers.destroy',$acc_manager->id) }}" method="POST">
                                <button type="button" class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#accountManagerViewModal{{ $acc_manager->id }}">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                                @can('account-manager-edit')
                                <button type="button" class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#accountManagerEditModal{{ $acc_manager->id }}">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                @endcan

                                @csrf
                                @method('DELETE')
                                @can('account-manager-delete')
                                <button type="button" class="btn btn-outline-danger show_confirm" data-toggle="tooltip" title='Delete' >
                                <i class="fas fa-trash me-1"></i>  Delete
                                </button> 
                                @endcan
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="accountManagersPager" class="mt-2"></div>

        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@include('account_managers.create_modal')
@include('account_managers.edit_modal')
@include('account_managers.view_modal')
@endsection

