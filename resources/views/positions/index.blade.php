
@extends('layouts.admin')

@section('title')
Positions
@endsection
@include('partials.css')
@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">Positions</h3>
        <div class="card-tools">
            @can('department-create')
            <button  class="btn btn-primary btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#positionCreateModal"><i class="fas fa-plus-circle"></i>Create New Position </button>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="positionsPageSize" class="form-control">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="positionsSearch" class="form-control" placeholder="Search Positions">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#positionsPageSize" data-pager="#positionsPager" data-search="#positionsSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Positions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($positions as $position)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $position->position}}</td>
                        <td>
                            @can('department-edit')
                                <a href="#" class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#positionEditModal" data-id="{{ $position->id }}" data-position="{{ $position->position }}">
                                    <i class="fas fa-edit me-1"></i>Edit</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="positionsPager" class="mt-2"></div>
        </div>

        @include('positions.create_modal')
        @include('positions.edit_modal')
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection

@section('scripts')
    @include('partials.department')

@endsection

