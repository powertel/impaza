@php
    $badge = $mr->statusBadge();
@endphp
<div class="modal custom-modal fade" id="viewMRModal-{{ $mr->id }}" tabindex="-1" aria-labelledby="viewMRModalLabel-{{ $mr->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0" id="viewMRModalLabel-{{ $mr->id }}"><i class="fas fa-file-invoice me-2"></i>Request Details</h5>
          <div class="mt-1">
            <span class="font-monospace me-2">{{ $mr->request_number }}</span>
            <x-status-badge :label="$badge['label']" :color="$badge['color']" :soft="true" />
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="fault-modal-section h-100">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div>
                  <div class="fault-modal-section-title">Fault</div>
                  <div class="fault-modal-section-subtitle">The fault this request is linked to.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                <div class="fault-modal-grid">
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Fault Ref.</span>
                    <div class="fault-modal-kv-value">{{ optional($mr->fault)->fault_ref_number ?: '#' . $mr->fault_id }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Customer</span>
                    <div class="fault-modal-kv-value">{{ optional(optional($mr->fault)->customer)->customer ?: '—' }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">City</span>
                    <div class="fault-modal-kv-value">{{ optional(optional($mr->fault)->city)->city ?: '—' }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Location</span>
                    <div class="fault-modal-kv-value">{{ optional(optional($mr->fault)->suburb)->suburb ?: '—' }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Link</span>
                    <div class="fault-modal-kv-value">{{ optional(optional($mr->fault)->link)->link ?: '—' }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">POP</span>
                    <div class="fault-modal-kv-value">{{ optional(optional($mr->fault)->pop)->pop ?: '—' }}</div>
                  </div>
                </div>
                @can('fault-list')
                    @if($mr->fault)
                    <div class="mt-2">
                        <a href="{{ route('faults.show', $mr->fault->id) }}" target="_blank" class="small text-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Open Fault Profile
                        </a>
                    </div>
                    @endif
                @endcan
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="fault-modal-section h-100">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-user-circle"></i></span>
                <div>
                  <div class="fault-modal-section-title">Request Timeline</div>
                  <div class="fault-modal-section-subtitle">Who requested and when it was processed.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                <div class="fault-modal-grid">
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Technician</span>
                    <div class="fault-modal-kv-value">{{ optional($mr->requestedBy)->name ?: '—' }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Submitted</span>
                    <div class="fault-modal-kv-value"><small>{{ $mr->submitted_at ? \Carbon\Carbon::parse($mr->submitted_at)->format('j M Y H:i') : '—' }}</small></div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Processed By</span>
                    <div class="fault-modal-kv-value">{{ optional($mr->processedBy)->name ?: '—' }}</div>
                  </div>
                  <div class="fault-modal-kv">
                    <span class="fault-modal-kv-label">Processed At</span>
                    <div class="fault-modal-kv-value"><small>{{ $mr->processed_at ? \Carbon\Carbon::parse($mr->processed_at)->format('j M Y H:i') : '—' }}</small></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        @if($mr->technician_note || $mr->stores_note)
        <div class="row g-3 mb-3">
            @if($mr->technician_note)
            <div class="col-md-6">
                <div class="small text-muted fw-semibold mb-1"><i class="fas fa-comment-dots me-1"></i> Technician's Note</div>
                <div class="card card-body bg-light border p-2 small">{{ $mr->technician_note }}</div>
            </div>
            @endif
            @if($mr->stores_note)
            <div class="col-md-6">
                <div class="small text-muted fw-semibold mb-1"><i class="fas fa-warehouse me-1"></i> Stores' Note</div>
                <div class="card card-body bg-light border p-2 small">{{ $mr->stores_note }}</div>
            </div>
            @endif
        </div>
        @endif

        <div class="mt-2">
          <div class="small text-muted fw-semibold mb-2"><i class="fas fa-list-ul me-1"></i>Requested Materials</div>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:40%">Material</th>
                  <th class="text-center">Unit</th>
                  <th class="text-end">Requested</th>
                  <th class="text-end">Issued</th>
                  <th class="text-center">Available</th>
                  <th>Remark</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mr->items as $item)
                <tr class="{{ is_null($item->is_available) ? '' : (!$item->is_available ? 'table-danger' : ($item->isFullyIssued() ? 'table-success' : 'table-warning')) }}">
                  <td>
                    <div class="fw-medium">{{ $item->material_name }}</div>
                    @if($item->material)
                        <div class="small text-muted"><span class="font-monospace">{{ $item->material->sku ?? 'no sku' }}</span> · {{ $item->material->category ?? '' }}</div>
                    @endif
                  </td>
                  <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->unit }}</span></td>
                  <td class="text-end fw-medium">{{ number_format($item->quantity_requested, 2) }}</td>
                  <td class="text-end">
                    <span class="{{ $item->quantity_issued>0 ? 'text-success fw-medium' : 'text-muted' }}">
                      {{ number_format($item->quantity_issued, 2) }}
                    </span>
                  </td>
                  <td class="text-center">
                    @if(is_null($item->is_available))
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border">Pending</span>
                    @elseif($item->is_available)
                        <span class="badge bg-success-subtle text-success-emphasis border"><i class="fas fa-check"></i> Yes</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger-emphasis border"><i class="fas fa-xmark"></i> No Stock</span>
                    @endif
                  </td>
                  <td class="small text-muted">{{ $item->remark ?: '—' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer fault-modal-footer">
        @can('stores-process')
            @if($mr->isPending() || $mr->status===\App\Models\MaterialRequest::STATUS_PROCESSING)
                <a href="{{ route('stores.issue', $mr->id) }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="fas fa-check-double me-1"></i>Process / Issue
                </a>
            @endif
        @endcan
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
