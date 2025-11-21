@extends('layouts.admin')

@section('title')
Customers
@endsection

@include('partials.css')

@section('content')

<section class="content">
<div class="row row-cols-5 g-3 mb-3">
  <div class="col">
    <a href="#" class="text-decoration-none customersStatusStat" data-status-id="all">
      <div class="card shadow-sm border-0">
        <div class="rounded-top" style="height:6px; background:#6c757d"></div>
        <div class="card-body d-flex justify-content-between align-items-center py-3">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary"><i class="fas fa-list"></i></span>
            <div>
              <div class="text-muted small">All</div>
              <div class="fw-semibold">Customers</div>
            </div>
          </div>
          <div class="fs-5 fw-bold text-dark">{{ $totalCustomers ?? 0 }}</div>
        </div>
      </div>
    </a>
  </div>
  @php
    $statusCards = [
      ['id'=>1,'label'=>'Pending','icon'=>'fa-hourglass-half','bar'=>'#ff8080','badge'=>'bg-danger'],
      ['id'=>2,'label'=>'Connected','icon'=>'fa-plug','bar'=>'#90EE90','badge'=>'bg-success'],
      ['id'=>3,'label'=>'Disconnected','icon'=>'fa-unlink','bar'=>'#FFFF00','badge'=>'bg-warning'],
      ['id'=>4,'label'=>'Decommissioned','icon'=>'fa-ban','bar'=>'#A9A9A9','badge'=>'bg-secondary'],
    ];
  @endphp
  @foreach($statusCards as $st)
    <div class="col">
      <a href="#" class="text-decoration-none customersStatusStat" data-status-id="{{ $st['id'] }}">
        <div class="card shadow-sm border-0">
          <div class="rounded-top" style="height:6px; background: {{ $st['bar'] }}"></div>
          <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <span class="badge {{ $st['badge'] }}"><i class="fas {{ $st['icon'] }}"></i></span>
              <div>
                <div class="text-muted small">{{ $st['label'] }}</div>
                <div class="fw-semibold">Status</div>
              </div>
            </div>
            <div class="fs-5 fw-bold text-dark">{{ (int)($customerStatusCounts[$st['id']] ?? 0) }}</div>
          </div>
        </div>
      </a>
    </div>
  @endforeach
</div>

<div class="card">
    <!--Card Header-->
    <div class="card-header">
        <h3 class="card-title">Customers</h3>
        <div class="card-tools">
            @can('customer-create')
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#customerCreateModal"><i class="fas fa-plus-circle"></i> Create Customer(s) </button>
            @endcan            
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="table-responsive">
            <div class="filter-toolbar d-flex justify-content-end align-items-center gap-2 mb-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    @php $perPage = request('per_page', 20); @endphp
                    <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
                    <select id="customersPageSize" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                        <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                        <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text"><i class="fas fa-filter me-1"></i> Status</span>
                    @php $statusSel = request('status'); @endphp
                    <select id="customersStatusFilter" class="form-select form-select-sm" style="width:auto;">
                        <option value="all" {{ empty($statusSel) || $statusSel==='all' ? 'selected' : '' }}>All</option>
                        <option value="1" {{ (string)$statusSel === '1' ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ (string)$statusSel === '2' ? 'selected' : '' }}>Connected</option>
                        <option value="3" {{ (string)$statusSel === '3' ? 'selected' : '' }}>Disconnected</option>
                        <option value="4" {{ (string)$statusSel === '4' ? 'selected' : '' }}>Decommissioned</option>
                    </select>
                </div>
                <form method="GET" action="{{ route('customers.index') }}" class="m-0">
                    <div class="input-group input-group-sm" style="width: 360px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search all records">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
                        <a href="{{ route('customers.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
                    </div>
                </form>
            </div>
            <table  class="table table-hover">
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
                        <td>{{ $customers->firstItem() + $loop->index }}</td>
                        <td>{{ $customer->customer}}</td>
                        <td>{{ $customer->accountManager }}</td>
                        <td>{{ $customer->account_number }}</td>
                        <td>
                            @php
                                $statusMap = [1=>'Pending',2=>'Connected',3=>'Disconnected',4=>'Decommissioned'];
                                $statusColors = ['Pending'=>'#ff8080','Connected'=>'#90EE90','Disconnected'=>'#FFFF00','Decommissioned'=>'#A9A9A9'];
                                $label = $statusMap[(int)($customer->customer_status ?? 2)] ?? 'Connected';
                                $color = $statusColors[$label] ?? '#6c757d';
                            @endphp
                            <span class="badge rounded-pill" style="background-color: {{ $color }}; color: black; padding: 0.35rem 0.6rem; font-weight: 600;">
                                {{ $label }}
                            </span>
                        </td>
                         <!-- <td>{{ $customer->address ?? '' }}</td>
                        <td>{{ $customer->contact_number ?? '' }}</td> -->
                        <td>
                            <div class="btn-group">
                              <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i> Actions
                              </button>
                              <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                                <li>
                                  <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#customerViewModal{{ $customer->id }}" title="View">
                                    <i class="fas fa-eye text-success"></i>
                                    <span>View</span>
                                  </a>
                                </li>
                                @can('customer-edit')
                                <li>
                                  <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#customerEditModal{{ $customer->id }}" title="Edit">
                                    <i class="fas fa-edit text-primary"></i>
                                    <span>Edit</span>
                                  </a>
                                </li>
                                @endcan
                                @can('customer-delete')
                                <li>
                                  <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#customerDeleteModal{{ $customer->id }}" title="Delete">
                                    <i class="fas fa-trash text-danger"></i>
                                    <span class="text-danger">Delete</span>
                                  </a>
                                </li>
                                @endcan
                                <li><hr class="dropdown-divider"></li>
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
            <div class="d-flex justify-content-between align-items-center mt-3">
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
    <!-- /.card-body -->
</div>
 
</section>
@endsection

@section('scripts')
<script>
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



