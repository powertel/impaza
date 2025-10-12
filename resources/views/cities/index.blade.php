@extends('layouts.admin')

@section('title')
Cities
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Cities')}}</h3>
        <div class="card-tools">
            @can('city-create')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cityCreateModal"><i class="fas fa-plus-circle"></i>{{_('Create City/Town(s)')}} </button>
            @endcan
            @can('location-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('locations.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create location')}} </a>
            @endcan
            
           
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <label for="citiesPageSize" class="mb-0 small text-muted">Show</label>
            <select id="citiesPageSize" class="form-select form-select-sm" style="width:auto;">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>
            <input id="citiesSearch" type="search" class="form-control form-control-sm" placeholder="Search..." style="max-width:240px;">
        </div>
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#citiesPageSize" data-pager="#citiesPager" data-search="#citiesSearch">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>City/Town</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cities as $city)
                 <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $city->city}}</td>
                    <td>
                        <form action="{{ route('cities.destroy',$city->id) }}" method="POST">
                            <a href="{{ route('cities.show',$city->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>
                            @can('city-edit')
-                            <a href="{{ route('cities.edit',$city->id) }}" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" >Edit</a>
+                            <button type="button" class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" data-bs-toggle="modal" data-bs-target="#cityEditModal{{ $city->id }}">Edit</button>
-                            <button class="btn btn-sm btn-danger" style="padding:0px 2px; color:#fff;" data-bs-toggle="modal" data-bs-target="#cityEditModal{{ $city->id }}">Edit</button>
                             @endcan

                        </form>
                        
                        
                    </td>
                </tr>
                @endforeach
            </tbody> 
        </table>
        <div id="citiesPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection

@include('cities.create_modal')
+@include('cities.edit_modal')
