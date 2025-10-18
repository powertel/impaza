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
        <h3 class="card-title">My Faults</h3>
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
                        <!-- <td style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? 'none' }};">
                            <strong>{{$fault->description}}</strong>
                        </td> -->
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$fault->description}}
                            </span>
                        </td>
                        <td>

                        @can('noc-clear-faults-clear')
                            <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#nocClearModal-{{ $fault->id }}">
                                <i class="fas fa-save me-1"></i>Clear
                            </button>
                        @endcan
                        @can('chief-tech-clear-faults-clear')
                            <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#chiefTechClearModal-{{ $fault->id }}">
                                <i class="fas fa-save me-1"></i>Clear
                            </button>
                        @endcan

                        <!--<a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>-->
                            @if ($fault->description==='Fault is under rectification')

                            @can('rectify-fault')
                                <button class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#rectifyEditModal-{{ $fault->id }}">
                                    <i class="fas fa-save me-1"></i>Rectify
                                </button>
                            @endcan  
                            @can('request-permit')
                                <button class="btn btn-outline-warning"  data-bs-toggle="modal" data-bs-target="#requestPermitEditModal-{{ $fault->id }}">
                                    <i class="fas fa-pencil me-1"></i>Request Permit
                                </button>
                            @endcan
                            @can('materials')
                                <button class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#requestMaterialCreateModal-{{ $fault->id }}">
                                    <i class="fas fa-pencil me-1"></i>Request Material
                                </button>
                            @endcan
                            <button class="btn  btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            @endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @foreach ($faults as $fault)
                @include('clear_faults.noc_clear_modal', [ 'fault' => $fault ])
                @include('clear_faults.chief_tech_clear_modal', [ 'fault' => $fault ])
                @include('rectification.edit_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                @include('permits.requested-permits.edit_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                @include('stores.create_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                @include('faults.show', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
            @endforeach
            <div id="myFaultsPager" class="mt-2"></div>
        </div>       
    </div>
    <!-- /.card-body -->
</div>

</section>
@endsection

@section('scripts')
    @include('partials.scripts')
    <script>
      window.currentUserName = @json(optional(auth()->user())->name);
    </script>
@endsection

