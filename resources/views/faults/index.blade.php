@extends('layouts.admin')

@section('title')
Faults
@endsection

@section('Faults')
Faults
@endsection

@include('partials.css')

@section('content')
<section class="content">
<div class="row row-cols-4 g-3 mb-3">
  <div class="col">
    <a href="#" class="text-decoration-none faultsAgeStat" data-age="" data-status="lt4">
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
    <a href="#" class="text-decoration-none faultsAgeStat" data-age="today" data-status="lt4">
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
    <a href="#" class="text-decoration-none faultsAgeStat" data-age="lt72" data-status="lt4">
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
    <a href="#" class="text-decoration-none faultsAgeStat" data-age="gt72" data-status="lt4">
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
        <div class="d-flex align-items-center gap-3">
            <div>
                <h3 class="card-title mb-0">
                Manage and track faults
                </h3>
            </div>
        </div>
        <div class="card-tools">
            @can('fault-create')
                <button type="button" class="btn btn-primary btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createFaultModal">
                    <i class="fas fa-plus-circle"></i> Log Fault
                </button>
            @endcan
            <div class="btn-group ms-2">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('faults.export.csv', request()->only('q','status','age')) }}">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('faults.export.pdf', request()->only('q','status','age')) }}">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="impaza-data-panel">
            <div class="filter-toolbar impaza-list-toolbar">
                <div class="impaza-list-toolbar-left">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                        @php $perPage = request('per_page', 20); @endphp
                        <select id="faultsPageSize" class="form-select form-select-sm">
                            <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                            <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                            <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div class="impaza-list-toolbar-right">
                    <form method="GET" action="{{ route('faults.index') }}" class="m-0">
                        <div class="impaza-list-filter-row">
                            @php $statusFilter = request('status', 'all'); @endphp
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
                                <select name="status" id="faultsStatusFilter" class="form-select form-select-sm">
                                    <option value="all"   {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="lt4"   {{ $statusFilter === 'lt4' ? 'selected' : '' }}>Open Faults</option>
                                    @foreach(($openStatuses ?? collect()) as $st)
                                        <option value="{{ $st->id }}" {{ $statusFilter == (string)$st->id ? 'selected' : '' }}>{{ $st->description }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @php $ageFilter = request('age', 'all'); @endphp
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-clock me-1"></i> Age</span>
                                <select name="age" id="faultsAgeFilter" class="form-select form-select-sm">
                                    <option value="all"    {{ $ageFilter === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="today"  {{ $ageFilter === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="lt72"   {{ $ageFilter === 'lt72' ? 'selected' : '' }}>Within 72 hours</option>
                                    <option value="gt72"   {{ $ageFilter === 'gt72' ? 'selected' : '' }}>Over 72 hours</option>
                                </select>
                            </div>

                            <div class="input-group input-group-sm impaza-search">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search all records">
                                <input type="hidden" name="per_page" value="{{ $perPage }}">
                            </div>

                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-search me-1"></i>Search</button>
                            <a href="{{ route('faults.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive impaza-table-wrap">
            <table class="table table-hover align-middle impaza-table" id="faults-list">
                <thead>
                    <tr>
                    <!-- <th>No.</th>-->
                    <!-- <th>fault No.</th>-->
                        <th>Ref. No.</th>
                        <th>Customer</th>
                        <th>Account Manager</th>
                        <th>Link</th>
                        <th>Assigned To</th>
                        <th>Date Reported</th>
                        <th>Logged By</th>
                        <th>Status</th>
                        <th>Age</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faults as $fault)
                    <tr >
                        <!--<td>{{ ++$i }}</td>-->
                        <!--<td>{{$fault->id}}</td>-->
                        <td>{{$fault->fault_ref_number}}</td>
                        <td>{{$fault->customer }}</td>
                        <td >{{$fault->accountManager }}</td>
                        <td>{{$fault->link }}</td>
                        <td class="{{ $fault->assignedTo ? 'fw-bold' : 'text-muted' }}">{{ $fault->assignedTo ?: 'Not yet assigned' }}</td>
                        <td>
                        {{ Carbon\Carbon::parse($fault->created_at)->format('j F Y h:i a') }}
                        </td>
                        <td class="text-muted"> {{$fault->reportedBy}}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$fault->description}}
                            </span>
                        </td>
                        <td>
                          @php $ageText = $faultAges[$fault->id] ?? ''; @endphp
                          @php $ageStart = $faultAgeStart[$fault->id] ?? null; @endphp
                          @php $ageEnd = $faultAgeEnd[$fault->id] ?? null; @endphp
                          <span class="badge bg-light text-dark border fault-age" data-age-start="{{ $ageStart }}" data-age-end="{{ $ageEnd }}">{{ $ageText }}</span>
                        </td>
                        <td class="text-nowrap">
                            <div class="btn-group btn-group gap-2" role="group" aria-label="Actions">
                                @can('fault-edit')
                                  @if ($fault->status_id != 6)
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editFaultModal-{{ $fault->id }}">
                                      <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                  @else
                                    <button class="btn btn-outline-secondary" disabled title="Editing locked after initial stage">
                                      <i class="fas fa-lock me-1"></i> Edit
                                    </button>
                                  @endif
                                @endcan

                                <button  class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted">No faults to display</td>
                        </tr>
                    @endif
                </tbody> 
            </table>
            </div>
            @include('faults.create')

            @foreach ($faults as $fault)
                @if ($fault->status_id != 6)
                    @include('faults.edit', [
                        'fault' => $fault,
                        'customers' => $customer,
                        'cities' => $city,
                        'suburbs' => $location,
                        'pops' => $pop,
                        'links' => $link,
                        'accountManagers' => $accountManager,
                        'suspectedRFO' => $suspectedRFO,
                        'remarks' => ($remarksByFault[$fault->id] ?? collect())
                    ])
                @endif
                @include('faults.show', [
                    'fault' => $fault,
                    'remarks' => ($remarksByFault[$fault->id] ?? collect()),
                    'ageText' => ($faultAges[$fault->id] ?? ''),
                    'ageStart' => ($faultAgeStart[$fault->id] ?? null),
                    'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
                ])
            @endforeach
            <div class="impaza-table-footer d-flex justify-content-between align-items-center">
              <small class="text-muted">
                Showing {{ $faults->firstItem() }} to {{ $faults->lastItem() }} of {{ $faults->total() }} results
              </small>
              {{ $faults->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
</section>
@endsection

@section('scripts')
    @include('partials.faults')
    <script>
      document.getElementById('faultsPageSize')?.addEventListener('change', function(){
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', this.value);
        params.delete('page');
        window.location.search = params.toString();
      });
      // Auto-submit on filter change
      document.getElementById('faultsStatusFilter')?.addEventListener('change', function(){
        this.form?.submit();
      });
      document.getElementById('faultsAgeFilter')?.addEventListener('change', function(){
        // Update select coloration prior to submit
        try {
          const el = this;
          el.classList.remove('age-select-all','age-select-today','age-select-lt72','age-select-gt72');
          const map = { all: 'age-select-all', today: 'age-select-today', lt72: 'age-select-lt72', gt72: 'age-select-gt72' };
          if (map[el.value]) el.classList.add(map[el.value]);
        } catch (e) { /* noop */ }
        this.form?.submit();
      });
      // Initial age select coloration
      (function(){
        const el = document.getElementById('faultsAgeFilter');
        if (!el) return;
        el.classList.remove('age-select-all','age-select-today','age-select-lt72','age-select-gt72');
        const map = { all: 'age-select-all', today: 'age-select-today', lt72: 'age-select-lt72', gt72: 'age-select-gt72' };
        if (map[el.value]) el.classList.add(map[el.value]);
      })();

      // Clickable age stats cards -> set status=lt4 and age, preserve q/per_page
      document.querySelectorAll('.faultsAgeStat').forEach(function(el){
        el.addEventListener('click', function(e){
          e.preventDefault();
          const params = new URLSearchParams(window.location.search);
          params.set('status', this.getAttribute('data-status'));
          const age = this.getAttribute('data-age');
          if (!age) params.delete('age'); else params.set('age', age);
          params.delete('page');
          window.location.search = params.toString();
        });
      });

      function fmtAge(ms){
        const totalMinutes = Math.floor(ms / 60000);
        const days = Math.floor(totalMinutes / (60*24));
        const hours = Math.floor((totalMinutes - days*60*24) / 60);
        const minutes = totalMinutes - days*60*24 - hours*60;
        let s = '';
        if (days > 0) s += days + 'd ';
        s += hours + 'h ' + minutes + 'm';
        return s;
      }
      function updateAges(){
        const now = Date.now();
        document.querySelectorAll('.fault-age').forEach(function(el){
          const start = el.getAttribute('data-age-start');
          const end = el.getAttribute('data-age-end');
          if (!start) return;
          const startMs = Date.parse(start);
          const endMs = end ? Date.parse(end) : null;
          const diff = (endMs ? endMs : now) - startMs;
          if (diff < 0) return;
          el.textContent = fmtAge(diff);
        });
      }
      updateAges();
      setInterval(updateAges, 60000);
    </script>
@endsection


