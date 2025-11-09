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

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h3 class="card-title mb-0">
                Manage and track network faults
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
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    @php $perPage = request('per_page', 20); @endphp
                    <select id="faultsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('faults.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 760px; max-width: 100%;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
                        </div>
                        @php $statusFilter = request('status', 'all'); @endphp
                        <select name="status" id="faultsStatusFilter" class="form-select form-select-sm" style="width:auto;">
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
                        <select name="age" id="faultsAgeFilter" class="form-select form-select-sm me-1" style="width:50px;">
                            <option value="all"    {{ $ageFilter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="today"  {{ $ageFilter === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="lt72"   {{ $ageFilter === 'lt72' ? 'selected' : '' }}>Within 72 hours</option>
                            <option value="gt72"   {{ $ageFilter === 'gt72' ? 'selected' : '' }}>Over 72 hours</option>
                        </select>

                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search all records">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('faults.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
            <table class="table  table-hover align-middle" id="faults-list" style="font-size:14px">
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
                        <th>Actions</th>
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
                        <td class="text-nowrap">
                            <div class="btn-group btn-group gap-2" role="group" aria-label="Actions">
                                @can('fault-edit')
                                  @if ($fault->status_id == 1)
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
            @include('faults.create')

            @foreach ($faults as $fault)
                @if ($fault->status_id == 1)
                    @include('faults.edit', [
                        'fault' => $fault,
                        'customers' => $customer,
                        'cities' => $city,
                        'suburbs' => $location,
                        'pops' => $pop,
                        'links' => $link,
                        'accountManagers' => $accountManager,
                        'suspectedRFO' => $suspectedRFO
                    ])
                @endif
                @include('faults.show', [
                    'fault' => $fault,
                    'remarks' => ($remarksByFault[$fault->id] ?? collect())
                ])
            @endforeach
            <div class="d-flex justify-content-between align-items-center mt-3">
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
    </script>
@endsection


