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
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    @php $perPage = request('per_page', 20); @endphp
                    <select id="linksPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <form id="linksSearchForm" method="GET" action="{{ route('links.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 360px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search all records">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('links.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
            <table id="linksTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>City/Town</th>
                        <th>Location</th>
                        <th>Pop</th>
                        <th>Link</th>
                        <th>Status</th>
                        <th>Action(s)</th>
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
                          @php $colors = \App\Models\LinkStatus::STATUS_COLOR; $color = $colors[$link->link_status ?? ''] ?? '#e9ecef'; @endphp
                          <span class="badge rounded-pill" style="background-color: {{ $color }}; color: #000;">{{ $link->link_status ?? '—' }}</span>
                        </td>

                        <td>
                            <div class="btn-group">
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i> Actions
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                @can('link-list')
                                  <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#linkViewModal{{ $link->id }}" title="View">
                                      <i class="fas fa-eye text-success"></i>
                                      <span>View</span>
                                    </a>
                                  </li>
                                @endcan
                                @can('link-edit')
                                  <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#linkEditModal{{ $link->id }}" title="Edit">
                                      <i class="fas fa-edit text-primary"></i>
                                      <span>Edit</span>
                                    </a>
                                  </li>
                                @endcan
                                @can('link-delete')
                                  <li>
                                    <form action="{{ route('links.destroy',$link->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('DELETE')
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 show_confirm" title="Delete">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="text-danger">Delete</span>
                                      </button>
                                    </form>
                                  </li>
                                @endcan
                                <li><hr class="dropdown-divider"></li>
                                @if(($link->link_status ?? '') === 'Connected')
                                  <li>
                                    <form action="{{ route('disconnect',$link->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_disconnect" title="Disconnect">
                                        <i class="fas fa-unlink text-warning"></i>
                                        <span class="text-warning">Disconnect</span>
                                      </button>
                                    </form>
                                  </li>
                                @endif
                                @if(($link->link_status ?? '') === 'Disconnected' || ($link->link_status ?? '') === 'Decommissioned')
                                  <li>
                                    <form action="{{ route('reconnect',$link->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect</span>
                                      </button>
                                    </form>
                                  </li>
                                @endif
                                  <li>
                                    <form action="{{ route('decommission',$link->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_decommission" title="Decommission">
                                        <i class="fas fa-ban text-danger"></i>
                                        <span class="text-danger">Decommission</span>
                                      </button>
                                    </form>
                                  </li>
                              </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>  
                    @if ($links->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted">No Links to display</td>
                        </tr>
                    @endif
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
    (function(){
      var success = @json(session('success'));
      var error = @json(session('error'));
      var warning = @json(session('warning'));
      var info = @json(session('info'));
      function show(type, text){
        if (!text) return;
        if (window.toast) {
          window.toast.fire({ icon: type, title: String(text) });
        } else {
          alert(String(text));
        }
      }
      show('success', success);
      show('error', error);
      show('warning', warning);
      show('info', info);
    })();

    // Hide inline alert banners on Links page (use JS toast only)
    document.addEventListener('DOMContentLoaded', function(){
      try {
        document.querySelectorAll('.content .alert').forEach(function(el){ el.remove(); });
      } catch (e) {}
    });
  </script>
@endsection
@endsection








                          

