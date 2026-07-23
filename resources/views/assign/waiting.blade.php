@extends('layouts.admin')

@section('title')
Assign Faults
@endsection

@include('partials.css')

@section('content')

<section class="content workflow-faults-page">
    <div class="card faults-panel">
        <div class="faults-panel-header">
            <div class="faults-panel-copy">
                <h3 class="faults-panel-title">Assign Faults</h3>
                <div class="faults-panel-subtitle">Review new faults, search the queue, and assign them from the same workspace layout as the main faults log.</div>
            </div>
            <div class="faults-panel-actions"></div>
        </div>
        <div class="faults-toolbar">
            <div class="faults-toolbar-grid">
                    <div class="faults-toolbar-field">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-list"></i></span>
                            <select id="assignedfaultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
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
                        <input type="text" id="assignedfaultsSearch" class="form-control" placeholder="Search faults to assign...">
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="waitingAssignSearchTrigger">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="waitingAssignReset">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </button>
            </div>
        </div>
        <div class="faults-table-shell">
            <div class="table-responsive impaza-table-wrap faults-table-wrap">
                <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" id="assigned-faults-list" data-page-size="20" data-page-size-control="#assignedfaultsPageSize" data-pager="#assignedfaultsPager" data-search="#assignedfaultsSearch">
                    <thead class="theah-light">
                        <tr>
                            <th>Ref. No.</th>
                            <th>Customer</th>
                            <th>Link</th>
                            <th>Switch</th>
                            <th>Port</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Fault Age</th>
                            <th>Created</th>
                            <th>Action(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faults as $fault)
                            @php
                                $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
                            @endphp
                            <tr>
                                <td data-label="Ref. No.">{{ $fault->fault_ref_number }}</td>
                                <td data-label="Customer">{{ $fault->customer }}</td>
                                <td data-label="Link">{{ $fault->link }}</td>
                                <td data-label="Switch">{{ $latestRemark->switch_name ?? 'N/A' }}</td>
                                <td data-label="Port">{{ $latestRemark->port ?? 'N/A' }}</td>
                                <td data-label="Priority">
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
                                <td data-label="Status">
                                    <x-status-badge :label="$fault->description" :color="\App\Models\Status::STATUS_COLOR[$fault->description] ?? '#64748B'" :soft="true" />
                                </td>
                                <td data-label="Fault Age">
                                    <span class="faults-age-pill age-ticker" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                                </td>
                                <td data-label="Created">{{ \Carbon\Carbon::parse($fault->created_at)->format('Y-m-d H:i') }}</td>
                                <td data-label="Action(s)">
                                    <div class="faults-actions">
                                    @can('assign-fault')
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $fault->id }}"><i class="fas fa-user-tag"></i> Assign</button>
                                    @endcan
                                    <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    </div>
                                </td>
                            </tr>
                                @include('faults.show', [
                                    'fault' => $fault,
                                    'remarks' => ($remarksByFault[$fault->id] ?? collect()),
                                    'ageText' => ($faultAges[$fault->id] ?? ''),
                                    'ageStart' => ($faultAgeStart[$fault->id] ?? null),
                                    'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
                                ])
                               
                        @endforeach
                        @if ($faults->isEmpty())
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">No faults to assign at the moment</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="faults-table-footer">
                <small class="text-muted">Showing open faults waiting for assignment</small>
                <div id="assignedfaultsPager"></div>
            </div>
        </div>
    </div>
    @foreach($faults as $fault)
        @include('assign.assign_modal', [
            'fault' => $fault,
            'technicians' => ($techniciansByFault[$fault->id] ?? $technicians),
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
      document.getElementById('waitingAssignSearchTrigger')?.addEventListener('click', function () {
        const input = document.getElementById('assignedfaultsSearch');
        if (!input) return;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
      });
      document.getElementById('waitingAssignReset')?.addEventListener('click', function () {
        const input = document.getElementById('assignedfaultsSearch');
        const perPage = document.getElementById('assignedfaultsPageSize');
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
 



