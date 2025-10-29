@extends('layouts.admin')

@section('title')
Pops
@endsection
@include('partials.css')
@section('content')
<section class="content">

<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Pops</h3>
        <div class="card-tools">
            @can('pop-create')
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#popCreateModal"><i class="fas fa-plus-circle"></i>Create Pop </button>
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
                    <select id="popsPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('pops.index') }}" class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search pops (all records)">
                    </div>
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                    <a href="{{ route('pops.index', ['per_page' => $perPage]) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </form>
            </div>
            <table  class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>City/Town</th>
                        <th>Location</th>
                        <th>Pop</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pops as $pop)
                    <tr >
                        <td>{{ $loop->iteration + $pops->firstItem() - 1 }}</td>
                        <td>{{ $pop->city}}</td>
                        <td>{{ $pop->suburb}}</td>
                        <td>{{ $pop->pop}}</td>
                        <td>
                            @can('pop-edit')
                            <button type="button" class="btn btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#popEditModal{{ $pop->id }}">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            @endcan
                            
                            <button type="button" class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#popViewModal{{ $pop->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">
                Showing {{ $pops->firstItem() }} to {{ $pops->lastItem() }} of {{ $pops->total() }} results
              </small>
              {{ $pops->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
         </div>
     </div>
     <!-- /.card-body -->
        @include('pops.create_modal')
        @include('pops.view_modal')
        @include('pops.edit_modal')
 </div>
 
 
 </section>
 @endsection

@section('scripts')
  <script>
    document.getElementById('popsPageSize')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      params.set('per_page', this.value);
      params.delete('page');
      window.location.search = params.toString();
    });
  </script>
@endsection


