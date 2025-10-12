@extends('layouts.admin')

@section('title')
RFO
@endsection
@include('partials.css')
@section('content')

<section class="content" >
<div class="card" >
    <div class="card-header">
        <h3 class="card-title">{{_('Reasons For Outage')}}</h3>
        <div class="card-tools">
            @can('department-create')
            <a  class="btn btn-primary btn-sm" href="{{ route('rfos.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create New RFO')}} </a>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="rfosPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="rfosSearch" class="form-control" placeholder="Search rfos">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#rfosPageSize" data-pager="#rfosPager" data-search="#rfosSearch">
                <thead class="thead=light">
                    <tr>
                        <th>No.</th>
                        <th>Reason For Outage</th>
                        <th>Actions</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rfos as $rfo)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $rfo->RFO}}</td>
                        <td>
                                @can('department-edit')
                                <a href="{{ route('rfos.edit',$rfo->id) }}" class="btn btn-sm btn-outline-primary" style="padding:0px 2px;" >
                                    <i class="fas fa-edit"></i>Edit
                                </a>
                                @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="rfosPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection
