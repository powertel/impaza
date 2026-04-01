@extends('layouts.admin')

@section('title')
Department Faults
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Reffered Faults</h3>
        <div class="card-tools">
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span></div>
                    <select id="departmentFaultsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('department_faults.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 360px;">
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
                       <!--  <th>Account Manager</th> -->
                        <th>Link Name</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $faults as $fault )
                    <tr>
                        <td>{{ $faults->firstItem() + $loop->index }}</td>
                        <td>{{$fault->fault_ref_number}}</td>
                        <td>{{ $fault->customer }}</td>
                        <!-- <td>{{ $fault->accountManager }}</td> -->
                        <td>{{ $fault->link }}</td>
                        <td class="{{ $fault->name ? 'fw-bold' : 'text-muted' }}">{{ $fault->name ?: 'Not yet assigned' }}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$fault->description}}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-light text-danger age-ticker fs-6" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                        </td>
                        <td>
                            <button class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            @if(!empty($fault->referral_id))
                              <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#completeReferralModal-{{ $fault->id }}">
                                <i class="fas fa-check me-1"></i>Complete Referral
                              </button>
                              <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reassignReferralModal-{{ $fault->id }}">
                                <i class="fas fa-user-plus me-1"></i>Reassign
                              </button>
                            @endif
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
                @include('faults.show', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                @if(!empty($fault->referral_id))
                    @include('department_faults.complete_referral_modal', [ 'fault' => $fault, 'remarks' => ($remarksByFault[$fault->id] ?? collect()) ])
                    @include('department_faults.reassign_referral_modal', [ 'fault' => $fault, 'technicians' => $technicians ])
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
    </script>
@endsection

