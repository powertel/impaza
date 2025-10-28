@extends('layouts.admin')

@section('title')
Assign Faults
@endsection

@include('partials.css')

@section('content')

<section class="content">
    <div class="card">

        <!--Card Header-->
        <div class="card-header">
            <h3 class="card-title">Assigned Faults</h3>
            <div class="card-tools">
                
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="table-responsive">

                <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                    <div class="input-group input-group-sm" style="width: 170px;">
                        <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                        <select id="assignedfaultsPageSize" class="form-select form-select-sm" style="width:auto;">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <input type="text" id="assignedfaultsSearch" class="form-control" placeholder="Search to assign faults">
                    </div>
                </div>
                <table class="table  table-hover align-middle js-paginated-table" id="assigned-faults-list" style="font-size:14px" data-page-size="20" data-page-size-control="#assignedfaultsPageSize" data-pager="#assignedfaultsPager" data-search="#assignedfaultsSearch">
                    <thead class="thead-light">
                        <tr>
                            <th>Ref No.</th>
                            <th>Customer</th>
                            <th>Link</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Fault Age</th>
                            <th>Assigned To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faults as $fault)
                            <tr data-fault-id="{{ $fault->id }}">
                                <td>{{ $fault->fault_ref_number }}</td>
                                <td>{{ $fault->customer }}</td>
                                <td>{{ $fault->link }}</td>
                                <td>
                                    @php
                                        $priorityColors = [
                                            'low'    => '#28a745',
                                            'medium' => '#ffc107',
                                            'high'   => '#dc3545'
                                        ];
                                        $priority = strtolower(trim($fault->priorityLevel ?? ''));
                                    @endphp
                                    <span class="badge rounded-pill" style="background-color: {{ $priorityColors[$priority] ?? '#6c757d' }}; color: white; padding: 0.5rem 0.75rem; font-weight: 600;">
                                        {{ ucfirst($fault->priorityLevel) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                        {{ $fault->description }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-danger age-ticker fs-6" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                                </td>
                                <td class="{{ $fault->assignedTo ? 'fw-bold' : 'text-muted' }}">{{ $fault->name ?: 'Not yet assigned' }}</td>
                                <td>
                                    @can('re-assign-fault')
                                    @if ((int)($fault->status_id ?? 0) === 3)
                                    <button type="button" class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#reassignModal-{{ $fault->id }}">
                                        <i class="fas fa-save me-1"></i>Re-Assign
                                    </button>
                                    @else
                                    <button class="btn btn-outline-secondary" disabled title="Re-assignment locked after initial stage" >
                                        <i class="fas fa-lock me-1"></i>Re-Assign
                                    </button>
                                    @endif
                                    @endcan
                                    <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @if ($faults->isEmpty())
                            <tr>
                                <td colspan="12" class="text-center text-muted">No faults assigned at the moment</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div id="assignedfaultsPager" class="mt-2"></div>
            </div>   
                @foreach ($faults as $fault)
                    @include('assign.reassign_modal', ['fault' => $fault, 'technicians' => ($techniciansByFault[$fault->id] ?? $technicians)])
                    @include('faults.show', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                @endforeach


        </div>
        <!-- /.card-body -->
    </div>
 </section>
@endsection

@section('scripts')
    @include('partials.scripts')
@endsection
 



