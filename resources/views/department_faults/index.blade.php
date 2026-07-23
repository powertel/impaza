@extends('layouts.admin')

@section('title')
Department Faults
@endsection
@include('partials.css')
@section('content')
<section class="content workflow-faults-page">

<div class="faults-kpi-grid mb-4">
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#94A3B8;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-clipboard-list"></i></span>
          <div>
            <div class="faults-kpi-label">All Open Faults</div>
            <div class="faults-kpi-title">View all open faults</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_total'] ?? 0) }}</div>
      </div>
    </div>
  </a>
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="today" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#2563EB;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-calendar-day"></i></span>
          <div>
            <div class="faults-kpi-label">Logged Today</div>
            <div class="faults-kpi-title">View today's faults</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_today'] ?? 0) }}</div>
      </div>
    </div>
  </a>
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="lt72" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#10B981;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-hourglass-half"></i></span>
          <div>
            <div class="faults-kpi-label">Within 72 Hours</div>
            <div class="faults-kpi-title">View within 72 hours</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_lt72'] ?? 0) }}</div>
      </div>
    </div>
  </a>
  <a href="#" class="faults-kpi-link deptFaultsAgeStat" data-age="gt72" data-status="lt4">
    <div class="faults-kpi-card" style="--faults-kpi-color:#F59E0B;">
      <div class="faults-kpi-body">
        <div class="faults-kpi-copy">
          <span class="faults-kpi-icon"><i class="fas fa-hourglass-end"></i></span>
          <div>
            <div class="faults-kpi-label">Over 72 Hours</div>
            <div class="faults-kpi-title">View overdue faults</div>
          </div>
        </div>
        <div class="faults-kpi-value">{{ (int) ($ageStats['open_gt72'] ?? 0) }}</div>
      </div>
    </div>
  </a>
</div>

<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title">Department Faults</h3>
            <div class="faults-panel-subtitle">Search, filter, and review faults assigned to your department from one responsive workspace.</div>
        </div>
        <div class="faults-panel-actions"></div>
    </div>
    <div class="faults-toolbar">
        <form method="GET" action="{{ route('department_faults.index') }}" class="m-0">
            <div class="faults-toolbar-grid">
                <div class="faults-toolbar-field">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <select id="departmentFaultsPageSize" class="form-select form-select-sm" aria-label="Rows per page">
                            <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                            <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                            <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    @php $statusFilter = request('status', 'all'); @endphp
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-filter"></i></span>
                        <select name="status" id="deptFaultsStatusFilter" class="form-select form-select-sm" aria-label="Status filter">
                            <option value="all"   {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="lt4"   {{ $statusFilter === 'lt4' ? 'selected' : '' }}>Open Faults</option>
                            @foreach(($openStatuses ?? collect()) as $st)
                                <option value="{{ $st->id }}" {{ $statusFilter == (string)$st->id ? 'selected' : '' }}>{{ $st->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    @php $ageFilter = request('age', 'all'); @endphp
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                        <select name="age" id="deptFaultsAgeFilter" class="form-select form-select-sm" aria-label="Age filter">
                            <option value="all"    {{ $ageFilter === 'all' ? 'selected' : '' }}>All Ages</option>
                            <option value="today"  {{ $ageFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="lt72"   {{ $ageFilter === 'lt72' ? 'selected' : '' }}>Within 72 Hours</option>
                            <option value="gt72"   {{ $ageFilter === 'gt72' ? 'selected' : '' }}>Over 72 Hours</option>
                        </select>
                    </div>
                </div>
                <div class="faults-toolbar-field faults-toolbar-search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search faults, customers, links, managers...">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit"><i class="fas fa-search me-1"></i> Search</button>
                <a href="{{ route('department_faults.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset"><i class="fas fa-rotate-left me-1"></i> Reset</a>
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
                        <td data-label="Ref No.">{{$fault->fault_ref_number}}</td>
                        <td data-label="Customer">{{ $fault->customer }}</td>
                        <td data-label="Link Name">{{ $fault->link }}</td>
                        <td data-label="Switch">{{ $latestRemark->switch_name ?? 'N/A' }}</td>
                        <td data-label="Port">{{ $latestRemark->port ?? 'N/A' }}</td>
                        <td class="{{ $fault->name ? 'fw-bold' : 'text-muted' }}" data-label="Assigned To">{{ $fault->name ?: 'Not yet assigned' }}</td>
                        <td class="text-nowrap" data-label="Status">
                            <x-status-badge :label="$fault->description" :color="\App\Models\Status::STATUS_COLOR[$fault->description] ?? '#64748B'" :soft="true" />
                        </td>
                        <td data-label="Action(s)">
                            <div class="faults-actions">
                                <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->count() === 0)
                        <tr>
                            <td colspan="10" class="text-center text-muted">No Department faults</td>
                        </tr>
                    @endif
                </tbody> 
            </table>
            <div class="d-flex justify-content-between align-items-center mt-2 faults-table-footer">
                <div class="text-muted">
                    Showing {{ $faults->firstItem() ?? 0 }} to {{ $faults->lastItem() ?? 0 }} of {{ $faults->total() }} results
                    @if (request('q'))
                        for "{{ request('q') }}"
                    @endif
                </div>
                <div>
                    {{ $faults->links('pagination::bootstrap-5') }}
                </div>
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
@endforeach
@foreach ($faults as $fault)
  @if(!empty($fault->referral_id))
    @include('department_faults.complete_referral_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
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
        var perSelect = document.getElementById('departmentFaultsPageSize');
        if (perSelect) {
          perSelect.addEventListener('change', function(){
            var params = new URLSearchParams(window.location.search);
            params.set('per_page', String(perSelect.value));
            params.delete('page');
            window.location.search = params.toString();
          });
        }
      })();
      document.querySelectorAll('.deptFaultsAgeStat').forEach(function(el){
        el.addEventListener('click', function(e){
          e.preventDefault();
          var params = new URLSearchParams(window.location.search);
          params.set('status', this.getAttribute('data-status'));
          var age = this.getAttribute('data-age');
          if (!age) params.delete('age'); else params.set('age', age);
          params.delete('page');
          window.location.search = params.toString();
        });
      });
      document.getElementById('deptFaultsStatusFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
      document.getElementById('deptFaultsAgeFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
    </script>
@endsection

