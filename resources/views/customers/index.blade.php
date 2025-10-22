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
        <h3 class="card-title">Customers</h3>
        <div class="card-tools">
            @can('customer-create')
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#customerCreateModal"><i class="fas fa-plus-circle"></i> Create Customer(s) </button>
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
                        <th>Account Manager</th>
                        <th>Account Number</th>
+                       <th>Address</th>
+                       <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                    <tr>
                        <td>{{++$i}}</td>
                        <td>{{ $customer->customer}}</td>
                        <td>{{ $customer->accountManager }}</td>
                        <td>{{ $customer->account_number }}</td>
+                       <td>{{ $customer->address ?? '' }}</td>
+                       <td>{{ $customer->contact_number ?? '' }}</td>
                        <td>
                            <button type="button" class="btn  btn-outline-success"  data-bs-toggle="modal" data-bs-target="#customerViewModal{{ $customer->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            @can('customer-edit')
                            <button type="button" class="btn  btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#customerEditModal{{ $customer->id }}">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            @endcan
                            @can('customer-delete')
                            <button type="button" class="btn btn-outline-danger "  data-bs-toggle="modal" data-bs-target="#customerDeleteModal{{ $customer->id }}">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button> 
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="customersPager" class="mt-2"></div>

            @include('customers.create_modal')
            @include('customers.edit_modal')
            @include('customers.view_modal')
            @include('customers.delete_modal')
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection



