@extends('layouts.admin')

@section('title')
Cleared
@endsection

@section('content')
<section class="content">
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
              <td>{{ $fault->fault_ref_number }}</td>
              <td>{{ $fault->customer }}</td>
              <td>{{ $fault->accountManager }}</td>
              <td>{{ $fault->link }}</td>
              <td class="{{ $fault->assignedTo ? 'fw-bold' : 'text-muted' }}">{{ $fault->assignedTo ?: 'Not assigned' }}</td>
              <td class="text-muted">{{ $fault->reportedBy }}</td>
              <td>{{ \Carbon\Carbon::parse($fault->updated_at)->format('j F Y h:i a') }}</td>
              <td>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resolvedRevoke-{{ $fault->id }}">
                  <i class="fas fa-undo me-1"></i> Revoke
                </button>
              </td>
            </tr>
            @endforeach
            @if ($faults->isEmpty())
              <tr>
                <td colspan="8" class="text-center text-muted">No NOC-cleared faults in the last 24 hours</td>
              </tr>
            @endif
          </tbody>
        </table>

        @foreach ($faults as $fault)
        <div class="modal fade" id="resolvedRevoke-{{ $fault->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Revoke Fault</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" action="{{ route('resolved.revoke', $fault->id) }}">
                @csrf
                <div class="modal-body">
                  <div class="mb-2">
                    <label class="form-label">Remark</label>
                    <textarea name="remark" class="form-control" rows="3" required></textarea>
                  </div>
                  <div class="alert alert-warning mb-0">This moves the fault to status 5.</div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Revoke</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center mt-3">
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