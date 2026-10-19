@extends('layouts.admin')

@section('title')
Process — {{ $mr->request_number }}
@endsection
@include('partials.css')
@section('content')

<section class="content workflow-faults-page">
<div class="card faults-panel mb-4">
    <div class="faults-panel-header">
        <div class="faults-panel-copy">
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('stores.requests') }}" class="text-muted text-decoration-none me-2" title="Back to list">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h3 class="faults-panel-title mb-0"><i class="fas fa-clipboard-check me-2"></i>Process Material Request</h3>
            </div>
            <div class="faults-panel-subtitle mt-1">
                <span class="font-monospace me-2">{{ $mr->request_number }}</span>
                @php $b = $mr->statusBadge(); @endphp
                <x-status-badge :label="$b['label']" :color="$b['color']" :soft="true" />
                <span class="mx-2">·</span>
                <small>Requested by <strong>{{ optional($mr->requestedBy)->name ?: 'Unknown' }}</strong>
                @if($mr->fault) for <strong>{{ $mr->fault->fault_ref_number }}</strong> · {{ optional(optional($mr->fault)->customer)->customer }}@endif</small>
            </div>
        </div>
        <div class="faults-panel-actions">
            @can('stores-process')
            <button class="btn btn-outline-secondary btn-sm rounded-pill" id="grantAllBtn" type="button">
                <i class="fas fa-check me-1"></i>Grant All
            </button>
            <button class="btn btn-outline-danger btn-sm rounded-pill ms-1" id="denyAllBtn" type="button">
                <i class="fas fa-ban me-1"></i>Mark None Available
            </button>
            @endcan
        </div>
    </div>
</div>

