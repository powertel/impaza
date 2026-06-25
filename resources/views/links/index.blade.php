@extends('layouts.admin')

@section('title')
links
@endsection

@include('partials.css')
@section('styles')
<style>
  .links-page .links-status-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .links-page .links-status-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }

  .links-page .links-status-card {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 104px;
    border-radius: 18px;
    border: 1px solid var(--impaza-border);
    background: var(--impaza-card);
    box-shadow: var(--impaza-shadow-sm);
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .links-page .links-status-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--status-color, var(--impaza-primary));
  }

  .links-page .linksStatusStat:hover .links-status-card,
  .links-page .linksStatusStat:focus-visible .links-status-card {
    transform: translateY(-2px);
    box-shadow: var(--impaza-shadow);
    border-color: color-mix(in srgb, var(--status-color, var(--impaza-primary)) 26%, var(--impaza-border));
  }

  .links-page .links-status-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .links-page .links-status-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .links-page .links-status-icon {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--status-color, var(--impaza-primary));
    background: color-mix(in srgb, var(--status-color, var(--impaza-primary)) 12%, transparent);
    font-size: .95rem;
    flex: 0 0 auto;
  }

  .links-page .links-status-label {
    font-size: .72rem;
    color: var(--impaza-muted);
  }

  .links-page .links-status-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .links-page .links-status-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--impaza-text);
  }

  .links-page .links-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(180px, 220px) minmax(280px, 1fr) auto auto;
  }

  .links-page .links-toolbar .toolbar-search-form,
  .links-page .links-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .links-page .links-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .links-page .link-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .links-page .link-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  .links-page .configuration-banner {
    border-radius: 16px;
    border: 1px solid color-mix(in srgb, #F59E0B 32%, var(--impaza-border));
    background: color-mix(in srgb, #F59E0B 10%, var(--impaza-card));
    color: var(--impaza-text);
  }

  @media (max-width: 1199.98px) {
    .links-page .links-status-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .links-page .links-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .links-page .links-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .links-page .links-status-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .links-page .links-toolbar {
      grid-template-columns: 1fr;
    }

    .links-page .links-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection
@section('content')
<section class="content workflow-faults-page links-page">
<div class="links-status-grid">
  <a href="#" class="links-status-link linksStatusStat" data-status-id="">
    <div class="links-status-card" style="--status-color:#64748B;">
      <div class="links-status-body">
        <div class="links-status-copy">
          <span class="links-status-icon"><i class="fas fa-list"></i></span>
          <div>
            <div class="links-status-label">All</div>
            <div class="links-status-title">Links</div>
          </div>
        </div>
        <div class="links-status-value">{{ $totalLinks ?? 0 }}</div>
      </div>
    </div>
  </a>
  @foreach($linkStatuses as $st)
    @php
      $icon = $st->link_status === 'Pending' ? 'fa-hourglass-half' : ($st->link_status === 'Connected' ? 'fa-plug' : ($st->link_status === 'Disconnected' ? 'fa-unlink' : 'fa-ban'));
      $bar = $st->link_status === 'Pending' ? '#EF4444' : ($st->link_status === 'Connected' ? '#10B981' : ($st->link_status === 'Disconnected' ? '#F59E0B' : '#64748B'));
    @endphp
    <a href="#" class="links-status-link linksStatusStat" data-status-id="{{ $st->id }}">
      <div class="links-status-card" style="--status-color: {{ $bar }}">
        <div class="links-status-body">
          <div class="links-status-copy">
            <span class="links-status-icon"><i class="fas {{ $icon }}"></i></span>
            <div>
              <div class="links-status-label">{{ $st->link_status }}</div>
              <div class="links-status-title">Status</div>
            </div>
          </div>
          <div class="links-status-value">{{ (int)($statusCounts[$st->id] ?? 0) }}</div>
        </div>
      </div>
    </a>
  @endforeach
</div>
<div class="card">

    <!--Card Header-->
    <div class="card-header">
        <div>
          <h3 class="card-title mb-0">Manage Links</h3>
          <div class="page-lead">Search, filter, configure, edit, and review links from one responsive workspace with modern filters and dark-safe tables.</div>
        </div>
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
                    <i class="fas fa-plus-circle me-1"></i> Create Link
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
    <div class="card-body">
        @if($needsConfiguration)
          <div class="configuration-banner d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 mb-3">
            <div>
              <strong>Links Pending Configuration</strong>
              <span class="ms-2 text-muted">New customers: {{ $newCustomerLinksToConfigure }} | Existing customers: {{ $existingCustomerLinksToConfigure }}</span>
            </div>
            <a href="{{ route('links.index', request()->except(['needs_configuration', 'page'])) }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-list me-1"></i> Back To All Links
            </a>
          </div>
        @endif
        <div class="faults-toolbar">
            <div class="filter-toolbar links-toolbar">
                <div class="faults-toolbar-field">
                    @php $perPage = request('per_page', 20); @endphp
                    <div class="input-group input-group-sm">
                      <span class="input-group-text"><i class="fas fa-list"></i></span>
                      <select id="linksPageSize" class="form-select">
                          <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>Show 10</option>
                          <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>Show 20</option>
                          <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>Show 50</option>
                          <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>Show 100</option>
                      </select>
                    </div>
                </div>
                <div class="faults-toolbar-field">
                    @php $statusSel = request('status'); @endphp
                    <div class="input-group input-group-sm">
                      <span class="input-group-text"><i class="fas fa-filter"></i></span>
                      <select id="linksStatusFilter" class="form-select">
                          <option value="" {{ empty($statusSel) ? 'selected' : '' }}>All Statuses</option>
                          @foreach($linkStatuses as $st)
                            <option value="{{ $st->id }}" {{ (string)$statusSel === (string)$st->id ? 'selected' : '' }}>{{ $st->link_status }}</option>
                          @endforeach
                      </select>
                    </div>
                </div>
                <form id="linksSearchForm" method="GET" action="{{ route('links.index') }}" class="toolbar-search-form m-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search links, customers, cities, or locations">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <input type="hidden" name="status" value="{{ $statusSel }}">
                        <input type="hidden" name="needs_configuration" value="{{ $needsConfiguration ? 1 : '' }}">
                    </div>
                </form>
                <button type="submit" form="linksSearchForm" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Search</button>
                <a href="{{ route('links.index', array_filter(['per_page' => $perPage, 'needs_configuration' => $needsConfiguration ? 1 : null], fn ($value) => $value !== null && $value !== '')) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
        <div class="faults-table-shell">
        <div class="table-responsive">
            <table id="linksTable" class="table table-hover align-middle">
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
                        <td data-label="No."><span class="record-chip">#{{ $links->firstItem() + $loop->index }}</span></td>
                        <td data-label="Customer">
                          <div class="record-meta">
                          <div class="record-main">{{ $link->customer}}</div>
                          @if($needsSetup)
                            <small class="record-chip mt-1"><i class="fas fa-tools"></i> {{ $link->configuration_owner_type }}</small>
                          @endif
                          </div>
                        </td>
                        <td data-label="City/Town">{{ $link->city ?? 'Needs configuration' }}</td>
                        <td data-label="Location">{{ $link->suburb ?? 'Needs configuration' }}</td>
                        <td data-label="Pop">{{ $link->pop ?? 'Needs configuration' }}</td>
                        <td data-label="Link">
                          <div class="record-meta">
                            <span class="link-name">{{ $link->link}}</span>
                            <span class="link-helper">Service link</span>
                          </div>
                        </td>
                        <td data-label="Status">
                          @php $colors = \App\Models\LinkStatus::STATUS_COLOR; $color = $colors[$link->link_status ?? ''] ?? '#64748B'; @endphp
                          <x-status-badge :label="$link->link_status ?? '—'" :color="$color" :soft="true" />
                        </td>

                        <td class="text-end" data-label="Action(s)">
                            <div class="workflow-actions">
                              @can('link-list')
                              <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#linkViewModal{{ $link->id }}" title="View">
                                <i class="fas fa-eye me-1"></i> View
                              </button>
                              @endcan
                              @can('link-edit')
                              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#linkEditModal{{ $link->id }}" title="Edit">
                                <i class="fas fa-edit me-1"></i> Edit
                              </button>
                              @endcan
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v me-1"></i> More
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
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
                            <td colspan="8" class="text-center empty-state">No Links to display</td>
                        </tr>
                    @endif
            </table>
            <div class="workflow-pagination">
              <small class="table-note">
                Showing {{ $links->firstItem() }} to {{ $links->lastItem() }} of {{ $links->total() }} results
              </small>
              {{ $links->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
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








                          

