@extends('layouts.admin')

@section('title')
Cleared
@endsection

@section('content')
<section class="content workflow-faults-page">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="card-title mb-0">Cleared by NOC (last 24 hours)</h3>
      <div class="card-tools">
        <form method="GET" action="{{ route('resolved.index') }}" class="m-0 d-flex align-items-center gap-2">
          @php $perPage = request('per_page', 20); @endphp
          <div class="input-group input-group-sm" style="width: 200px;">
            <span class="input-group-text"><i class="fas fa-list me-1"></i> Show</span>
            <select name="per_page" id="resolvedPageSize" class="form-select form-select-sm" style="width:auto;">
              <option value="10"  {{ (int)$perPage===10 ? 'selected' : '' }}>10</option>
              <option value="20"  {{ (int)$perPage===20 ? 'selected' : '' }}>20</option>
              <option value="50"  {{ (int)$perPage===50 ? 'selected' : '' }}>50</option>
              <option value="100" {{ (int)$perPage===100 ? 'selected' : '' }}>100</option>
            </select>
          </div>
          <div class="input-group input-group-sm" style="width: 360px;">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="q" value="{{ request('q','') }}" class="form-control" placeholder="Search">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>Search</button>
            <a href="{{ route('resolved.index', ['per_page' => $perPage]) }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>Reset</a>
          </div>
        </form>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Ref. No.</th>
              <th>Customer</th>
              <th>Account Manager</th>
              <th>Link</th>
              <th>Assigned To</th>
              <th>Logged By</th>
              <th>Cleared At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($faults as $fault)
            <tr>
              <td data-label="Ref. No.">{{ $fault->fault_ref_number }}</td>
              <td data-label="Customer">{{ $fault->customer }}</td>
              <td data-label="Account Manager">{{ $fault->accountManager }}</td>
              <td data-label="Link">{{ $fault->link }}</td>
              <td class="{{ $fault->assignedTo ? 'fw-bold' : 'text-muted' }}" data-label="Assigned To">{{ $fault->assignedTo ?: 'Not assigned' }}</td>
              <td class="text-muted" data-label="Logged By">{{ $fault->reportedBy }}</td>
              <td data-label="Cleared At">{{ \Carbon\Carbon::parse($fault->updated_at)->format('j F Y h:i a') }}</td>
              <td data-label="Action">
                <div class="workflow-actions">
                    <button  class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#showFaultModal-{{ $fault->id }}">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resolvedRevoke-{{ $fault->id }}">
                      <i class="fas fa-undo me-1"></i> Revoke
                    </button>
                </div>
              </td>
            </tr>
            @endforeach
            @if ($faults->isEmpty())
              <tr>
                <td colspan="8" class="text-center text-muted">No cleared faults in the last 24 hours</td>
              </tr>
            @endif
          </tbody>
        </table>

        @foreach ($faults as $fault)
        <div class="modal custom-modal fade" id="resolvedRevoke-{{ $fault->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <div class="fault-modal-header-copy">
                  <h5 class="modal-title">Revoke Fault</h5>
                  <div class="text-muted small mt-1">Move {{ $fault->fault_ref_number }} back to the NOC workflow with a reason.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" action="{{ route('resolved.revoke', $fault->id) }}">
                @csrf
                <div class="modal-body">
                  <div class="fault-modal-note mb-3">
                    <i class="fas fa-triangle-exclamation"></i>
                    <div>This action returns the fault to the previous stage, so include a clear operational reason for the reversal.</div>
                  </div>
                  <div class="mb-2">
                    <label class="form-label">Remark</label>
                    <textarea name="remark" class="form-control" rows="3" required></textarea>
                  </div>
                  <div class="fault-modal-attachment-missing"><i class="fas fa-rotate-left"></i><span>This will move the fault back to NOC.</span></div>
                </div>
                <div class="modal-footer fault-modal-footer">
                  <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel</button>
                  <button type="submit" class="btn btn-danger btn-sm rounded-pill">
                    <i class="fas fa-undo me-1"></i> Revoke</button>
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

        <div class="d-flex justify-content-between align-items-center mt-3 workflow-pagination">
          <small class="text-muted">
            Showing {{ $faults->firstItem() }} to {{ $faults->lastItem() }} of {{ $faults->total() }} results
          </small>
          {{ $faults->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
