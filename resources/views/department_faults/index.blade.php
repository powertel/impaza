@extends('layouts.admin')

@section('title')
Department Faults
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="row row-cols-4 g-3 mb-3">
  <div class="col">
    <a href="#" class="text-decoration-none deptFaultsAgeStat" data-age="" data-status="lt4">
      <div class="card shadow-sm border-0">
        <div class="rounded-top" style="height:6px; background:#6c757d"></div>
        <div class="card-body d-flex justify-content-between align-items-center py-3">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary"><i class="fas fa-clipboard-list"></i></span>
            <div>
              <div class="text-muted small">All Open</div>
              <div class="fw-semibold">Faults</div>
            </div>
          </div>
          <div class="fs-5 fw-bold text-dark">{{ (int)($ageStats['open_total'] ?? 0) }}</div>
        </div>
      </div>
    </a>
  </div>
  <div class="col">
    <a href="#" class="text-decoration-none deptFaultsAgeStat" data-age="today" data-status="lt4">
      <div class="card shadow-sm border-0">
        <div class="rounded-top" style="height:6px; background:#0d6efd"></div>
        <div class="card-body d-flex justify-content-between align-items-center py-3">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary"><i class="fas fa-calendar-day"></i></span>
            <div>
              <div class="text-muted small">Logged</div>
              <div class="fw-semibold">Today</div>
            </div>
          </div>
          <div class="fs-5 fw-bold text-dark">{{ (int)($ageStats['open_today'] ?? 0) }}</div>
        </div>
      </div>
    </a>
  </div>
  <div class="col">
    <a href="#" class="text-decoration-none deptFaultsAgeStat" data-age="lt72" data-status="lt4">
      <div class="card shadow-sm border-0">
        <div class="rounded-top" style="height:6px; background:#20c997"></div>
        <div class="card-body d-flex justify-content-between align-items-center py-3">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success"><i class="fas fa-hourglass-half"></i></span>
            <div>
              <div class="text-muted small">Within</div>
              <div class="fw-semibold">72 Hours</div>
            </div>
          </div>
          <div class="fs-5 fw-bold text-dark">{{ (int)($ageStats['open_lt72'] ?? 0) }}</div>
        </div>
      </div>
    </a>
  </div>
  <div class="col">
    <a href="#" class="text-decoration-none deptFaultsAgeStat" data-age="gt72" data-status="lt4">
      <div class="card shadow-sm border-0">
        <div class="rounded-top" style="height:6px; background:#ffc107"></div>
        <div class="card-body d-flex justify-content-between align-items-center py-3">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-end"></i></span>
            <div>
              <div class="text-muted small">Over</div>
              <div class="fw-semibold">72 Hours</div>
            </div>
          </div>
          <div class="fs-5 fw-bold text-dark">{{ (int)($ageStats['open_gt72'] ?? 0) }}</div>
        </div>
      </div>
    </a>
  </div>
</div>

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Department Faults</h3>
        <div class="card-tools">
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span></div>
                    <select id="departmentFaultsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('department_faults.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 760px; max-width: 100%;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
                        </div>
                        @php $statusFilter = request('status', 'all'); @endphp
                        <select name="status" id="deptFaultsStatusFilter" class="form-select form-select-sm" style="width:auto;">
                            <option value="all"   {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="lt4"   {{ $statusFilter === 'lt4' ? 'selected' : '' }}>Open Faults</option>
                            @foreach(($openStatuses ?? collect()) as $st)
                                <option value="{{ $st->id }}" {{ $statusFilter == (string)$st->id ? 'selected' : '' }}>{{ $st->description }}</option>
                            @endforeach
                        </select>

                        <div class="input-group-prepend ms-2">
                            <span class="input-group-text"><i class="fas fa-clock me-1"></i> Age</span>
                        </div>
                        @php $ageFilter = request('age', 'all'); @endphp
                        <select name="age" id="deptFaultsAgeFilter" class="form-select form-select-sm me-1" style="width:50px;">
                            <option value="all"    {{ $ageFilter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="today"  {{ $ageFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="lt72"   {{ $ageFilter === 'lt72' ? 'selected' : '' }}>Within 72 hours</option>
                            <option value="gt72"   {{ $ageFilter === 'gt72' ? 'selected' : '' }}>Over 72 hours</option>
                        </select>

                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search faults (all records)">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('department_faults.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Ref No.</th>
                        <th>Customer</th>
                        <th>Account Manager</th>
                        <th>Link Name</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $faults as $fault )
                    <tr>
                        <td>{{ $faults->firstItem() + $loop->index }}</td>
                        <td>{{$fault->fault_ref_number}}</td>
                        <td>{{ $fault->customer }}</td>
                        <td>{{ $fault->accountManager }}</td>
                        <td>{{ $fault->link }}</td>
                        <td class="{{ $fault->name ? 'fw-bold' : 'text-muted' }}">{{ $fault->name ?: 'Not yet assigned' }}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$fault->description}}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        
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
            <div class="d-flex justify-content-between align-items-center mt-2">
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
    <!-- /.card-body -->
</div>
 
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

