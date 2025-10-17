@extends('layouts.admin')

@section('title')
Requested Permits
@endsection
@include('partials.css')
@section('content')
   
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Requested Permits</h3>
        <div class="card-tools">
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="requestedPermitsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="requestedPermitsSearch" class="form-control" placeholder="Search Requested Permits">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#requestedPermitsPageSize" data-pager="#requestedPermitsPager" data-search="#requestedPermitsSearch">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Fault ID</th>
                        <th>PTW Numnber</th>
                        <th>Customer Name</th>
                        <th>Date of Request</th>
                        <th>Technician</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                <tr>
                    <td>1</td>
                    <td>653276</td>
                    <td>1213313</td>
                    <th>Zb Bank</th>
                    <th>05/05/43</th>
                    <th>Freedom</th>
                    <td>
                        <button  class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#showPermitModal">
                            <i class="fas fa-eye me-1"></i> View
                        </button>
                    </td>
                </tr>

                </tbody> 
            </table>
            <div id="requestedPermitsPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
@section('scripts')
    @include('partials.scripts')

@endsection

