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

        <div class="table-responsive">

            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="locationsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="locationsSearch" class="form-control" placeholder="Search Locations">
                </div>
            </div>

            

            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#locationsPageSize" data-pager="#locationsPager" data-search="#locationsSearch">
                <thead class="thead-light">
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
                            <a href="{{ route('locations.edit',$location->id) }}" class="btn btn-sm btn-outline-primary" style="padding:0px 2px;" >
                               <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            @endcan
                            <a href="{{ route('locations.show',$location->id) }}" class="btn btn-sm btn-outline-success" style="padding:0px 2px;" >
                               <i class="fas fa-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="locationsPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
@section('scripts')
    @include('partials.scripts')
@endsection
