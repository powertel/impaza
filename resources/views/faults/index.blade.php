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
        <h3 class="card-title">Faults</h3>
        <div class="card-tools">
            
                <!-- <a  class="btn btn-primary btn-sm" href="{{ route('faults.create') }}"><i class="fas fa-plus-circle"></i>Log Fault </a> -->
            
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
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    @php $perPage = request('per_page', 20); @endphp
                    <select id="faultsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('faults.index') }}" class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search faults (all records)">
                    </div>
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                    <a href="{{ route('faults.index', ['per_page' => $perPage]) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </form>
            </div>
            <table class="table  table-hover align-middle" id="faults-list" style="font-size:14px">
                <thead class="thead-light">
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
    </script>
@endsection


