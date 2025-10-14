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
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="citiesPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="citiesSearch" class="form-control" placeholder="Search Cities">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#citiesPageSize" data-pager="#citiesPager" data-search="#citiesSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>City/Town</th>
                        <th>Region</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cities as $city)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $city->city}}</td>
                        <td>{{ $city->region ?? '-' }}</td>
                        <td>
                            <form action="{{ route('cities.destroy',$city->id) }}" method="POST">
                                @can('city-edit')
                                    <button type="button" class="btn btn-sm btn-outline-primary" style="padding:0px 2px;" data-bs-toggle="modal" data-bs-target="#cityEditModal{{ $city->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                @endcan

                            </form>
                            
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="citiesPager" class="mt-2"></div>
        </div>
        @include('cities.create_modal')
        @include('cities.edit_modal')
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection

