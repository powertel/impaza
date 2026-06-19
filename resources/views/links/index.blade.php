@extends('layouts.admin')

@section('title')
links
@endsection

@include('partials.css')
@section('content')
<section class="content">
<div class="row row-cols-5 g-3 mb-3">
  <div class="col">
    <a href="#" class="text-decoration-none linksStatusStat" data-status-id="">
      <div class="card shadow-sm border-0">
        <div class="rounded-top" style="height:6px; background:#6c757d"></div>
        <div class="card-body d-flex justify-content-between align-items-center py-3">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary"><i class="fas fa-list"></i></span>
            <div>
              <div class="text-muted small">All</div>
              <div class="fw-semibold">Links</div>
            </div>
          </div>
          <div class="fs-5 fw-bold text-dark">{{ $totalLinks ?? 0 }}</div>
        </div>
      </div>
    </a>
  </div>
  @foreach($linkStatuses as $st)
    @php
      $icon = $st->link_status === 'Pending' ? 'fa-hourglass-half' : ($st->link_status === 'Connected' ? 'fa-plug' : ($st->link_status === 'Disconnected' ? 'fa-unlink' : 'fa-ban'));
      $bar = $st->link_status === 'Pending' ? '#ff8080' : ($st->link_status === 'Connected' ? '#90EE90' : ($st->link_status === 'Disconnected' ? '#FFFF00' : '#A9A9A9'));
      $badge = $st->link_status === 'Pending' ? 'bg-danger' : ($st->link_status === 'Connected' ? 'bg-success' : ($st->link_status === 'Disconnected' ? 'bg-warning' : 'bg-secondary'));
    @endphp
    <div class="col">
      <a href="#" class="text-decoration-none linksStatusStat" data-status-id="{{ $st->id }}">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background: {{ $bar }}"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge {{ $badge }}"><i class="fas {{ $icon }}"></i></span>
              <div>
                <div class="text-muted small">{{ $st->link_status }}</div>
                <div class="fw-semibold">Status</div>
              </div>
            </div>
            <div class="fs-5 fw-bold text-dark">{{ (int)($statusCounts[$st->id] ?? 0) }}</div>
          </div>
        </div>
      </a>
    </div>
  @endforeach
</div>
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">Links</h3>
          <div class="card-tools">
           
            <a href="{{ $needsConfiguration ? route('links.index', request()->except(['needs_configuration', 'page'])) : route('links.index', array_merge(request()->except('page'), ['needs_configuration' => 1])) }}"
               class="btn btn-warning btn-sm">
                <i class="fas fa-tools me-1"></i>
                {{ $needsConfiguration ? 'View All Links' : 'Links To Configure' }}
                @if(!$needsConfiguration)
                  <span class="badge bg-light text-dark ms-1">{{ $linksNeedingConfigurationCount ?? 0 }}</span>
                @endif
            </a>
          
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
        <style>
          .status-cards{gap: 12px}
          .status-card{display:flex; align-items:center; justify-content:space-between; min-width:220px; padding:12px 16px; border:1px solid #e5e7eb; border-radius:12px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.04); text-decoration:none}
          .status-card .left{display:flex; align-items:center; gap:10px}
          .status-card .icon{width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff}
          .status-card .title{font-size:12px; color:#6b7280}
          .status-card .value{font-size:18px; font-weight:700; color:#111827}
          .status-card:hover{box-shadow:0 4px 10px rgba(0,0,0,0.08)}
        </style>
        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        @if($needsConfiguration)
          <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <strong>Links Pending Configuration</strong>
              <span class="ms-2 text-muted">New customers: {{ $newCustomerLinksToConfigure }} | Existing customers: {{ $existingCustomerLinksToConfigure }}</span>
            </div>
            <a href="{{ route('links.index', request()->except(['needs_configuration', 'page'])) }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-list me-1"></i> Back To All Links
            </a>
          </div>
        @endif
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
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
                    @php $statusSel = request('status'); @endphp
                    <select id="linksStatusFilter" class="form-select form-select-sm" style="width:auto;">
                        <option value="" {{ empty($statusSel) ? 'selected' : '' }}>All</option>
                        @foreach($linkStatuses as $st)
                          <option value="{{ $st->id }}" {{ (string)$statusSel === (string)$st->id ? 'selected' : '' }}>{{ $st->link_status }}</option>
                        @endforeach
                    </select>
                </div>
                <form id="linksSearchForm" method="GET" action="{{ route('links.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 360px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search all records">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <input type="hidden" name="status" value="{{ $statusSel }}">
                        <input type="hidden" name="needs_configuration" value="{{ $needsConfiguration ? 1 : '' }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('links.index', array_filter(['per_page' => $perPage, 'needs_configuration' => $needsConfiguration ? 1 : null], fn ($value) => $value !== null && $value !== '')) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
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
                    @php
                      $needsSetup = empty($link->city_id) || empty($link->suburb_id) || empty($link->pop_id);
                    @endphp
                    <tr class="{{ $needsSetup ? 'table-warning' : '' }}">
                        <td>{{ $links->firstItem() + $loop->index }}</td>
                        <td>
                          <div>{{ $link->customer}}</div>
                          @if($needsSetup)
                            <small class="badge bg-warning text-dark mt-1">{{ $link->configuration_owner_type }}</small>
                          @endif
                        </td>
                        <td>{{ $link->city ?? 'Needs configuration' }}</td>
                        <td>{{ $link->suburb ?? 'Needs configuration' }}</td>
                        <td>{{ $link->pop ?? 'Needs configuration' }}</td>
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
    document.getElementById('linksStatusFilter')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      const val = this.value;
      if (!val) params.delete('status'); else params.set('status', val);
      params.delete('page');
      window.location.search = params.toString();
    });
    document.querySelectorAll('.linksStatusStat').forEach(function(el){
      el.addEventListener('click', function(e){
        e.preventDefault();
        const id = this.getAttribute('data-status-id');
        const params = new URLSearchParams(window.location.search);
        if (!id) params.delete('status'); else params.set('status', id);
        params.delete('page');
        window.location.search = params.toString();
      });
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








                          

