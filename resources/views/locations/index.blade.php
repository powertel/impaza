@extends('layouts.admin')

@section('title')
Locations
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Locations')}}</h3>
        <div class="card-tools">
            @can('location-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('locations.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create location')}} </a>
            @endcan
            @can('pop-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('pops.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Pop')}} </a>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <label for="locationsPageSize" class="mb-0 small text-muted">Show</label>
            <select id="locationsPageSize" class="form-select form-select-sm" style="width:auto;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>
            <input id="locationsSearch" type="search" class="form-control form-control-sm" placeholder="Search..." style="max-width:240px;">
        </div>
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#locationsPageSize" data-pager="#locationsPager" data-search="#locationsSearch">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>City</th>
                    <th>location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($locations as $location)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $location->city}}</td>
                    <td>{{ $location->suburb}}</td>
                    <td>
                        @can('location-edit')
                        <a href="{{ route('locations.edit',$location->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
                        @endcan
                        <a href="{{ route('locations.show',$location->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                    </td>
                </tr>
                @endforeach
            </tbody> 
        </table>
        <div id="locationsPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
@section('scripts')
    @include('partials.scripts')
@endsection
