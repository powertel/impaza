@extends('layouts.admin')

@section('title')
Stores
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Stores</h3>
        <div class="card-tools">
            @can('material')
                <a  class="btn btn-primary btn-sm" href="{{ route('material') }}"><i class="fas fa-plus-circle"></i> </a>
            @endcan

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <div class="input-group input-group-sm" style="width: 170px;">
                <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                <select id="storesPageSize" class="form-select form-select-sm" style="width:auto;">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>
            <div class="input-group input-group-sm" style="width: 220px;">
                <input type="text" id="storesSearch" class="form-control" placeholder="Search stores">
            </div>
        </div>
        <table  class="table table-hover js-paginated-table" id="material" style="font-size:14px" data-page-size="20" data-page-size-control="#storesPageSize" data-pager="#storesPager" data-search="#storesSearch">
            <thead class="thead-light">
                <tr>
                    <th>Fault Ref. No.</th>
                    <th>Fault Name</th>
                    <th>Requisition No.</th>
                    <th>SAP Ref. No.</th>
                    <th>Date Requested</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stores as $stores)
                 <tr >
                    <td>{{$stores->fault_ref_number}}</td>
                    <td>{{$stores->fault_name }}</td>
                    <td>{{$stores->requisition_number }}</td>
                    <td>{{$stores->ref_Number }}</td>
                    <td>
                        {{ Carbon\Carbon::parse($stores->created_at)->format('j F Y h:i a') }}
                        </td>
                    <td>
                        @can('material')
                        <a href="{{ route('stores.show',$stores->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                        </div>
                        @endcan
                        @can('material')
                        <a href="{{ route('stores.issue',$link->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >issue</a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="storesPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>
</section>
@endsection

