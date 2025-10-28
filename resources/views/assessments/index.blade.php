@extends('layouts.admin')

@section('title')
Assess Faults
@endsection

@include('partials.css')
@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Assess Faults</h3>
        <div class="card-tools">

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="assessmentsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="assessmentsSearch" class="form-control" placeholder="Search faults">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#assessmentsPageSize" data-pager="#assessmentsPager" data-search="#assessmentsSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Contact Name</th>
                        <th>Account Manager</th>
                        <th>Link Name</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faults as $fault)
                    <tr >
                        <td>{{ ++$i }}</td>
                        <td>{{ $fault->customer }}</td>
                        <td>{{ $fault->contactName }}</td>
                        <td>{{ $fault->accountManager }}</td>
                        <td>{{ $fault->link }}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: #fff; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$fault->description}}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-light text-danger age-ticker fs-6" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                        </td>
                        <td class="text-nowrap">
                            <div class="btn-group btn-group gap-2" role="group" aria-label="Actions">
                                @can('fault-assessment')
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#assessFaultModal-{{ $fault->id }}">
                                  <i class="fas fa-clipboard-check me-1"></i> Assess
                                </button>
                                @endcan

                                @can('fault-edit')
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFaultModal-{{ $fault->id }}">
                                  <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                @endcan

                                <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted">No faults to assess at the moment</td>
                        </tr>
                    @endif
                </tbody> 
            </table>
            @foreach ($faults as $fault)
                @include('assessments.assess_modal', [
                    'fault' => $fault,
                    'sections' => $sections,
                    'confirmedRFO' => $confirmedRFO
                ])
                    @include('faults.show', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
            @endforeach
            <div id="assessmentsPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection

