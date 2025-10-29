@extends('layouts.admin')

@section('title')
links
@endsection

@include('partials.css')
@section('content')
<section class="content">
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Links</h3>
        <div class="card-tools">
            @can('link-create')
                <button type="button" class="btn btn-primary btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#createLinkModal">
                    <i class="fas fa-plus-circle"></i> Create Link
                </button>
            @endcan
            @can('link-edit')
            <button type="button" class="btn btn-secondary btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editExistingLinksModal">
                <i class="fas fa-search me-1"></i> Edit Existing Links
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
                    <select id="linksPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" id="linksSearch" class="form-control" placeholder="Search Links">
                </div>
            </div>
            <table  class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>City/Town</th>
                        <th>Location</th>
                        <th>Pop</th>
                        <th>link</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($links as $link)
                    <tr >
                        <td>{{ $links->firstItem() + $loop->index }}</td>
                        <td>{{ $link->customer}}</td>
                        <td>{{ $link->city}}</td>
                        <td>{{ $link->suburb}}</td>
                        <td>{{ $link->pop}}</td>
                        <td>{{ $link->link}}</td>

                        <td>
                            <form name="theForm" action="{{ route('links.destroy',$link->id) }}" method="POST" class="d-inline">
                                @can('link-list')
                                <button type="button" class="btn btn-outline-success"  
                                        data-bs-toggle="modal" data-bs-target="#linkViewModal{{ $link->id }}">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                                @endcan
                                @can('link-edit')
                                <button type="button" class="btn btn-outline-primary"  
                                        data-bs-toggle="modal" data-bs-target="#linkEditModal{{ $link->id }}">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                @endcan
                                @csrf
                                @method('DELETE')
                                @can('link-delete') 
                                <button type="button" class="btn btn-outline-danger show_confirm" data-toggle="tooltip" title='Delete' >
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>  
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">
                Showing {{ $links->firstItem() }} to {{ $links->lastItem() }} of {{ $links->total() }} results
              </small>
              {{ $links->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <!-- /.card-body -->
     @include('links.create_modal', [
        'customers' => $customers,
        'cities' => $cities,
        'suburbs' => $suburbs,
        'pops' => $pops,
        'linkTypes' => $linkTypes
        ])

        @include('links.search_modal', [
            'customers' => $customers,
            'cities' => $cities,
            'linkTypes' => $linkTypes
        ])

        @foreach($links as $lnk)
        @include('links.edit_modal', [
            'link' => $lnk,
            'customers' => $customers,
            'cities' => $cities,
            'suburbs' => $suburbs,
            'pops' => $pops,
            'linkTypes' => $linkTypes
        ])
        @include('links.view_modal', [ 'link' => $lnk ])
        @endforeach
</div>
 
</section>
@section('scripts')
  @include('partials.scripts')
  @include('links.partials.scripts')
  <script>
    document.getElementById('linksPageSize')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      params.set('per_page', this.value);
      params.delete('page');
      window.location.search = params.toString();
    });
  </script>
@endsection
@endsection








                          

