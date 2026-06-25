@extends('layouts.admin')

@section('title')
Cleared
@endsection

@include('partials.css')

@section('content')
<section class="content workflow-faults-page">
  <div class="card faults-panel">
    <div class="faults-panel-header">
      <div class="faults-panel-copy">
        <h3 class="faults-panel-title">Cleared by NOC</h3>
        <div class="faults-panel-subtitle">Review recently cleared faults, audit the latest updates, and revoke to NOC when needed.</div>
      </div>
      <div class="faults-panel-actions"></div>
    </div>

    <div class="faults-toolbar">
      <form method="GET" action="{{ route('resolved.index') }}" class="m-0">
        @php $perPage = request('per_page', 20); @endphp
        <div class="faults-toolbar-grid">
          <div class="faults-toolbar-field">
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="fas fa-list"></i></span>
              <select name="per_page" id="resolvedPageSize" class="form-select form-select-sm" aria-label="Rows per page">
                <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
                <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
                <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
              </select>
            </div>
          </div>
          <div class="faults-toolbar-field faults-toolbar-search" style="grid-column: span 3;">
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
              <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search faults, customers, links, and users...">
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 faults-toolbar-submit">
            <i class="fas fa-search me-1"></i> Search
          </button>
          <a href="{{ route('resolved.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 faults-toolbar-reset">
            <i class="fas fa-rotate-left me-1"></i> Reset
          </a>
        </div>
      </form>
    </div>

    <div class="faults-table-shell">
      <div class="table-responsive impaza-table-wrap faults-table-wrap">
        <table class="table table-hover align-middle impaza-table faults-table">
          <thead>
            <tr>
              <th>Ref. No.</th>
              <th>Customer</th>
              <th>Account Manager</th>
              <th>Link</th>
              <th>Assigned To</th>
              <th>Logged By</th>
              <th>Cleared At</th>
              <th>Action(s)</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($faults as $fault)
            <tr>
              <td data-label="Ref. No.">
                <div class="faults-ref">
                  <span class="faults-cell-main">{{ $fault->fault_ref_number }}</span>
                  <span class="faults-cell-sub">Fault record</span>
                </div>
              </td>
              <td data-label="Customer">
                <div class="faults-cell-main">{{ $fault->customer }}</div>
              </td>
              <td data-label="Account Manager">
                <div class="faults-cell-main">{{ $fault->accountManager ?: 'N/A' }}</div>
              </td>
              <td data-label="Link">
                <div class="faults-cell-main">{{ $fault->link }}</div>
              </td>
              <td data-label="Assigned To">
                <div class="faults-cell-main {{ $fault->assignedTo ? '' : 'text-muted fw-normal' }}">{{ $fault->assignedTo ?: 'Not assigned' }}</div>
              </td>
              <td data-label="Logged By">
                <div class="faults-cell-main">{{ $fault->reportedBy ?: 'N/A' }}</div>
              </td>
              <td data-label="Cleared At">
                <div class="faults-cell-main">{{ \Carbon\Carbon::parse($fault->updated_at)->format('d M Y') }}</div>
                <div class="faults-cell-sub">{{ \Carbon\Carbon::parse($fault->updated_at)->format('h:i a') }}</div>
              </td>
              <td data-label="Action(s)">
                <div class="faults-actions">
                  <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                    <i class="fas fa-eye me-1"></i> View
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resolvedRevoke-{{ $fault->id }}">
                    <i class="fas fa-undo me-1"></i> Revoke
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @if ($faults->isEmpty())
              <tr>
                <td colspan="8" class="text-center text-muted py-5">No cleared faults in the last 24 hours</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <div class="faults-table-footer">
        <small class="text-muted">Showing {{ $faults->firstItem() ?? 0 }} to {{ $faults->lastItem() ?? 0 }} of {{ $faults->total() }} results</small>
        <div>{{ $faults->appends(request()->except('page'))->links('pagination::bootstrap-5') }}</div>
      </div>
    </div>
  </div>

  @foreach ($faults as $fault)
  <div class="modal custom-modal fade" id="resolvedRevoke-{{ $fault->id }}" tabindex="-1" aria-labelledby="resolvedRevokeLabel-{{ $fault->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div class="fault-modal-header-copy">
            <h5 class="modal-title" id="resolvedRevokeLabel-{{ $fault->id }}"><i class="fas fa-rotate-left me-2"></i>Revoke Fault</h5>
            <div class="text-muted small mt-1">Move {{ $fault->fault_ref_number }} back to the NOC workflow with a clear reason.</div>
            <div class="fault-modal-meta">
              <span class="fault-modal-meta-item"><i class="fas fa-hashtag"></i> {{ $fault->fault_ref_number }}</span>
              <span class="fault-modal-meta-item"><i class="fas fa-user"></i> {{ $fault->customer }}</span>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('resolved.revoke', $fault->id) }}">
          @csrf
          <div class="modal-body">
            <div class="fault-modal-note mb-3">
              <i class="fas fa-triangle-exclamation"></i>
              <div>This action returns the fault to the previous NOC stage, so capture the operational reason before you continue.</div>
            </div>
            <div class="fault-modal-section">
              <div class="fault-modal-section-header">
                <span class="fault-modal-section-icon"><i class="fas fa-comment-dots"></i></span>
                <div>
                  <div class="fault-modal-section-title">Revoke Note</div>
                  <div class="fault-modal-section-subtitle">Explain why the cleared state should be reversed.</div>
                </div>
              </div>
              <div class="fault-modal-section-body">
                <label class="form-label">Remark</label>
                <textarea name="remark" class="form-control" rows="3" required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer fault-modal-footer">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" class="btn btn-danger btn-sm rounded-pill">
              <i class="fas fa-undo me-1"></i> Revoke
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @include('faults.show', [
      'fault' => $fault,
      'remarks' => ($remarksByFault[$fault->id] ?? collect()),
      'ageText' => ($faultAges[$fault->id] ?? ''),
      'ageStart' => ($faultAgeStart[$fault->id] ?? null),
      'ageEnd' => ($faultAgeEnd[$fault->id] ?? null),
  ])
  @endforeach
</section>
@endsection

@section('scripts')
  @include('partials.scripts')
  <script>
    window.currentUserName = @json(optional(auth()->user())->name);
    document.getElementById('resolvedPageSize')?.addEventListener('change', function () {
      this.form?.submit();
    });
  </script>
@endsection
