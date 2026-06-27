@can('account-manager-list')
@foreach($account_managers as $acc_manager)
<div class="modal custom-modal fade" id="accountManagerViewModal{{ $acc_manager->id }}" tabindex="-1" aria-labelledby="accountManagerViewModalLabel{{ $acc_manager->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="fault-modal-header-copy">
          <h5 class="modal-title" id="accountManagerViewModalLabel{{ $acc_manager->id }}"><i class="fas fa-eye me-2"></i>Account Manager Details</h5>
          <div class="text-muted small mt-1">Review the account manager profile and the customers currently assigned to this owner.</div>
          <div class="fault-modal-meta">
            <span class="fault-modal-meta-item"><i class="fas fa-user-tie"></i> {{ $acc_manager->name ?? '—' }}</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="fault-modal-note mb-3">
          <i class="fas fa-circle-info"></i>
          <div>This summary helps confirm customer ownership before reassigning account managers.</div>
        </div>

        <div class="fault-modal-section mb-3">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-user-tie"></i></span>
            <div>
              <div class="fault-modal-section-title">Manager Overview</div>
              <div class="fault-modal-section-subtitle">Current ownership details for this account manager record.</div>
            </div>
          </div>
          <div class="fault-modal-section-body">
            <dl class="row mb-0">
              <dt class="col-sm-4">Account Manager</dt>
              <dd class="col-sm-8">{{ $acc_manager->name ?? '—' }}</dd>
            </dl>
          </div>
        </div>

        @php
          $customersForManager = DB::table('customers')
            ->where('account_manager_id','=',$acc_manager->id)
            ->orderBy('customer','asc')
            ->get(['id','customer','account_number']);
        @endphp
        <div class="fault-modal-section">
          <div class="fault-modal-section-header">
            <span class="fault-modal-section-icon"><i class="fas fa-users"></i></span>
            <div>
              <div class="fault-modal-section-title">Assigned Customers</div>
              <div class="fault-modal-section-subtitle">{{ $customersForManager->count() }} customer {{ Str::plural('record', $customersForManager->count()) }} assigned to this manager.</div>
            </div>
          </div>
          <div class="fault-modal-section-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th style="width:60px;">#</th>
                    <th>Customer</th>
                    <th>Account Number</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($customersForManager as $c)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $c->customer }}</td>
                      <td>{{ $c->account_number }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-muted">No customers associated.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan
