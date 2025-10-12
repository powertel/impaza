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
        <h3 class="card-title">{{_('Account Manager')}}</h3>
        <div class="card-tools">
            @can('account-manager-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('account_managers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Account Manager')}} </a>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <label for="accountManagersPageSize" class="mb-0 small text-muted">Show</label>
            <select id="accountManagersPageSize" class="form-select form-select-sm" style="width:auto;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>
            <input id="accountManagersSearch" type="search" class="form-control form-control-sm" placeholder="Search..." style="max-width:240px;">
        </div>
        <table class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#accountManagersPageSize" data-pager="#accountManagersPager" data-search="#accountManagersSearch">
            <thead>
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
                    <td>{{ $acc_manager->accountManager}}</td>
                    <td>
                        <form action="{{ route('account_managers.destroy',$acc_manager->id) }}" method="POST">
                            <a href="{{ route('account_managers.show',$acc_manager->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                            @can('account-manager-edit')
                            <a href="{{ route('account_managers.edit',$acc_manager->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                            @endcan

                            @csrf
                            @method('DELETE')
                            @can('account-manager-delete')
                            <button type="button" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="padding:0px 2px; color:#fff;">Delete</button> 
                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody> 
        </table>
        <div id="accountManagersPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
