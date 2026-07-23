@extends('layouts.admin')

@section('title')
Department Faults
@endsection
@include('partials.css')
@section('content')
<section class="content workflow-faults-page">

<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Referred Faults</h3>
            <div class="faults-panel-subtitle">Review referred cases, complete section work, and reassign to technicians from the same shared faults workspace.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>
    <div class="faults-toolbar">
        <form method="GET" action="{{ route('referred_faults.index') }}" class="m-0">
            <div class="faults-toolbar-grid">
                <div class="faults-toolbar-field">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <select id="referredFaultsPageSize" name="per_page" class="form-select form-select-sm" aria-label="Rows per page">
                            <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                            <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                            <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search referred faults, customers, links, and users...">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                <a href="{{ route('referred_faults.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
                    <i class="fas fa-rotate-left me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Switch</th>
                        <th>Port</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $faults as $fault )
                    @php
                        $latestRemark = ($remarksByFault[$fault->id] ?? collect())->first();
                    @endphp
                    <tr>
                        <td data-label="No.">{{ $faults->firstItem() + $loop->index }}</td>
                        <td data-label="Ref No.">
                            <div class="faults-ref">
                                <span class="faults-cell-main">{{ $fault->fault_ref_number }}</span>
                                <span class="faults-cell-sub">Referred record</span>
                            </div>
                        </td>
                        <td data-label="Customer">
                            <div class="faults-cell-main">{{ $fault->customer }}</div>
                        </td>
                        <td data-label="Link Name">
                            <div class="faults-cell-main">{{ $fault->link }}</div>
                        </td>
                        <td data-label="Switch">
                            <div class="faults-cell-main">{{ $latestRemark->switch_name ?? 'N/A' }}</div>
                        </td>
                        <td data-label="Port">
                            <div class="faults-cell-main">{{ $latestRemark->port ?? 'N/A' }}</div>
                        </td>
                        <td data-label="Assigned To">
                            <div class="faults-cell-main {{ $fault->assignedTo ? '' : 'text-muted fw-normal' }}">{{ $fault->assignedTo ?: 'Not yet assigned' }}</div>
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
                                @if(!empty($fault->referral_id))
                                  <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#completeReferralModal-{{ $fault->id }}">
                                    <i class="fas fa-check me-1"></i> Complete
                                  </button>
                                  <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reassignReferralModal-{{ $fault->id }}">
                                    <i class="fas fa-user-plus me-1"></i> Reassign
                                  </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->count() === 0)
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">No referred faults</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <div class="text-muted">
                Showing {{ $faults->firstItem() ?? 0 }} to {{ $faults->lastItem() ?? 0 }} of {{ $faults->total() }} results
                @if (request('q'))
                    for "{{ request('q') }}"
                @endif
            </div>
            <div>{{ $faults->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

@foreach ($faults as $fault)
    @include('faults.show', [
        'fault' => $fault,
        'remarks' => ($remarksByFault[$fault->id] ?? collect()),
        'ageText' => ($faultAges[$fault->id] ?? ''),
        'ageStart' => ($faultAgeStart[$fault->id] ?? null),
        'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
    ])
    @if(!empty($fault->referral_id))
        @include('department_faults.complete_referral_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
        @include('department_faults.reassign_referral_modal', [ 'fault' => $fault, 'technicians' => $technicians, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
    @endif
@endforeach
 
{{-- {{$section->section}}

@foreach ($section -> faults as $fault )

<span>{{$fault->contactName}}</span>
    
@endforeach --}}
</section>
@endsection

@section('scripts')
    @include('partials.scripts')
    <script>
      window.currentUserName = @json(optional(auth()->user())->name);
      (function(){
        var perSelect = document.getElementById('referredFaultsPageSize');
        if (perSelect) {
          perSelect.addEventListener('change', function(){
            var params = new URLSearchParams(window.location.search);
            params.set('per_page', String(perSelect.value));
            params.delete('page');
            window.location.search = params.toString();
          });
        }
      })();
    </script>
@endsection
