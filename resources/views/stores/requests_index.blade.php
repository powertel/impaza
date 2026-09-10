@extends('layouts.admin')

@section('title')
Stores — Material Requests
@endsection
@include('partials.css')
@section('content')

<section class="content workflow-faults-page">
<div class="card faults-panel">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <h3 class="faults-panel-title"><i class="fas fa-dolly me-2"></i>Material Requests</h3>
            <div class="faults-panel-subtitle">Review material requests from technicians, issue items and mark unavailability per line.</div>
        </div>
        <div class="faults-panel-actions">
            @canany(['material-list','material-create'])
                <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-warehouse me-1"></i>Inventory
                </a>
            @endcanany
        </div>
    </div>

    <div class="faults-toolbar">
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#F59E0B;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-hourglass-half"></i></div>
                        <div class="impaza-stat-title">Pending</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value">{{ number_format($stats['pending']) }}</div>
                            <div class="impaza-stat-sub">Awaiting Stores review &amp; processing</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#8B5CF6;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-boxes-stacked"></i></div>
                        <div class="impaza-stat-title">Partial</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value">{{ number_format($stats['partial']) }}</div>
                            <div class="impaza-stat-sub">Some lines issued; remainder pending stock</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#10B981;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-circle-check"></i></div>
                        <div class="impaza-stat-title">Issued</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value">{{ number_format($stats['issued']) }}</div>
                            <div class="impaza-stat-sub">All items dispatched from Stores</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="impaza-stat h-100" style="--impaza-stat-accent:#3B82F6;">
                    <div class="impaza-stat-head">
                        <div class="impaza-stat-icon"><i class="fas fa-file-lines"></i></div>
                        <div class="impaza-stat-title">Total</div>
                    </div>
                    <div class="impaza-stat-body">
                        <div class="impaza-stat-metric">
                            <div class="impaza-stat-value">{{ number_format($stats['total']) }}</div>
                            <div class="impaza-stat-sub">All material requests (all statuses)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('stores.requests') }}" class="faults-toolbar-grid">
            <div class="faults-toolbar-field">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" {{ $perPage==10?'selected':'' }}>10</option>
                        <option value="20" {{ $perPage==20?'selected':'' }}>20</option>
                        <option value="50" {{ $perPage==50?'selected':'' }}>50</option>
                        <option value="100" {{ $perPage==100?'selected':'' }}>100</option>
                    </select>
                </div>
            </div>
            <div class="faults-toolbar-field">
                <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                    <option value="pending" {{ $statusFilter=='pending'?'selected':'' }}>Pending</option>
                    <option value="processing" {{ $statusFilter=='processing'?'selected':'' }}>Processing</option>
                    <option value="partial" {{ $statusFilter=='partial'?'selected':'' }}>Partial</option>
                    <option value="issued" {{ $statusFilter=='issued'?'selected':'' }}>Issued</option>
                    <option value="cancelled" {{ $statusFilter=='cancelled'?'selected':'' }}>Cancelled</option>
                    <option value="all" {{ $statusFilter=='all'?'selected':'' }}>All Statuses</option>
                </select>
            </div>
            <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Search request #, fault ref, technician..." value="{{ $q }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
                <i class="fas fa-search me-1"></i>Search
            </button>
            <a href="{{ route('stores.requests') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
                <i class="fas fa-rotate-left me-1"></i>Reset
            </a>
        </form>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive impaza-table-wrap faults-table-wrap">
            <table class="table table-hover align-middle impaza-table faults-table">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Fault</th>
                        <th>Technician</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequests as $mr)
                    @php
                        $badge = $mr->statusBadge();
                        $itemsCount = $mr->items->count();
                        $issuedCount = $mr->items->filter(fn($i)=>$i->quantity_issued>0)->count();
                    @endphp
                    <tr>
                        <td>
                            <span class="font-monospace fw-medium">{{ $mr->request_number }}</span>
                        </td>
                        <td>
                            @if($mr->fault)
                                <div class="fw-medium">{{ $mr->fault->fault_ref_number }}</div>
                                <div class="small text-muted">{{ optional($mr->fault->customer)->customer }}</div>
                            @else
                                <span class="text-muted">Fault #{{ $mr->fault_id }} (deleted)</span>
                            @endif
                        </td>
                        <td>{{ optional($mr->requestedBy)->name ?: '—' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $itemsCount }} items</span>
                            @if($issuedCount>0)
                                <span class="badge bg-success-subtle text-success border ms-1">{{ $issuedCount }} issued</span>
                            @endif
                        </td>
                        <td>
                            <x-status-badge :label="$badge['label']" :color="$badge['color']" :soft="true" />
                        </td>
                        <td>
                            <small>{{ $mr->created_at ? \Carbon\Carbon::parse($mr->created_at)->format('j M Y, H:i') : '—' }}</small>
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#viewMRModal-{{ $mr->id }}" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            @can('stores-process')
                                @if($mr->isPending() || $mr->status===\App\Models\MaterialRequest::STATUS_PROCESSING)
                                    <a href="{{ route('stores.issue', $mr->id) }}" class="btn btn-sm btn-outline-primary rounded-pill ms-1" title="Issue items for this request (Stores / Technician acknowledgement)" style="min-width:7.5rem;">
                                        <i class="fas fa-boxes-packing me-1"></i>Process
                                    </a>
                                @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No requests matching your filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="faults-table-footer">
            <small class="text-muted">Showing {{ $materialRequests->count() }} of {{ $materialRequests->total() }} requests</small>
            <div>{{ $materialRequests->links() }}</div>
        </div>
    </div>
</div>
</section>

@foreach($materialRequests as $mr)
  @include('stores.show_modal', ['mr' => $mr])
@endforeach

@endsection

@section('scripts')
  @include('partials.scripts')
@endsection
