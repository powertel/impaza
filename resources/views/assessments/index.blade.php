@extends('layouts.admin')

@section('title')
Assess Faults
@endsection

@include('partials.css')
@section('content')

<section class="content workflow-faults-page">

<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Assess Faults</h3>
            <div class="faults-panel-subtitle">Review incoming faults, assess severity, and progress them through the workflow in the same layout as the main log.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>
    <div class="faults-toolbar">
        <div class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="assessmentsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
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
                    <input type="text" id="assessmentsSearch" class="form-control" placeholder="Search assessable faults...">
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit" id="assessmentsSearchTrigger">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset" id="assessmentsReset">
                <i class="fas fa-rotate-left me-1"></i> Reset
            </button>
        </div>
    </div>
    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table js-paginated-table" data-page-size="20" data-page-size-control="#assessmentsPageSize" data-pager="#assessmentsPager" data-search="#assessmentsSearch">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref. No.</th>
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
                        <td data-label="Ref. No.">{{$fault->fault_ref_number}}</td>
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
                        <td class="text-nowrap" data-label="Action(s)">
                            <div class="faults-actions" role="group" aria-label="Actions">
                                <!-- @can('noc-clear-faults-clear')
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nocClearModal-{{ $fault->id }}">
                                    <i class="fas fa-save me-1"></i> Clear
                                </button>
                                @endcan -->
                                @can('fault-assessment')
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#assessFaultModal-{{ $fault->id }}">
                                  <i class="fas fa-clipboard-check me-1"></i> Assess
                                </button>
                                @endcan

                                <!-- @can('fault-edit')
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFaultModal-{{ $fault->id }}">
                                  <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                @endcan -->

                                <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">No faults to assess at the moment</td>
                        </tr>
                    @endif
                </tbody> 
            </table>
            @foreach ($faults as $fault)
                @include('assessments.assess_modal', [
                    'fault' => $fault,
                    'sections' => $sections,
                    'confirmedRFO' => $confirmedRFO,
                    'remarks' => ($remarksByFault[$fault->id] ?? collect()),
                    'ageText' => ($faultAges[$fault->id] ?? ''),
                    'ageStart' => ($faultAgeStart[$fault->id] ?? null),
                    'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
                ])
                @include('clear_faults.noc_clear_modal', [ 'fault' => $fault ])
                @include('faults.show', [
                    'fault' => $fault,
                    'remarks' => ($remarksByFault[$fault->id] ?? collect()),
                    'ageText' => ($faultAges[$fault->id] ?? ''),
                    'ageStart' => ($faultAgeStart[$fault->id] ?? null),
                    'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
                ])
            @endforeach

            <div id="assessmentsPager" class="mt-2"></div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@endsection

@section('scripts')
    @include('partials.scripts')
    <script>
      document.getElementById('assessmentsSearchTrigger')?.addEventListener('click', function () {
        const input = document.getElementById('assessmentsSearch');
        if (!input) return;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
      });
      document.getElementById('assessmentsReset')?.addEventListener('click', function () {
        const input = document.getElementById('assessmentsSearch');
        const perPage = document.getElementById('assessmentsPageSize');
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

