@extends('layouts.admin')

@section('title')
Customers
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">
    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Customers')}}</h3>
        <div class="card-tools">
            @can('customer-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('customers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Customer')}} </a>
            @endcan
            @can('link-create')
                <a  class="btn btn-primary btn-sm" href="{{ route('links.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Link')}} </a>
            @endcan
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="customersPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="customersSearch" class="form-control" placeholder="Search Customers">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#customersPageSize" data-pager="#customersPager" data-search="#customersSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                    <tr>
                        <td>{{++$i}}</td>
                        <td>{{ $customer->customer}}</td>
                        <td>
                        <form action="{{ route('customers.destroy',$customer->id) }}" method="POST">
                            <a href="{{ route('customers.show',$customer->id) }}" class="btn btn-sm btn-outline-success" style="padding:0px 2px;" >
                                <i class="fas fa-eye"></i>View
                            </a>
                            @can('account-manager-edit')
                            <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-sm btn-outline-danger" style="padding:0px 2px;" >
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            @endcan

                            @csrf
                            @method('DELETE')
                            @can('customer-delete')
                            <button type="button" class="btn btn-outline-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="padding:0px 2px;">
                                <i class="fas fa-trash"></i>Delete
                            </button> 
                            @endcan
                        </form>
                        
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="customersPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
