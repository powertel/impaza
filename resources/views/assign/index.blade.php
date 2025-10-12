@extends('layouts.admin')

@section('title')
Assign Faults
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Assigned Faults')}}</h3>
        <div class="card-tools">
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#assignPageSize" data-pager="#assignPager" data-search="#assignSearch">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>Account Manager</th>
                    <th>Link Name</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $faults as $fault )
                 <tr >
                    <td>{{ ++$i }}</td>
                    <td>{{ $fault->customer }}</td>
                    <td>{{ $fault->accountManager }}</td>
                    <td>{{ $fault->link }}</td>
                    <td>{{ $fault->name }}</td>
                    <td style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? 'none' }};">
                        <strong>{{$fault->description}}</strong> 
                    </td>
                    <td>
                        @can('re-assign-fault')
                        <a href="{{ route('assign.edit',$fault->id) }}" class="btn btn-sm btn-primary" style="padding:0px 2px; color:#fff;" >Re-Assign</a>
                        @endcan
                        <a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                    </td>
                </tr>
                @endforeach
            </tbody> 
        </table>
        <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
          <label for="assignPageSize" class="mb-0 small text-muted">Show</label>
          <select id="assignPageSize" class="form-select form-select-sm" style="width:auto;">
            <option value="10">10</option>
            <option value="20" selected>20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">All</option>
          </select>
          <input id="assignSearch" type="search" class="form-control form-control-sm" placeholder="Search..." style="max-width:240px;">
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection

<div id="assignPager" class="mt-2"></div>
