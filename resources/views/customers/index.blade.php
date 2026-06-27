@extends('layouts.admin')

@section('title')
Customers
@endsection

@include('partials.css')

@section('styles')
<style>
  .customers-page .customers-status-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 1rem;
  }

  .customers-page .customers-status-link {
    text-decoration: none;
    color: inherit;
    display: block;
    min-width: 0;
  }

  .customers-page .customers-status-card {
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

  .customers-page .customers-status-card::before {
    content: "";
    width: 4px;
    flex: 0 0 4px;
    background: var(--status-color, var(--impaza-primary));
  }

  .customers-page .customersStatusStat:hover .customers-status-card,
  .customers-page .customersStatusStat:focus-visible .customers-status-card {
    transform: translateY(-2px);
    box-shadow: var(--impaza-shadow);
    border-color: color-mix(in srgb, var(--status-color, var(--impaza-primary)) 26%, var(--impaza-border));
  }

  .customers-page .customers-status-body {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
  }

  .customers-page .customers-status-copy {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .customers-page .customers-status-icon {
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

  .customers-page .customers-status-label {
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.25;
  }

  .customers-page .customers-status-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .customers-page .customers-status-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    color: var(--impaza-text);
  }

  .customers-page .customers-toolbar {
    grid-template-columns: minmax(120px, 150px) minmax(180px, 220px) minmax(280px, 1fr) auto auto;
  }

  .customers-page .customers-toolbar .toolbar-search-form,
  .customers-page .customers-toolbar .toolbar-search-form .input-group {
    width: 100%;
    min-width: 0;
  }

  .customers-page .customers-toolbar .btn {
    min-height: 36px;
    border-radius: 999px;
    padding-inline: 14px;
    font-weight: 600;
  }

  .customers-page .customer-name {
    font-weight: 700;
    color: var(--impaza-text);
    line-height: 1.25;
  }

  .customers-page .customer-helper {
    margin-top: 3px;
    font-size: .72rem;
    color: var(--impaza-muted);
    line-height: 1.35;
  }

  .customers-page .customer-actions .dropdown-menu {
    min-width: 220px;
  }

  @media (max-width: 1199.98px) {
    .customers-page .customers-status-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .customers-page .customers-toolbar {
      grid-template-columns: 1fr 1fr;
    }

    .customers-page .customers-toolbar .toolbar-search-form {
      grid-column: span 2;
    }
  }

  @media (max-width: 767.98px) {
    .customers-page .customers-status-grid {
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .customers-page .customers-toolbar {
      grid-template-columns: 1fr;
    }

    .customers-page .customers-toolbar .toolbar-search-form {
      grid-column: auto;
    }
  }
</style>
@endsection

@section('content')

<section class="content workflow-faults-page customers-page">
<div class="customers-status-grid">
  <a href="#" class="customers-status-link customersStatusStat" data-status-id="all">
    <div class="customers-status-card" style="--status-color:#64748B;">
      <div class="customers-status-body">
        <div class="customers-status-copy">
          <span class="customers-status-icon"><i class="fas fa-list"></i></span>
          <div>
            <div class="customers-status-label">All</div>
            <div class="customers-status-title">Customers</div>
          </div>
        </div>
        <div class="customers-status-value">{{ $totalCustomers ?? 0 }}</div>
      </div>
    </div>
  </a>
  @php
    $statusCards = [
      ['id'=>1,'label'=>'Pending','icon'=>'fa-hourglass-half','bar'=>'#EF4444'],
      ['id'=>2,'label'=>'Connected','icon'=>'fa-plug','bar'=>'#10B981'],
      ['id'=>3,'label'=>'Disconnected','icon'=>'fa-unlink','bar'=>'#F59E0B'],
      ['id'=>4,'label'=>'Decommissioned','icon'=>'fa-ban','bar'=>'#64748B'],
    ];
  @endphp
  @foreach($statusCards as $st)
    <a href="#" class="customers-status-link customersStatusStat" data-status-id="{{ $st['id'] }}">
      <div class="customers-status-card" style="--status-color: {{ $st['bar'] }}">
        <div class="customers-status-body">
          <div class="customers-status-copy">
            <span class="customers-status-icon"><i class="fas {{ $st['icon'] }}"></i></span>
            <div>
              <div class="customers-status-label">{{ $st['label'] }}</div>
              <div class="customers-status-title">Status</div>
            </div>
          </div>
          <div class="customers-status-value">{{ (int)($customerStatusCounts[$st['id']] ?? 0) }}</div>
        </div>
      </div>
    </a>
  @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Manage Customers</h3>
            <div class="page-lead">Search, filter, review, and manage customer lifecycle actions from one responsive workspace with modern table filters.</div>
        </div>
        <div class="card-tools">
            <span class="record-chip"><i class="fas fa-layer-group"></i> {{ $customers->total() }} total records</span>
            @can('customer-create')
            <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#customerCreateModal"><i class="fas fa-plus-circle me-1"></i> Create Customer </button>
            @endcan
        </div>
    </div>
    <div class="faults-toolbar">
        <div class="filter-toolbar customers-toolbar">
            <div class="faults-toolbar-field">
                @php $perPage = request('per_page', 20); @endphp
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="customersPageSize" class="form-select">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>Show 10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>Show 20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>Show 50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>Show 100</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    @php $statusSel = request('status'); @endphp
                    <select id="customersStatusFilter" class="form-select">
                        <option value="all" {{ empty($statusSel) || $statusSel==='all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="1" {{ (string)$statusSel === '1' ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ (string)$statusSel === '2' ? 'selected' : '' }}>Connected</option>
                        <option value="3" {{ (string)$statusSel === '3' ? 'selected' : '' }}>Disconnected</option>
                        <option value="4" {{ (string)$statusSel === '4' ? 'selected' : '' }}>Decommissioned</option>
                    </select>
                </div>
            </div>
            <form method="GET" action="{{ route('customers.index') }}" class="toolbar-search-form m-0">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search customers, account numbers, or managers">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <input type="hidden" name="status" value="{{ $statusSel }}">
                </div>
            </form>
            <button type="button" class="btn btn-primary btn-sm" id="customersApplyFilters"><i class="fas fa-search me-1"></i>Search</button>
            <a href="{{ route('customers.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Reset</a>
        </div>
    </div>
    <div class="card-body">
        <div class="faults-table-shell">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer</th>
                        <th>Account Manager</th>
                        <th>Account Number</th>
                        <th>Status</th>
                        <!-- <th>Address</th>
                        <th>Contact Number</th> -->
                        <th>Action(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                    <tr>
                        <td data-label="No."><span class="record-chip">#{{ $customers->firstItem() + $loop->index }}</span></td>
                        <td data-label="Customer">
                            <div class="record-meta">
                                <span class="customer-name">{{ $customer->customer }}</span>
                                <span class="customer-helper">Customer record</span>
                            </div>
                        </td>
                        <td data-label="Account Manager">
                            <div class="record-meta">
                                <span class="record-main">{{ $customer->accountManager ?: 'Unassigned' }}</span>
                                <span class="record-sub">Ownership</span>
                            </div>
                        </td>
                        <td data-label="Account Number">
                            <span class="record-chip"><i class="fas fa-hashtag"></i> {{ $customer->account_number }}</span>
                        </td>
                        <td data-label="Status">
                            @php
                                $statusMap = [1=>'Pending',2=>'Connected',3=>'Disconnected',4=>'Decommissioned'];
                                $statusColors = ['Pending'=>'#EF4444','Connected'=>'#10B981','Disconnected'=>'#F59E0B','Decommissioned'=>'#64748B'];
                                $label = $statusMap[(int)($customer->customer_status ?? 2)] ?? 'Connected';
                                $color = $statusColors[$label] ?? '#6c757d';
                            @endphp
                            <x-status-badge :label="$label" :color="$color" :soft="true" />
                        </td>
                        <td class="text-end" data-label="Action(s)">
                            <div class="workflow-actions customer-actions">
                              <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerViewModal{{ $customer->id }}" title="View">
                                <i class="fas fa-eye me-1"></i> View
                              </button>
                              @can('customer-edit')
                              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customerEditModal{{ $customer->id }}" title="Edit">
                                <i class="fas fa-edit me-1"></i> Edit
                              </button>
                              @endcan
                              @can('customer-delete')
                              <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#customerDeleteModal{{ $customer->id }}" title="Delete">
                                <i class="fas fa-trash me-1"></i> Delete
                              </button>
                              @endcan
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v me-1"></i> More
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                @can('finance-link-update')
                                  @php $custStatus = (int)($customer->customer_status ?? 2); @endphp
                                  @if($custStatus === 2)
                                  <li>
                                    <form action="{{ route('customers.disconnect',$customer->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_disconnect" title="Disconnect">
                                        <i class="fas fa-unlink text-warning"></i>
                                        <span class="text-warning">Disconnect</span>
                                      </button>
                                    </form>
                                  </li>
                                  @endif
                                  @if($custStatus === 3)
                                  <li>
                                    <form action="{{ route('customers.reconnect',$customer->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect Disconnected Links">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect (Disconnected)</span>
                                      </button>
                                    </form>
                                  </li>
                                  @endif
                                  @if($custStatus === 4)
                                  <li>
                                    <form action="{{ route('customers.reconnect',$customer->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect Disconnected Links">
                                        <i class="fas fa-plug text-success"></i>
                                        <span class="text-success">Reconnect (Disconnected)</span>
                                      </button>
                                    </form>
                                  </li>
                                  <li>
                                    <form action="{{ route('customers.reconnect_decommissioned',$customer->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="submit" class="dropdown-item d-flex align-items-center gap-2" title="Reconnect Decommissioned Links">
                                        <i class="fas fa-plug text-primary"></i>
                                        <span class="text-primary">Reconnect (Decommissioned)</span>
                                      </button>
                                    </form>
                                  </li>
                                  @endif
                                  <li>
                                    <form action="{{ route('customers.decommission',$customer->id) }}" method="POST" class="px-2 m-0">
                                      @csrf
                                      @method('PUT')
                                      <button type="button" class="dropdown-item d-flex align-items-center gap-2 confirm_decommission" title="Decommission">
                                        <i class="fas fa-ban text-danger"></i>
                                        <span class="text-danger">Decommission</span>
                                      </button>
                                    </form>
                                  </li>
                                @endcan
                              </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
            <div class="workflow-pagination">
              <small class="text-muted">
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
              </small>
              {{ $customers->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>

            @include('customers.create_modal')
            @include('customers.edit_modal')
            @include('customers.view_modal')
            @include('customers.delete_modal')
        </div>
    </div>
</div>
 
</section>
@endsection

@section('scripts')
<script>
  document.getElementById('customersApplyFilters')?.addEventListener('click', function(){
    const form = document.querySelector('.customers-toolbar .toolbar-search-form');
    if (form) form.submit();
  });
  document.getElementById('customersPageSize')?.addEventListener('change', function(){
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', this.value);
    params.delete('page');
    window.location.search = params.toString();
  });
  document.getElementById('customersStatusFilter')?.addEventListener('change', function(){
    const params = new URLSearchParams(window.location.search);
    params.set('status', this.value);
    params.delete('page');
    window.location.search = params.toString();
  });
  document.querySelectorAll('.customersStatusStat').forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault();
      const statusId = this.getAttribute('data-status-id');
      const params = new URLSearchParams(window.location.search);
      params.set('status', statusId);
      params.delete('page');
      window.location.search = params.toString();
    });
  });
</script>
@endsection



