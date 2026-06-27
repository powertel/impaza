@extends('layouts.admin')

@section('title')
Rectified Faults
@endsection

@include('partials.css')

@section('content')

<section class="content workflow-faults-page">
<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Rectified Faults</h3>
            <div class="faults-panel-subtitle">Track faults awaiting final chief technician review from the same workspace pattern used across the faults module.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>

    <div class="faults-toolbar">
        <div class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="chiefTechClearPageSize" class="form-select form-select-sm" aria-label="Rows per page">
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
                    <input type="text" id="chiefTechClearSearch" class="form-control" placeholder="Search faults, customers, links, and statuses...">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="chiefTechClearSearchTrigger">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="chiefTechClearReset">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" data-page-size="20" data-page-size-control="#chiefTechClearPageSize" data-pager="#chiefTechClearPager" data-search="#chiefTechClearSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $faults as $fault )
                    <tr>
                        <td data-label="No.">{{ ++$i }}</td>
                        <td data-label="Ref No.">
                            <div class="faults-ref">
                                <span class="faults-cell-main">{{ $fault->fault_ref_number }}</span>
                                <span class="faults-cell-sub">Fault record</span>
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
                        <td data-label="Fault Age">
                            <span class="faults-age-pill age-ticker" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                        </td>
                        <td data-label="Action(s)">
                            <div class="faults-actions">
                                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">No rectified faults</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing the current rectification queue for chief technician review</small>
            <div id="chiefTechClearPager"></div>
        </div>
    </div>
</div>
 
  
 {{-- {{$section->section}}
 
 @foreach ($section -> faults as $fault )
 
 <span>{{$fault->contactName}}</span>
     
 @endforeach --}}
 </section>

 {{-- Per-row Fault show modals for "View" --}}
 @foreach ($faults as $fault)
    @include('faults.show', [
        'fault' => $fault,
        'remarks' => ($remarksByFault[$fault->id] ?? collect()),
        'ageText' => ($faultAges[$fault->id] ?? ''),
        'ageStart' => ($faultAgeStart[$fault->id] ?? null),
        'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
    ])
 @endforeach
 @endsection

@section('scripts')
    @include('partials.scripts')
    <script>
        window.currentUserName = "{{ auth()->user()->name }}";
        document.getElementById('chiefTechClearSearchTrigger')?.addEventListener('click', function () {
            const input = document.getElementById('chiefTechClearSearch');
            if (!input) return;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.focus();
        });
        document.getElementById('chiefTechClearReset')?.addEventListener('click', function () {
            const input = document.getElementById('chiefTechClearSearch');
            const perPage = document.getElementById('chiefTechClearPageSize');
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
    <!-- Per-row Chief Tech Clear confirmation modals using partial -->
    @foreach ($faults as $fault)
        @include('clear_faults.chief_tech_clear_modal', ['fault' => $fault])
    @endforeach
@endsection

