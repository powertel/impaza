@can('account-manager-list')
@foreach($account_managers as $acc_manager)
<div class="modal fade" id="accountManagerViewModal{{ $acc_manager->id }}" tabindex="-1" aria-labelledby="accountManagerViewModalLabel{{ $acc_manager->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accountManagerViewModalLabel{{ $acc_manager->id }}">Account Manager Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-3">
          <dt class="col-sm-4">Account Manager</dt>
          <dd class="col-sm-8">{{ $acc_manager->name ?? '—' }}</dd>
        </dl>

        @php
          $customersForManager = DB::table('customers')
            ->where('account_manager_id','=',$acc_manager->id)
            ->orderBy('customer','asc')
            ->get(['id','customer','account_number']);
        @endphp
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead class="table-light">
              <tr>
                <th style="width:60px;">#</th>
                <th>Customer</th>
                <th>Account Number</th>
              </tr>
            </thead>
            <tbody>
              @forelse($customersForManager as $c)
                <tr>
                  <td>{{ $c->id }}</td>
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endcan