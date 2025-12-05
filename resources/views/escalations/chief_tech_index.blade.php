@extends('layouts.admin')

@section('title')
Escalations
@endsection

@include('partials.css')

@section('content')

<section class="content">

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Escalations</h3>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover js-paginated-table" data-page-size="20">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Status</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i = 0)
                    @foreach ($faults as $fault)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $fault->fault_ref_number }}</td>
                        <td>{{ $fault->customer }}</td>
                        <td>{{ $fault->link }}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{ $fault->description }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            
                            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            @can('chief-tech-return-to-technician')
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#returnRectModal-{{ $fault->id }}">
                                <i class="fas fa-undo me-1"></i> Return to Rectification
                            </button>
                            @endcan
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#referModal-{{ $fault->id }}">
                                <i class="fas fa-share-square me-1"></i> Refer to section
                            </button>
                            @can('chief-tech-escalate')
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#escalateMgrModal-{{ $fault->id }}">
                                <i class="fas fa-level-up-alt me-1"></i> Escalate to Manager
                            </button>
                            @endcan
                            @if ((int)($fault->status_id ?? 0) === \App\Services\FaultLifecycle::managerEscalatedId())
                              @can('manager-return-to-chief-tech')
                              <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#returnFromManagerModal-{{ $fault->id }}">
                                  <i class="fas fa-level-down-alt me-1"></i> Return from Manager
                              </button>
                              @endcan
                            @endif
                            
                        </td>
                    </tr>
                    @include('escalations.refer_modal', ['fault' => $fault])
                    @include('escalations.return_rect_modal', ['fault' => $fault])
                    @include('escalations.escalate_manager_modal', ['fault' => $fault])
                    @include('escalations.return_manager_modal', ['fault' => $fault])
                    @include('faults.show', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                    @endforeach
                    @if ($faults->count() === 0)
                        <tr>
                            <td colspan="6" class="text-center text-muted">No escalations</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>

@endsection
