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
            <input id="rfosSearch" type="text" class="form-control form-control-sm d-inline-block w-auto" placeholder="Search">
            <select id="rfosPageSize" class="form-control form-control-sm d-inline-block w-auto">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table  class="table table-striped js-paginated-table" data-page-size="20" data-page-size-control="#rfosPageSize" data-pager="#rfosPager" data-search="#rfosSearch">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Reason For Outage</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($rfos as $rfo)
                <tr >
                    <td>{{++$i}}</td>
                    <td>{{ $rfo->RFO}}</td>
                    <td>
                            @can('department-edit')
                            <a href="{{ route('rfos.edit',$rfo->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >Edit</a>
                            @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="rfosPager" class="mt-2"></div>
    </div>
    <!-- /.card-body -->
</div>


</section>

@endsection
