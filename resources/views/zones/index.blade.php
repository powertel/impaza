@extends('layouts.admin')

@section('title')
Zones
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Zones</h3>
        <div class="card-tools">
            @can('technician-configuration')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#zoneCreateModal"><i class="fas fa-plus-circle"></i> Create Zone</button>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    </div>
                    <select id="zonesPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="zonesSearch" class="form-control" placeholder="Search Zones">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#zonesPageSize" data-pager="#zonesPager" data-search="#zonesSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Region</th>
                        <th>POPs Count</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($zones as $zone)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $zone->name}}</td>
                        <td>{{ $zone->region ?? '-' }}</td>
                        <td>{{ $zone->pops_count }}</td>
                        <td>
                            <form action="{{ route('zones.destroy',$zone->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                @can('technician-configuration')
                                    <button type="button" class="btn btn-sm btn-outline-primary" style="padding:0px 2px;" data-bs-toggle="modal" data-bs-target="#zoneEditModal{{ $zone->id }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:0px 2px;" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                @endcan

                            </form>
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="zonesPager" class="mt-2"></div>
        </div>
        @include('zones.create_modal')
        @include('zones.edit_modal')
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection
