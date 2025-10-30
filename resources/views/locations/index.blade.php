@extends('layouts.admin')

@section('title')
Locations
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Locations</h3>
        <div class="card-tools">
            @can('location-create')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#locationCreateModal"><i class="fas fa-plus-circle"></i>Create location </button>
            @endcan
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">

            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    @php $perPage = request('per_page', 20); @endphp
                    <select id="locationsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('locations.index') }}" class="input-group input-group-sm" style="width: 260px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search locations">
                    @if(request()->has('per_page'))
                      <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
                    @if(request('q'))
                      <button type="button" class="btn btn-outline-secondary" onclick="const p=new URLSearchParams(window.location.search);p.delete('q');p.delete('page');window.location.search=p.toString();"><i class="fas fa-times"></i></button>
                    @endif
                </form>
            </div>

            <table class="table table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>City</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $location)
                    <tr>
                        <td>{{ (($locations->currentPage()-1)*$locations->perPage()) + $loop->iteration }}</td>
                        <td>{{ $location->city }}</td>
                        <td>{{ $location->suburb }}</td>
                        <td class="text-nowrap">
                            @can('location-edit')
                            <button class="btn btn-outline-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#locationEditModal{{ $location->id }}">
                               <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            @endcan
                            <button class="btn btn-outline-success btn-sm"  data-bs-toggle="modal" data-bs-target="#locationViewModal{{ $location->id }}">
                               <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No locations found</td>
                    </tr>
                    @endforelse
                </tbody> 
            </table>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                @if($locations->total())
                  Showing {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ $locations->total() }} results
                @else
                  Showing 0 results
                @endif
              </small>
              {{ $locations->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
 
</section>
@include('locations.create_modal')
@include('locations.edit_modal')
@include('locations.view_modal')
@endsection
@section('scripts')
    @include('partials.scripts')
    <script>
      document.getElementById('locationsPageSize')?.addEventListener('change', function(){
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', this.value);
        params.delete('page');
        window.location.search = params.toString();
      });
    </script>
@endsection

