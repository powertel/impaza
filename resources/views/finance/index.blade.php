@extends('layouts.admin')

@section('title')
Finance
@endsection
@include('partials.css')
@section('content')
<section class="content">
<div class="row row-cols-5 g-3 mb-3">
  <div class="col">
    <a href="#" class="text-decoration-none financeStatusStat" data-status-id="">
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
  @foreach($linkStatuses ?? [] as $st)
    @php
      $icon = $st->link_status === 'Pending' ? 'fa-hourglass-half' : ($st->link_status === 'Connected' ? 'fa-plug' : ($st->link_status === 'Disconnected' ? 'fa-unlink' : 'fa-ban'));
      $bar = $st->link_status === 'Pending' ? '#ff8080' : ($st->link_status === 'Connected' ? '#90EE90' : ($st->link_status === 'Disconnected' ? '#FFFF00' : '#A9A9A9'));
      $badge = $st->link_status === 'Pending' ? 'bg-danger' : ($st->link_status === 'Connected' ? 'bg-success' : ($st->link_status === 'Disconnected' ? 'bg-warning' : 'bg-secondary'));
    @endphp
    <div class="col">
      <a href="#" class="text-decoration-none financeStatusStat" data-status-id="{{ $st->id }}">
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
        <h3 class="card-title">Links</h3>
        <div class="card-tools">
            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    @php $perPage = request('per_page', 20); @endphp
                    <select id="financePageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
                    @php $statusSel = request('status'); @endphp
                    <select id="financeStatusFilter" class="form-select form-select-sm" style="width:auto;">
                        <option value="" {{ empty($statusSel) ? 'selected' : '' }}>All</option>
                        @foreach(($linkStatuses ?? []) as $st)
                          <option value="{{ $st->id }}" {{ (string)$statusSel === (string)$st->id ? 'selected' : '' }}>{{ $st->link_status }}</option>
                        @endforeach
                    </select>
                </div>
                <form id="financeSearchForm" method="GET" action="{{ route('finance.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 360px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search Links">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <input type="hidden" name="status" value="{{ $statusSel }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('finance.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
            <table class="table table-hover" id="financeTable">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Contract Number</th>
                        <th>City/Town</th>
                        <th>link</th>
                        <th>Status</th>
                        <th>Action(s)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($finance_links as $link)
                    <tr >
                        <td>{{ $finance_links->firstItem() + $loop->index }}</td>
                        <td>{{ $link->customer}}</td>
                        <td>{{$link->contract_number}}</td>
                        <td>{{ $link->city}}</td>
                        <td>{{ $link->link}}</td>
                        <td class="text-nowrap">
                            <span class="badge rounded-pill" style="background-color: {{ App\Models\LinkStatus::STATUS_COLOR[ $link->link_status ] ?? '#6c757d' }}; color: #0d0c0cff; padding: 0.5rem 0.75rem; font-weight: 600;">
                                {{$link->link_status}}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i> Actions
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                  <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeViewModal-{{ $link->id }}" title="View">
                                      <i class="fas fa-eye text-success"></i>
                                      <span>View</span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeEditModal-{{ $link->id }}" title="Edit">
                                      <i class="fas fa-edit text-primary"></i>
                                      <span>Edit</span>
                                    </a>
                                  </li>
                                  @if ($link->link_status==='Pending')
                                    <li>
                                      <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeEditModal-{{ $link->id }}" title="Approve">
                                        <i class="fas fa-check text-primary"></i>
                                        <span>Approve</span>
                                      </a>
                                    </li>
                                  @endif
                                  <li><hr class="dropdown-divider"></li>
                                  @if ($link->link_status==='Connected')
                                    <li>
                                      <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeDisconnectModal-{{ $link->id }}" title="Disconnect">
                                        <i class="fas fa-unlink text-warning"></i>
                                        <span class="text-warning">Disconnect</span>
                                      </a>
                                    </li>
                                  @endif
                                  @if ($link->link_status==='Disconnected')
                                    <li>
                                      <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#financeReconnectModal-{{ $link->id }}" title="Reconnect">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect</span>
                                      </a>
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
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">
                Showing {{ $finance_links->firstItem() }} to {{ $finance_links->lastItem() }} of {{ $finance_links->total() }} results
              </small>
              {{ $finance_links->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>

@foreach ($finance_links as $link)
    @include('finance.view_modal', ['link' => $link])
    @include('finance.edit_modal', ['link' => $link])
    @if ($link->link_status==='Connected')
        @include('finance.disconnect_modal', ['link' => $link])
    @endif
    @if ($link->link_status==='Disconnected')
        @include('finance.reconnect_modal', ['link' => $link])
    @endif
@endforeach

</section>
@section('scripts')
  @include('partials.scripts')
  <script>
    document.getElementById('financePageSize')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      params.set('per_page', this.value);
      params.delete('page');
      window.location.search = params.toString();
    });
    document.getElementById('financeStatusFilter')?.addEventListener('change', function(){
      const params = new URLSearchParams(window.location.search);
      const val = this.value;
      if (!val) params.delete('status'); else params.set('status', val);
      params.delete('page');
      window.location.search = params.toString();
    });
    document.querySelectorAll('.financeStatusStat').forEach(function(el){
      el.addEventListener('click', function(e){
        e.preventDefault();
        const id = this.getAttribute('data-status-id');
        const params = new URLSearchParams(window.location.search);
        if (!id) params.delete('status'); else params.set('status', id);
        params.delete('page');
        window.location.search = params.toString();
      });
    });
  </script>
@endsection
@endsection

