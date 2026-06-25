@extends('layouts.admin')

@section('title')
Escalations
@endsection

@include('partials.css')

@section('content')

<section class="content workflow-faults-page">

<div class="card faults-panel">

    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Escalations</h3>
            <div class="faults-panel-subtitle">Manage escalated faults, review severity, and dispatch the next action from the same shared faults workspace.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>

    <div class="faults-toolbar">
        <div class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="escalationsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
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
                    <input type="text" id="escalationsSearch" class="form-control" placeholder="Search faults, customers, links, and escalation states...">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="escalationsSearchTrigger">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="escalationsReset">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" data-page-size="20" data-page-size-control="#escalationsPageSize" data-pager="#escalationsPager" data-search="#escalationsSearch">
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
                        <td data-label="No.">{{ ++$i }}</td>
                        <td data-label="Ref No.">
                            <div class="faults-ref">
                                <span class="faults-cell-main">{{ $fault->fault_ref_number }}</span>
                                <span class="faults-cell-sub">Escalated record</span>
                            </div>
                        </td>
                        <td data-label="Customer">
                            <div class="faults-cell-main">{{ $fault->customer }}</div>
                        </td>
                        <td data-label="Link Name">
                            <div class="faults-cell-main">{{ $fault->link }}</div>
                        </td>
                        <td class="text-nowrap" data-label="Status">
                            <x-status-badge :label="$fault->description" :color="\App\Models\Status::STATUS_COLOR[$fault->description] ?? '#64748B'" :soft="true" />
                        </td>
                        <td class="text-nowrap" data-label="Action(s)">
                            <div class="faults-actions">
                                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                                @can('chief-tech-return-to-technician')
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#returnRectModal-{{ $fault->id }}">
                                    <i class="fas fa-undo me-1"></i> Return
                                </button>
                                @endcan
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#referModal-{{ $fault->id }}">
                                    <i class="fas fa-share-square me-1"></i> Refer
                                </button>
                                @can('chief-tech-escalate')
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#escalateMgrModal-{{ $fault->id }}">
                                    <i class="fas fa-level-up-alt me-1"></i> Escalate
                                </button>
                                @endcan
                                @if ((int)($fault->status_id ?? 0) === \App\Services\FaultLifecycle::managerEscalatedId())
                                  @can('manager-return-to-chief-tech')
                                  <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#returnFromManagerModal-{{ $fault->id }}">
                                      <i class="fas fa-level-down-alt me-1"></i> Return From Manager
                                  </button>
                                  @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->count() === 0)
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No escalations</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing the current escalation workload</small>
            <div id="escalationsPager"></div>
        </div>
    </div>
</div>

 @foreach ($faults as $fault)
    @include('escalations.refer_modal', ['fault' => $fault])
    @include('escalations.return_rect_modal', ['fault' => $fault])
    @include('escalations.escalate_manager_modal', ['fault' => $fault])
    @include('escalations.return_manager_modal', ['fault' => $fault])
    @include('faults.show', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
@endforeach
</section>

@endsection

@section('scripts')
    @include('partials.scripts')
    <script>
        window.currentUserName = @json(optional(auth()->user())->name);
        document.getElementById('escalationsSearchTrigger')?.addEventListener('click', function () {
            const input = document.getElementById('escalationsSearch');
            if (!input) return;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.focus();
        });
        document.getElementById('escalationsReset')?.addEventListener('click', function () {
            const input = document.getElementById('escalationsSearch');
            const perPage = document.getElementById('escalationsPageSize');
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
