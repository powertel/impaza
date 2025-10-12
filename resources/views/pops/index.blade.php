@extends('layouts.admin')

@section('title')
Pops
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('Pops')}}</h3>
        <div class="card-tools">
            @can('pop-create')
              <a  class="btn btn-primary btn-sm" href="{{ route('pops.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Pop')}} </a>  
            @endcan
            @can('customer-create')
                <a  class="btn btn-primary btn-sm" href="{{ route('customers.create') }}"><i class="fas fa-plus-circle"></i>{{_('Create Customer')}} </a>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="popsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="popsSearch" class="form-control" placeholder="Search Pops">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#popsPageSize" data-pager="#popsPager" data-search="#popsSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>City/Town</th>
                        <th>Location</th>
                        <th>Pop</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pops as $pop)
                    <tr >
                        <td>{{++$i}}</td>
                        <td>{{ $pop->city}}</td>
                        <td>{{ $pop->suburb}}</td>
                        <td>{{ $pop->pop}}</td>
                        <td>
                            @can('pop-edit')
                            <a href="{{ route('pops.edit',$pop->id) }}" class="btn btn-sm btn-outline-danger" style="padding:0px 2px;" >
                                <i class="fas fa-edit"></i>Edit
                            </a> 
                            @endcan
                            
                            <a href="{{ route('pops.show',$pop->id) }}" class="btn btn-sm btn-outline-success" style="padding:0px 2px;" >
                                <i class="fas fa-eye"></i>View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div id="popsPager" class="mt-2"></div>
        </div>
     </div>
     <!-- /.card-body -->
 </div>
 
  
 </section>
 @endsection

