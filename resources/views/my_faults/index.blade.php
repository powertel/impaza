@extends('layouts.admin')

@section('title')
My Faults
@endsection
@include('partials.css')
@section('content')

<section class="content workflow-faults-page">

<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">My Faults</h3>
            <div class="faults-panel-subtitle">Track your active workload, review updates, and take action from one workspace.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>

    <div class="faults-toolbar">
        <div class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="myFaultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="myFaultsSearch" class="form-control" placeholder="Search faults, customers, links, managers...">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="myFaultsSearchTrigger">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="myFaultsReset">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" data-page-size="20" data-page-size-control="#myFaultsPageSize" data-pager="#myFaultsPager" data-search="#myFaultsSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Switch</th>
                        <th>Port</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faults as $fault)
                    @php
                        $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
                    @endphp
                    <tr >
                    <td data-label="No.">{{ ++$i }}</td>
                        <td data-label="Customer">{{ $fault->customer }}</td>
                        <td data-label="Link Name">{{ $fault->link }}</td>
                        <td data-label="Switch">{{ $latestRemark->switch_name ?? 'N/A' }}</td>
                        <td data-label="Port">{{ $latestRemark->port ?? 'N/A' }}</td>
                        <td class="text-nowrap" data-label="Status">
                            <x-status-badge :label="$fault->description" :color="\App\Models\Status::STATUS_COLOR[$fault->description] ?? '#64748B'" :soft="true" />
                        </td>
                        <td data-label="Fault Age">
                            <span class="faults-age-pill age-ticker" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                        </td>
                        <td data-label="Action(s)">
                        <div class="faults-actions">
                        @if ($fault->description==='Fault is under Rectification')
                            @can('noc-clear-faults-clear')
                                <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#nocClearModal-{{ $fault->id }}">
                                    <i class="fas fa-save me-1"></i>Clear
                                </button>
                                <button class="btn btn-sm btn-outline-success"  data-bs-toggle="modal" data-bs-target="#inProgressModal-{{ $fault->id }}">
                                    <i class="fas fa-save me-1"></i>In Progress
                                </button>
                            @endcan
                            <!-- @can('chief-tech-clear-faults-clear')
                                <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#chiefTechClearModal-{{ $fault->id }}">
                                    <i class="fas fa-save me-1"></i>Clear
                                </button>
                            @endcan -->

                            <!--<a href="{{ route('faults.show',$fault->id) }}" class="btn btn-sm btn-success" style="padding:0px 2px; color:#fff;" >View</a>-->
                            

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
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#escalateModal-{{ $fault->id }}">
                                <i class="fas fa-level-up-alt me-1"></i>Escalate
                            </button>
                            
                        @endif
                            <button class="btn  btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">No faults assigned</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing your current assigned fault list</small>
            <div id="myFaultsPager"></div>
        </div>
    </div>
</div>

@foreach ($faults as $fault)
    @include('my_faults.in_progress_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
    @include('rectification.noc_clear_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
    @include('clear_faults.chief_tech_clear_modal', [ 'fault' => $fault ])
    @include('rectification.edit_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()), 'confirmedRFO' => ($confirmedRFO ?? collect()) ])
    @include('permits.requested-permits.edit_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
    @include('stores.create_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
    @include('my_faults.escalate_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
    @include('faults.show', [
        'fault' => $fault,
        'remarks' => ($remarksByFault[$fault->id] ?? collect()),
        'ageText' => ($faultAges[$fault->id] ?? ''),
        'ageStart' => ($faultAgeStart[$fault->id] ?? null),
        'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
    ])
@endforeach

</section>
@endsection

@section('scripts')
    @include('partials.scripts')
    <script>
      window.currentUserName = @json(optional(auth()->user())->name);
      document.getElementById('myFaultsSearchTrigger')?.addEventListener('click', function () {
        const input = document.getElementById('myFaultsSearch');
        if (!input) return;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
      });
      document.getElementById('myFaultsReset')?.addEventListener('click', function () {
        const input = document.getElementById('myFaultsSearch');
        const perPage = document.getElementById('myFaultsPageSize');
        if (input) {
          input.value = '';
          input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (perPage) {
          perPage.value = '20';
          perPage.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    </script>
@endsection

