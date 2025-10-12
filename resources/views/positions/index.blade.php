
@extends('layouts.admin')

@section('title')
Positions
@endsection
@include('partials.css')
@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">{{_('Positions')}}</h3>
        <div class="card-tools">
            @can('department-create')
            <a  class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#positionCreateModal"><i class="fas fa-plus-circle"></i>{{_('Create New Position')}} </a>
            @endcan
            <input id="positionsSearch" type="text" class="form-control form-control-sm d-inline-block w-auto" placeholder="Search">
            <select id="positionsPageSize" class="form-control form-control-sm d-inline-block w-auto">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#positionsPageSize" data-pager="#positionsPager" data-search="#positionsSearch">
            <thead>
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
                            <a href="#" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" data-toggle="modal" data-target="#positionEditModal" data-id="{{ $position->id }}" data-position="{{ $position->position }}">Edit</a>
                            @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="positionsPager" class="mt-2"></div>

        @include('positions.create_modal')
        @include('positions.edit_modal')
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection

@section('scripts')
    @include('partials.department')
    {{-- @include('partials.scripts') removed to avoid duplicate event bindings; base layout already includes this --}}
@endsection
