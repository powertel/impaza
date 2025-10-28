@extends('layouts.admin')

@section('title')
Clear Faults
@endsection

@include('partials.css')

@section('content')

<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Clear Faults</h3>
        <div class="card-tools">

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 170px;">
                    <div class="input-group-prepend"><span class="input-group-text">Show</span></div>
                    <select id="chiefTechClearPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="chiefTechClearSearch" class="form-control" placeholder="Search faults">
                </div>
            </div>
            <table  class="table table-hover js-paginated-table" data-page-size="20" data-page-size-control="#chiefTechClearPageSize" data-pager="#chiefTechClearPager" data-search="#chiefTechClearSearch">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Ref No.</th>
                        <th>Customer</th>
                        <th>Link Name</th>
                        <th>Status</th>
                        <th>Fault Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $faults as $fault )
                    <tr >
                        <td>{{ ++$i }}</td>
                        <td>{{ $fault->fault_ref_number}}</td>
                        <td>{{ $fault->customer }}</td>
                        <td>{{ $fault->link }}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\Status::STATUS_COLOR[ $fault->description ] ?? '#6c757d' }}; color: black; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$fault->description}}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-light text-danger age-ticker fs-6" data-started-at="{{ $fault->stage_started_at ?? '' }}"></span>
                        </td>
                        <td>
                            @can('chief-tech-clear-faults-clear')
                                <button type="button" class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#chiefTechClearModal-{{ $fault->id }}">
                                    <i class="fas fa-save me-1"></i> Clear
                                </button>
                            @endcan
                            <button type="button" class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @if ($faults->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted">No faults to Clear</td>
                        </tr>
                    @endif
                </tbody> 
            </table>
            <div id="chiefTechClearPager" class="mt-2"></div>
        </div>
     </div>
     <!-- /.card-body -->
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
         'remarks' => $remarksByFault[$fault->id] ?? collect(),
     ])
 @endforeach
 @endsection

@section('scripts')
    @include('partials.scripts')
    <script>
        window.currentUserName = "{{ auth()->user()->name }}";
    </script>
    <!-- Per-row Chief Tech Clear confirmation modals using partial -->
    @foreach ($faults as $fault)
        @include('clear_faults.chief_tech_clear_modal', ['fault' => $fault])
    @endforeach
@endsection


