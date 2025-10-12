@extends('layouts.admin')

@section('title')
My Faults
@endsection
@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">{{_('My Faults')}}</h3>
        <div class="card-tools">


        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="myFaultsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="myFaultsSearch" class="form-control" placeholder="Search faults">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#myFaultsPageSize" data-pager="#myFaultsPager" data-search="#myFaultsSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Account Manager</th>
                        <th>Link Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faults as $fault)
                    <tr >
                    <td>{{ ++$i }}</td>
                        <td>{{ $fault->customer }}</td>
                        <td>{{ $fault->accountManager }}</td>
                        <td>{{ $fault->link }}</td>
                        <td style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? 'none' }};">
                            <strong>{{$fault->description}}</strong>
                        </td>
                        <td>

                        <form style="display:inline" action="{{ route('noc-clear.update',$fault->id) }}"  method="POST">
                            @csrf
                            @method('PUT')
                            @can('noc-clear-faults-clear')
                            <button type="submit" class="btn btn-sm btn-primary" style="padding:0px 2px; color:#fff;" >Clear</button>
                            @endcan
                        </form>
                        <form style="display:inline" action="{{ route('chief-tech-clear.update',$fault->id) }}"  method="POST">
                                @csrf
                                @method('PUT')
                                @can('chief-tech-clear-faults-clear')
                                <button type="submit" class="btn btn-sm btn-primary" style="padding:0px 2px; color:#fff;" >Clear</button>
                                @endcan
                        </form>

                        <!--<a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>-->
                            @if ($fault->description==='Fault is under rectification')

                            @can('rectify-fault')
                                <a href="{{ route('rectify.edit',$fault->id) }}" class="btn btn-sm btn-primary" style="padding:0px 2px; color:#fff;" >Rectify</a>
                            @endcan  
                            @can('request-permit')

                            <a href="{{ route('request-permit.edit',$fault->id) }}" class="btn btn-sm btn-warning" style="padding:0px 2px; color:#fff;" >Request Permit</a>
                            @endcan
                            @can('materials')
                            <a href="{{ route('stores.create',$fault->id) }}" class="btn btn-sm btn-primary" style="padding:0px 2px; color:#fff;" >Request Material</a>
                            @endcan
                            <a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>

        
                            @endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="myFaultsPager" class="mt-2"></div>
        </div>       
    </div>
    <!-- /.card-body -->
</div>

</section>
@endsection