@if($mr->fault)
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-3 small">
            <div class="col-md-2">
                <div class="text-muted">Fault Ref</div>
                <strong>{{ $mr->fault->fault_ref_number }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Customer</div>
                <strong>{{ optional(optional($mr->fault)->customer)->customer ?: '—' }}</strong>
            </div>
            <div class="col-md-2">
                <div class="text-muted">City</div>
                <strong>{{ optional(optional($mr->fault)->city)->city ?: '—' }}</strong>
            </div>
            <div class="col-md-2">
                <div class="text-muted">Location</div>
                <strong>{{ optional(optional($mr->fault)->suburb)->suburb ?: '—' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted">Link / POP</div>
                <strong>{{ optional(optional($mr->fault)->link)->link ?: '—' }} / {{ optional(optional($mr->fault)->pop)->pop ?: '—' }}</strong>
            </div>
            @if($mr->fault->address)
            <div class="col-12 border-top pt-2">
                <div class="text-muted">Address</div>
                <div>{{ $mr->fault->address }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@if($mr->technician_note)
<div class="alert alert-info" role="alert">
    <i class="fas fa-comment-dots me-2"></i><strong>Technician says:</strong> {{ $mr->technician_note }}
</div>
@endif

<form action="{{ route('stores.process', $mr->id) }}" method="POST">
@csrf
<div class="card faults-panel">
    <div class="faults-toolbar py-3">
        <div class="d-flex justify-content-between align-items-center px-3 flex-wrap gap-2">
            <div class="fw-semibold"><i class="fas fa-boxes-stacked me-1 text-secondary"></i>Requested Materials</div>
            <div class="form-text small">Tick <span class="text-success fw-medium">Issue</span> for available items; untick <span class="text-success">Available</span> for out-of-stock. Adjust issued qty if partial.</div>
        </div>
    </div>

    <div class="faults-table-shell">
        <div class="table-responsive">
            <table class="table align-middle impaza-table faults-table issue-line-table">
                <thead>
                    <tr>
                        <th style="width:2%" class="text-center">#</th>
                        <th style="width:30%">Material</th>
                        <th style="width:6%" class="text-center">Unit</th>
                        <th style="width:9%" class="text-end">Requested</th>
                        <th style="width:9%" class="text-end">In Stock</th>
                        <th style="width:8%" class="text-center">Available</th>
                        <th style="width:8%" class="text-center">Issue</th>
                        <th style="width:11%" class="text-end">Qty Issued</th>
                        <th>Remark / Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mr->items as $idx => $item)
                    @php
                        $stock = optional($item->material)->quantity_on_hand;
                        $available = $item->is_available ?? true;
                        $issued = $item->quantity_issued > 0 ? $item->quantity_issued : ($available ? $item->quantity_requested : 0);
                        $isChecked = $item->quantity_issued > 0 || (is_null($item->is_available) || $item->is_available);
                    @endphp
                    <tr class="item-row" data-row="{{ $idx }}" data-item="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-medium">{{ $item->material_name }}</div>
                            @if($item->material)
                                <div class="small text-muted">
                                    @if($item->material->sku)<span class="font-monospace">{{ $item->material->sku }}</span> · @endif
                                    {{ $item->material->category ?? 'General' }}
                                </div>
                            @else
                                <div class="small text-warning"><i class="fas fa-triangle-exclamation me-1"></i>Not in catalogue</div>
                            @endif
                        </td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->unit }}</span></td>
                        <td class="text-end fw-medium req-qty">{{ number_format($item->quantity_requested, 2) }}</td>
                        <td class="text-end">
                            @if(isset($stock))
                                <span class="{{ (float)$stock <= 0 ? 'text-danger fw-medium' : ((float)$stock < (float)$item->quantity_requested ? 'text-warning fw-medium' : 'text-success') }}">
                                    {{ number_format($stock, 2) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-flex justify-content-center">
                                <input type="hidden" name="items[{{ $idx }}][available]" value="0">
                                <input class="form-check-input avail-check" type="checkbox"
                                    name="items[{{ $idx }}][available]"
                                    value="1"
                                    data-row="{{ $idx }}"
                                    {{ $available ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-flex justify-content-center">
                                <input type="hidden" name="items[{{ $idx }}][issued]" value="0">
                                <input class="form-check-input issue-check" type="checkbox"
                                    name="items[{{ $idx }}][issued]"
                                    value="1"
                                    data-row="{{ $idx }}"
                                    {{ $isChecked ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" min="0"
                                    name="items[{{ $idx }}][quantity_issued]"
                                    class="form-control form-control-sm qty-issued text-end"
                                    data-row="{{ $idx }}"
                                    value="{{ $issued }}"
                                    max="{{ $item->quantity_requested }}"
                                    style="min-width:90px">
                            </div>
                        </td>
                        <td>
                            <input type="text"
                                name="items[{{ $idx }}][remark]"
                                class="form-control form-control-sm remark-input"
                                placeholder="Optional note (e.g. partial, blue only)"
                                value="{{ $item->remark }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label small fw-semibold"><i class="fas fa-sticky-note me-1 text-secondary"></i>Note to Technician / Stores Log</label>
            <textarea class="form-control form-control-sm" name="stores_note" rows="2" placeholder="Any notes, shortfalls, or follow-up action...">{{ old('stores_note', $mr->stores_note) }}</textarea>
        </div>
        <div class="d-flex gap-2 justify-content-end flex-wrap">
            <a href="{{ route('stores.requests') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="fas fa-xmark me-1"></i>Cancel
            </a>
            <button type="submit" class="btn btn-success btn-sm rounded-pill">
                <i class="fas fa-check-double me-1"></i>Acknowledge & Process Request
            </button>
        </div>
    </div>
</div>
</form>

@endsection

@section('scripts')
@include('partials.scripts')
<script>
(function () {
    function syncRow(rowIdx) {
        const tr = document.querySelector('tr.item-row[data-row="' + rowIdx + '"]');
        if (!tr) return;
        const avail = tr.querySelector('.avail-check');
        const issue = tr.querySelector('.issue-check');
        const qty = tr.querySelector('.qty-issued');
        const req = tr.querySelector('.req-qty');
        const reqVal = parseFloat(req ? req.textContent.replace(/,/g,'') : '0') || 0;
        if (!avail.checked) {
            issue.checked = false;
            issue.disabled = true;
            qty.value = '0';
            qty.disabled = true;
        } else {
            issue.disabled = false;
            qty.disabled = false;
            if (issue.checked && (!qty.value || parseFloat(qty.value) === 0)) {
                qty.value = reqVal.toFixed(2);
            }
            if (!issue.checked) {
                qty.value = '0';
            }
        }
    }
    function bindRow(idx) {
        const tr = document.querySelector('tr.item-row[data-row="' + idx + '"]');
        if (!tr) return;
        const avail = tr.querySelector('.avail-check');
        const issue = tr.querySelector('.issue-check');
        const qty = tr.querySelector('.qty-issued');
        const req = tr.querySelector('.req-qty');
        avail?.addEventListener('change', () => syncRow(idx));
        issue?.addEventListener('change', () => syncRow(idx));
        qty?.addEventListener('input', () => {
            const v = parseFloat(qty.value);
            if (v > 0) { issue.checked = true; avail.checked = avail.checked || true; }
        });
        syncRow(idx);
    }
    document.querySelectorAll('tr.item-row').forEach(r => bindRow(r.dataset.row));

    document.getElementById('grantAllBtn')?.addEventListener('click', function () {
        document.querySelectorAll('tr.item-row').forEach(tr => {
            const idx = tr.dataset.row;
            const avail = tr.querySelector('.avail-check');
            const issue = tr.querySelector('.issue-check');
            const qty = tr.querySelector('.qty-issued');
            const req = tr.querySelector('.req-qty');
            const reqVal = parseFloat(req ? req.textContent.replace(/,/g,'') : '0') || 0;
            avail.checked = true;
            issue.checked = true;
            qty.disabled = false;
            issue.disabled = false;
            qty.value = reqVal.toFixed(2);
        });
    });
    document.getElementById('denyAllBtn')?.addEventListener('click', function () {
        document.querySelectorAll('tr.item-row').forEach(tr => {
            const idx = tr.dataset.row;
            const avail = tr.querySelector('.avail-check');
            const issue = tr.querySelector('.issue-check');
            const qty = tr.querySelector('.qty-issued');
            avail.checked = false;
            issue.checked = false;
            issue.disabled = true;
            qty.value = '0';
            qty.disabled = true;
        });
    });
})();
</script>
@endsection
